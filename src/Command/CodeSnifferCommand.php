<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use const DS;

class CodeSnifferCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('code-sniffer')
            ->setDescription('Run php code sniffer')
            ->setHelp('Run php code sniffer')
            ->setAliases(['cs', 'phpcs'])
            ->addOption('sniffs', 's', InputOption::VALUE_NONE, 'Show sniff rules')
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');
        $sniffs = $input->getOption('sniffs');

        $project = $this->getProjectByName($project);

        $projectPath = $this->getFilePath($project['path']);

        $command = ['php', $projectPath . 'vendor' . DS . 'squizlabs' . DS . 'php_codesniffer' . DS . 'bin' . DS . 'phpcs'];

        if ($sniffs) {
            $command[] = '-s';
        }

        $this->executeCommand($command);

        return 0;
    }
}
