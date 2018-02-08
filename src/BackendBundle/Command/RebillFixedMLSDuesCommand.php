<?php
namespace BackendBundle\Command;

use BackendBundle\Entity\Agent;
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
class RebillFixedMLSDuesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rebill:mls:dues')
            ->setDescription('Rebill Fixed MLS dues.')
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

        $billing = $container->get('admin.billing');
        $transactionManager = $container->get('backend.transaction_manager');

        $transactions = $transactionManager->getFixedMLSDuesPendingTransactions();

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $progress = new ProgressBar($output, count($transactions));
        $progress->start();

        foreach ($transactions as $transaction) { /** @var $transaction Transaction */
            /** @var Agent $agent */
            $agent = $transaction->getUser();
            /** @var CreditCard $paymentMethod */
            $paymentMethod = $agent->getPaymentMethods()->first();
            if ($paymentMethod) {
                $billing->billMLSDuesFixedPendingTransaction($paymentMethod, $transaction);
            }

            $progress->advance();
        }

        $dispatcher->dispatch('backend_mls_dues_email_agent', new MLSDuesEmailAdminEvent($transactions, []));
        $progress->finish();
    }
}
