<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\CreditCardType;
use AdminBundle\Utils\Billing;
use AdminBundle\Utils\ConvergeApi;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\CreditCard;
use BackendBundle\Manager\CreditCardManager;
use BackendBundle\Manager\PaymentMethodManager;
use BackendBundle\Manager\TransactionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentPaymentMethodController
 * @Security("has_role('ROLE_AGENT')")
 */
class AgentPaymentMethodController extends Controller
{
    /**
     * @var PaymentMethodManager
     */
    private $paymentMethodManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var CreditCardManager
     */
    private $creditCardManager;

    /**
     * @var ConvergeApi
     */
    private $convergeApi;

    /**
     * @var Billing
     */
    private $billing;

    /**
     * PaymentMethodController constructor.
     * @param PaymentMethodManager $paymentMethodManager
     * @param TransactionManager   $transactionManager
     * @param CreditCardManager    $creditCardManager
     * @param ConvergeApi          $convergeApi
     * @param Billing              $billing
     */
    public function __construct(
        PaymentMethodManager $paymentMethodManager,
        TransactionManager $transactionManager,
        CreditCardManager $creditCardManager,
        ConvergeApi $convergeApi,
        Billing $billing
    ) {
        $this->paymentMethodManager = $paymentMethodManager;
        $this->transactionManager = $transactionManager;
        $this->creditCardManager = $creditCardManager;
        $this->convergeApi = $convergeApi;
        $this->billing = $billing;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function manageAction(Request $request)
    {
        /** @var Agent $agent */
        $agent = $this->getUser();
        $creditCard = $this->creditCardManager->findOneBy(['user' => $agent]);
        if (null === $terminalPin = $agent->getTerminalPin()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.agent_no_terminal_pin');
            return $this->redirectToRoute('admin_dashboard');
        }

        if (null === $merchantId = $agent->getMerchantId()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.agent_no_merchant_id');
            return $this->redirectToRoute('admin_dashboard');
        }

        $creditCardForm = $this->createForm(CreditCardType::class, $creditCard);
        $creditCardForm->handleRequest($request);
        if ($creditCardForm->isSubmitted() && $creditCardForm->isValid()) {
            /** @var CreditCard $creditCard */
            $creditCard = $creditCardForm->getData();
            $creditCard->setUser($agent);

            if ($oldToken = $creditCard->getCardToken()) {
                $this->convergeApi->request('ccdeletetoken', $terminalPin, $merchantId, [
                    'ssl_token' => $oldToken,
                ]);
            }

            $response = $this->convergeApi->request('ccgettoken', $terminalPin, $merchantId, [
                'ssl_card_number' => $creditCard->getCardNumber(),
                'ssl_exp_date' => $creditCard->getExpMonth().$creditCard->getExpYear(),
                'ssl_cvv2cvc2' => $creditCard->getCvv(),
                'ssl_first_name' => $creditCard->getFirstName(),
                'ssl_last_name' => $creditCard->getLastName(),
                'ssl_email' => $agent->getEmail(),
                'ssl_add_token' => 'Y',
            ]);

            if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
                $creditCard->setLastFour(substr($creditCard->getCardNumber(), -4));
                $creditCard->setCardToken($response['ssl_token']);
                $this->creditCardManager->save($creditCard);
                $this->get('session')->getFlashBag()->add('success', 'success.credit_card.manage');

                /* Bill Subscription if it is late. */
                $dateInterval = $agent->getSubscription()->getDueDate()->diff(new \DateTime('now'));
                if ($dateInterval->invert === 0 && $dateInterval->d > 5) {
                    if ($this->billing->billSubscription($agent->getSubscription())) {
                        $this->get('session')->getFlashBag()->add('success', 'success.subscription.billed');
                    }
                }

                $transactions = $this->transactionManager->getFixedMLSDuesPendingTransactions($agent);
                foreach ($transactions as $transaction) {
                    $this->billing->billMLSDuesFixedPendingTransaction($creditCard, $transaction);
                }

                return $this->redirectToRoute('admin_agent_profile_payment_method_manage');
            } elseif (array_key_exists('errorMessage', $response)) {
                $this->get('session')->getFlashBag()->add('danger', $response['errorMessage']);
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'error.credit_card.none');
            }
        } elseif (null !== $creditCard) {
            $this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('success.credit_card.on_file', ['%lastFour%' => $creditCard->getLastFour()]));
        }

        return $this->render('@Admin/AgentPaymentMethod/manage.html.twig', [
            'creditCard' => $creditCard,
            'form' => $creditCardForm->createView(),
        ]);
    }
}
