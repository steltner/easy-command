<?php declare(strict_types=1);

namespace Easy\Command;

use Easy\Application;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

use function array_flip;
use function file_exists;
use function strtolower;

use const DS;
use const PS;

abstract class AbstractCommand extends Command
{
    public function getApplication(): Application
    {
        return parent::getApplication();
    }

    public function getContainer(): ContainerInterface
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    protected function executeCommand($command): void
    {
        $process = new Process($command);
        $process->setTimeout(null);
        $process->start();

        foreach ($process as $type => $data) {
            echo $data;
        }
    }

    protected function getDistFilePath(string $project, string $file): string
    {
        return $this->getFilePath($project, $file . PS . 'dist');
    }

    protected function getFilePath(string $project, string $file = ''): string
    {
        if ($this->isAbsolutePath($project)) {
            return $project . DS . $file;
        } else {
            return realpath(ROOT . '..' . DS . $project) . DS . $file;
        }
    }

    protected function distFileExists(string $project, string $file): bool
    {
        return $this->fileExists($project, $file . PS . 'dist');
    }

    protected function fileExists(string $project, string $file): bool
    {
        if ($this->isAbsolutePath($project)) {
            return file_exists($project . DS . $file);
        } else {
            return file_exists(ROOT . '..' . DS . $project . DS . $file);
        }
    }

    private function isAbsolutePath(string $path): bool
    {
        return $path[0] === DS || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0;
    }

    protected function getProjectByName(?string $name): ?array
    {
        $config = $this->get('config');

        $projects = $config['projects'];

        if (isset($name)) {
            foreach ($projects as $project) {
                if (isset(array_flip($project['names'])[strtolower($name)])) {
                    return $project;
                }
            }

            return null;
        } else {
            return current($projects);
        }
    }
}
