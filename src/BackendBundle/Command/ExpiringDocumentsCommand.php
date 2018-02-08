<?php
namespace BackendBundle\Command;

use BackendBundle\Event\ExpiringDocumentAlertEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExpiringDocumentsCommand
 * @package Hero\BackendBundle\Command
 */
class ExpiringDocumentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('agent:expiring-documents')
            ->setDescription('Send emails to agents about expiring documents.')
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
        $documentManager = $this->getContainer()->get('backend.document_manager');
        $documentManager->setExpiredStatus();

        $agents = $this->getContainer()->get('backend.agent_manager')->getAgentsWithExpiringDocuments();

        $dispatcher = $this->getContainer()->get('event_dispatcher');
        $progress = new ProgressBar($output, count($agents));
        $progress->start();
        $onboardingEmail = $this->getContainer()->get('backend.system_default_manager')->getSystemDefault()->getOnboardingEmail();

        foreach ($agents as $agent) {
            $dispatcher->dispatch('backend_expiring_document_alert', new ExpiringDocumentAlertEvent($agent, $onboardingEmail));
            $progress->advance();
        }
        $progress->finish();
    }
}
