<?php declare(strict_types=1);

namespace Easy\Command;

use Exception;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function str_replace;
use const DS;
use const PS;

class PhpUnitCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('test')
            ->setDescription('Start phpunit tests')
            ->setHelp('Start phpunit tests')
            ->setAliases(['phpunit', 'unit'])
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        $filePath = $project['phpunit'] ?? 'phpunit.xml';

        try {
            if (isset($project['docker'])) {
                $command = $this->getDockerCommand($project['path'], $filePath, $project['docker']);
            } else {
                $command = $this->getLocalCommand($project['path'], $filePath);
            }

            $this->executeCommand($command);

            return 0;
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return 1;
        }
    }

    private function getDockerCommand(string $path, string $filePath, array $docker): array
    {
        $projectPath = $docker['path'];

        if ($this->fileExists($path, $filePath)) {
            $file = str_replace('//', '/', $projectPath . DS . $filePath);
        } elseif ($this->distFileExists($path, $filePath)) {
            $file = str_replace('//', '/', $projectPath . DS . $filePath . PS . 'dist');
        } else {
            throw new Exception('No valid phpunit configuration');
        }

        return [
            'docker', 'exec', '-i', $docker['container'], 'bash', '-c',
            '(cd ' . $projectPath . ' && ' . 'php', 'vendor' . DS . 'phpunit' . DS . 'phpunit' . DS . 'phpunit', '-c', $file . ')'
        ];
    }

    private function getLocalCommand(string $path, string $filePath): array
    {
        if ($this->fileExists($path, $filePath)) {
            $file = $this->getFilePath($path, $filePath);
        } elseif ($this->distFileExists($path, $filePath)) {
            $file = $this->getDistFilePath($path, $filePath);
        } else {
            throw new Exception('No valid phpunit configuration');
        }

        $projectPath = $this->getFilePath($path);

        return ['php', $projectPath . 'vendor' . DS . 'phpunit' . DS . 'phpunit' . DS . 'phpunit', '-c', $file];
    }
}
