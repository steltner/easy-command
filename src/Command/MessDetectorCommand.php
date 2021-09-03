<?php declare(strict_types=1);

namespace Easy\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MessDetectorCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('mess-detector')
            ->setDescription('Run php mess detector')
            ->setHelp('Run php mess detector')
            ->setAliases(['md', 'phpmd'])
            ->addOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Project name or short tag');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');

        $project = $this->getProjectByName($project);

        $projectPath = $this->getFilePath($project['path']);

        $target = $project['phpmd']['target'] ?? 'src';
        $format = $project['phpmd']['format'] ?? 'text';
        $ruleset = $project['phpmd']['ruleset'] ?? 'cleancode,codesize,controversial,design,naming,unusedcode';

        $this->executeCommand(
            [
                'php',
                $projectPath . 'vendor' . DS . 'phpmd' . DS . 'phpmd' . DS . 'src' . DS . 'bin' . DS . 'phpmd',
                $target,
                $format,
                $ruleset,
            ]
        );

        $output->writeln('- Mess detector done -');

        return 0;
    }
}
