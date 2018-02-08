<?php
namespace AdminBundle\Utils;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\CreditCard;
use BackendBundle\Entity\PaymentMethod;
use BackendBundle\Entity\Plan;
use BackendBundle\Entity\Referral;
use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\SuperAdmin;
use BackendBundle\Entity\Transaction;
use BackendBundle\Event\BillSubscriptionEvent;
use BackendBundle\Event\MLSDuesAgentPaymentNotificationEvent;
use BackendBundle\Manager\SubscriptionManager;
use BackendBundle\Manager\TransactionManager;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class Billing
 */
class Billing
{
    /**
     * @var ConvergeApi
     */
    private $convergeApi;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var SubscriptionManager
     */
    private $subscriptionManager;

    /**
     * Billing constructor.
     * @param ConvergeApi                       $convergeApi
     * @param EventDispatcherInterface          $dispatcher
     * @param TransactionManager                $transactionManager
     * @param SubscriptionManager               $subscriptionManager
     */
    public function __construct(ConvergeApi $convergeApi, EventDispatcherInterface $dispatcher, TransactionManager $transactionManager,
				SubscriptionManager $subscriptionManager)
    {
        $this->convergeApi = $convergeApi;
        $this->dispatcher = $dispatcher;
        $this->transactionManager = $transactionManager;
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * @param Subscription $subscription
     * @return boolean
     */
    public function billSubscription(Subscription $subscription)
    {
        /** @var Agent $agent */
        $agent = $subscription->getUser();

        /** @var CreditCard $paymentMethod */
        $paymentMethod = $agent->getPaymentMethods()->first();

        $amount = $this->calculateSubscriptionAmount($subscription->getPlan());

        $referralDiscount = $this->subscriptionManager->countReferralDiscount($subscription);
        if ($referralDiscount >= $amount) {
            $referralDiscount = $amount;
            $amount = 0;
        } else {
            $amount -= $referralDiscount;
        }

        $terminalPin = $agent->getTerminalPin();
	$merchantId = $agent->getMerchantId();
	$result = false;

	if ($amount > 0) {
		echo "Agent: ".$agent->getFirstName().' '.$agent->getLastName().'('.$agent->getId().'): '.$amount.
			($paymentMethod ? "BILLED" : "");
	}

        if ($paymentMethod && null !== $terminalPin) {
            $subscription->setLastAttempt(new \DateTime('now'));
            if ($amount > 0) {
		echo "(calling Converge API)";
                $response = $this->convergeApi->request('ccsale', $terminalPin, $merchantId, [
                    'ssl_token' => $paymentMethod->getCardToken(),
                    'ssl_amount' => $amount
		    //'ssl_description' => ?
                ]);

                $transaction = new Transaction();
                $transaction->setStatus(Transaction::STATUS_PENDING);
                $transaction->setAmount($amount);
                $transaction->setUser($agent);
                $transaction->setPaymentMethod($paymentMethod);

                if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
                    $result = true;
                    $transaction->setStatus(Transaction::STATUS_APPROVED);
                    $transaction->setNotes('Successfully billed subscription.');
                    $transaction->setVendorId($response['ssl_txn_id']);

                    $subscription->setDueDate(new \DateTime(date('Y-m-d', strtotime('first day of next month'))));

                  $this->dispatcher->dispatch('backend_bill_subscription_agent_notification', new BillSubscriptionEvent($subscription, $amount, $referralDiscount));
                } elseif (array_key_exists('errorMessage', $response)) {
                    $result = false;
                    $transaction->setStatus(Transaction::STATUS_DECLINED);
                    $transaction->setNotes($response['errorMessage']);
                } else {
                    $result = false;
                    $transaction->setStatus(Transaction::STATUS_DECLINED);
                    $transaction->setNotes('Unknown error.');
                }

                $this->transactionManager->save($transaction);
            }
	    else {
                $result = true;
                $subscription->setDueDate(new \DateTime(date('Y-m-d', strtotime('first day of next month'))));
                //$this->dispatcher->dispatch('backend_bill_subscription_agent_notification', new BillSubscriptionEvent($subscription, $amount, $referralDiscount));
            }

            $this->subscriptionManager->save($subscription);

        }
	if ($amount > 0) echo "\n";

        return $result;
    }

    /**
     * @param CreditCard  $paymentMethod
     * @param Transaction $transaction
     */
    public function billMLSDuesFixedPendingTransaction(CreditCard $paymentMethod, Transaction $transaction)
    {
        /** @var Agent $agent */
        $agent = $transaction->getUser();
        if (null === $terminalPin = $agent->getTerminalPin()) {
            return;
        }

        $district = $transaction->getDistrict();
        $response = $this->convergeApi->request('ccsale', $terminalPin, [
            'ssl_token' => $paymentMethod->getCardToken(),
            'ssl_amount' => $district->getMLSFee(),
        ]);

        if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
            $transaction->setNotes(sprintf('Successfully re billed at %s. Transaction for Fixed MLS dues.', date('Y-m-d H:i')));
            $transaction->setStatus(Transaction::STATUS_APPROVED);
            $transaction->setVendorId($response['ssl_txn_id']);
        } elseif (array_key_exists('errorMessage', $response)) {
            $transaction->setNotes(sprintf('Error: %s. MLS dues for %s', $response['errorMessage'], $district->getName()));
        } else {
            $transaction->setNotes(sprintf('Error: %s. MLS dues for %s', 'Unknown error', $district->getName()));
        }

        $this->dispatcher->dispatch('backend_mls_dues_agent_payment_notification', new MLSDuesAgentPaymentNotificationEvent($transaction));

        $this->transactionManager->save($transaction);
    }


    /**
     * @param Plan $plan
     * @return float|string
     */
    public function calculateSubscriptionAmount(Plan $plan)
    {
        $dStart = new \DateTime(date('Y-m-d'));
        // first day of next month
        $dEnd  = new \DateTime(date('Y-m-d', strtotime('first day of next month')));
        $monthlyPrice = $plan->getMonthlyPrice();

        // it's today or less. just bill the total
        if ($dStart >= $dEnd) {
            $amount = $monthlyPrice;
        } else {
            // calculate amount for the remaining days
            $dDiff = $dStart->diff($dEnd);
            $amount = round(($monthlyPrice / 30) * $dDiff->days, 2);
        }

        if ($amount > $monthlyPrice) {
            $amount = $monthlyPrice;
        }

	// Scott
	$amount = $monthlyPrice;

        return $amount;
    }
}
