<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use const DS;

class PhpUnitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Start phpunit tests')
            ->setHelp('Start phpunit tests')
            ->setAliases(['phpunit', 'unit'])
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        $filePath = $project['phpunit'] ?? 'phpunit.xml';

        if ($this->distFileExists($project['path'], $filePath)) {
            $file = $this->getDistFilePath($project['path'], $filePath);
        } elseif ($this->fileExists($project['path'], $filePath)) {
            $file = $this->getFilePath($project['path'], $filePath);
        } else {
            $output->writeln('No phpunit configuration');

            return 1;
        }

        $projectPath = $this->getFilePath($project['path']);

        $this->executeCommand(
            ['php', $projectPath . 'vendor' . DS . 'phpunit' . DS . 'phpunit' . DS . 'phpunit', '-c', $file]
        );

        return 0;
    }
}
