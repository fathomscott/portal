<?php
namespace BackendBundle\Command;

use Hero\BackendBundle\DataMigration\AgentLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class AgentLoadCommand
 * @package Hero\BackendBundle\Command
 */
class AgentLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('migration:agent-load')
            ->setDescription('Loads agents from a CSV into the database.')
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
        $loader = $this->getContainer()->get('backend.agent_loader');
        $loader->setOutput($output);
        $files = Finder::create()
            ->ignoreVCS(true)
            ->files()
            ->sortByName()
            ->in($this->getContainer()->getParameter('agent_files_data_dir'));
        foreach ($files as $file) {
            $output->writeln("Loading $file...");
            try {
                $loader->loadFile($file);
            } catch (\Exception $ex) {
                $output->writeln("Failed to load $file");
                $output->writeln($ex->getTraceAsString());
                throw $ex;
            }
        }
    }
}
