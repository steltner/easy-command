<?php declare(strict_types=1);

namespace Easy\Command;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function strtolower;

class DockerCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('docker')
            ->setDescription('Start docker commands, default up')
            ->setHelp('Setup the project by creating directories')
            ->addArgument('action', InputArgument::OPTIONAL, 'What do you wanna do? (up, down, pull)', 'up')
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Set project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');

        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        if (!isset($project)) {
            $output->writeln('Could not find project');

            return 1;
        }

        $dockerConfig = $project['docker']['config'] ?? 'docker-compose.yml';

        switch (strtolower($action)) {
            case 'up':
                $this->executeDockerCommand($dockerConfig, 'up');

                break;
            case 'down':
                $this->executeDockerCommand($dockerConfig, 'down');

                break;
            case 'pull':
                $this->executeDockerCommand($dockerConfig, 'pull');

                break;
            case 'restart':
                $this->executeDockerCommand($dockerConfig, 'down');
                $this->executeDockerCommand($dockerConfig, 'up');

                break;
            default:
                throw new InvalidArgumentException('Not a possible action');
        }

        return 0;
    }

    protected function executeDockerCommand(string $dockerConfig, string $command): void
    {
        $this->executeCommand(['docker-compose', '-f', $dockerConfig, $command]);
    }
}
