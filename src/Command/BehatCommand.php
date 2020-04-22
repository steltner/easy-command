<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputOption;
use function array_flip;
use function strtolower;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use const DS;

class BehatCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('behat')
            ->setDescription('Start behat tests')
            ->setHelp('Start behat tests')
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getOption('project');

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

        $this->executeCommand(
            ['php', $projectPath . 'vendor' . DS . 'behat' . DS . 'behat' . DS . 'bin' . DS . 'behat', '--config=' . $file]
        );

        return 0;
    }
}
