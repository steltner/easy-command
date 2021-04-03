<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WebserverCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('webserver')
            ->setDescription('Run phps built in webserver')
            ->setHelp('Run phps built in webserver')
            ->setAliases(['serve'])
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        $projectPath = $this->getFilePath($project['path']);

        $path = $project['webserver']['path'] ?? 'public';
        $port = $project['webserver']['port'] ?? '8000';

        $this->executeCommand(
            [
                'php',
                '-S',
                'localhost:' . $port,
                '-t' . $projectPath . $path,
            ]
        );

        return 0;
    }
}
