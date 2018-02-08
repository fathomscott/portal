<?php
namespace BackendBundle\Command;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\CreditCard;
use BackendBundle\Entity\Plan;
use BackendBundle\Entity\Referral;
use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\Transaction;
use BackendBundle\Event\BillSubscriptionEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BillSubscriptionsCommand
 * @package Hero\BackendBundle\Command
 */
class BillSubscriptionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bill:subscription')
            ->setDescription('Bill subscription')
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

        $subscriptionManager = $container->get('backend.subscription_manager');

        $startDate = new \DateTime('-5 days');
        $endDate = new \DateTime('today');

        $billing = $this->getContainer()->get('admin.billing');

        $subscriptions = $subscriptionManager->findRenewableSubscriptions($startDate, $endDate);

        $progress = new ProgressBar($output, count($subscriptions));
        $progress->start();

        foreach ($subscriptions as $subscription) {
            $billing->billSubscription($subscription);
            $progress->advance();
        }

        // send message to super admin with list of billed subscription
        $progress->finish();
    }
}
