<?php
namespace BackendBundle\Command;

use BackendBundle\Entity\CreditCard;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use BackendBundle\Event\ExpiringDocumentAlertEvent;
use BackendBundle\Event\MLSDuesAgentPaymentNotificationEvent;
use BackendBundle\Event\MLSDuesEmailAdminEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DistrictsMLSDuesCommand
 * @package Hero\BackendBundle\Command
 */
class DistrictsMLSDuesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bill:mls:dues')
            ->setDescription('Bill MLS dues')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $agents = $container->get('backend.agent_manager')->getAgentsWithMLSDuesRequiredDistricts();

        $convrgeApi = $container->get('admin.converge_api');
        $transactionManager = $container->get('backend.transaction_manager');
        $variableMLSDuesTransactions = [];
        $fixedMLSDuesTransactions = [];

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $progress = new ProgressBar($output, count($agents));
        $progress->start();

        foreach ($agents as $agent) {
            /** @var CreditCard $paymentMethod */
            $paymentMethod = $agent->getPaymentMethods()->first();
            if ($paymentMethod) {
                foreach ($agent->getAgentDistricts() as $agentDistrict) {
                    $district = $agentDistrict->getDistrict();
                    if (!$district->isMLSDuesRequired() || null === $agent->getTerminalPin()) {
                        continue;
                    }

                    $transaction = new Transaction();
                    $transaction->setStatus(Transaction::STATUS_PENDING);
                    $transaction->setPaymentMethod($paymentMethod);
                    $transaction->setUser($agent);
                    $transaction->setDistrict($district);

                    if ($district->getMLSDuesType() === District::MLS_DUES_TYPE_VARIABLE) {
                        $transaction->setNotes(sprintf('Pending transaction for district: %s ', $district->getName()));
                        $transaction->setAmount(0);
                        $variableMLSDuesTransactions[] = $transaction;
                    } else {
                        $response = $convrgeApi->request('ccsale', $agent->getTerminalPin(), [
                            'ssl_token' => $paymentMethod->getCardToken(),
                            'ssl_amount' => $district->getMLSFee(),
                        ]);

                        $transaction->setAmount($district->getMLSFee());
                        $transaction->setNotes(sprintf('MLS dues for %s', $district->getName()));

                        if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
                            $transaction->setStatus(Transaction::STATUS_APPROVED);
                            $transaction->setVendorId($response['ssl_txn_id']);
                        } elseif (array_key_exists('errorMessage', $response)) {
                            $transaction->setNotes(sprintf('Error: %s. MLS dues for %s', $response['errorMessage'], $district->getName()));
                        } else {
                            $transaction->setNotes(sprintf('Error: %s. MLS dues for %s', 'Unknown error', $district->getName()));
                        }

                        $dispatcher->dispatch('backend_mls_dues_agent_payment_notification', new MLSDuesAgentPaymentNotificationEvent($transaction));
                        $fixedMLSDuesTransactions[] = $transaction;
                    }

                    $transactionManager->save($transaction);
                }
            }

            $progress->advance();
        }

        $dispatcher->dispatch('backend_mls_dues_email_agent', new MLSDuesEmailAdminEvent($fixedMLSDuesTransactions, $variableMLSDuesTransactions));
        $progress->finish();
    }
}
