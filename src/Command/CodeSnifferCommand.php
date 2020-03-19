<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CodeSnifferCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('code-sniffer')
            ->setDescription('Run php code sniffer')
            ->setHelp('Run php code sniffer')
            ->setAliases(['cs', 'phpcs'])
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        $projectPath = $this->getFilePath($project['path']);

        $this->executeCommand(
            ['php', $projectPath . 'vendor' . DS . 'squizlabs' . DS . 'php_codesniffer' . DS . 'bin' . DS . 'phpcs']
        );

        return 0;
    }
}
