<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const DS;

class BehatCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('behat')
            ->setDescription('Start behat tests')
            ->setHelp('Start behat tests')
            ->addArgument('tags', InputArgument::OPTIONAL, 'Tags')
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');
        $tags = $input->getArgument('tags');

        $project = $this->getProjectByName($project);

        $filePath = $project['behat'] ?? 'behat.yml';

        if ($this->fileExists($project['path'], $filePath)) {
            $file = $this->getFilePath($project['path'], $filePath);
        } elseif ($this->distFileExists($project['path'], $filePath)) {
            $file = $this->getDistFilePath($project['path'], $filePath);
        } else {
            $output->writeln('No behat configuration');

            return 1;
        }

        $projectPath = $this->getFilePath($project['path']);

        $command = ['php', $projectPath . 'vendor' . DS . 'behat' . DS . 'behat' . DS . 'bin' . DS . 'behat', '--config=' . $file, '--colors'];

        if ($tags) {
            $command[] = '--tags=' . $tags;
        }

        $this->executeCommand($command);

        return 0;
    }
}
