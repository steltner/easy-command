<?php declare(strict_types=1);

namespace Easy\Command;

use Exception;
use InvalidArgumentException;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function current;
use function file;
use function glob;
use function pathinfo;
use function sort;
use function substr;
use function time;
use function trim;

use const DS;
use const PATHINFO_BASENAME;
use const PHP_EOL;
use const PS;
use const ROOT;

class DatabaseCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('database')
            ->setDescription('Install, update, reset database')
            ->setHelp('Install, update, reset database')
            ->setAliases(['db'])
            ->addArgument('action', InputArgument::REQUIRED, 'What do you wanna do? (install, update, reset, delete)')
            ->addArgument('database', InputArgument::OPTIONAL, 'Which database? (Do not work with multiple hosts!)')
            ->addOption('live', null, InputOption::VALUE_NONE, 'Use live data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');

        $database = $input->getArgument('database') ?? current($this->getContainer()->get('config')['database'])['name'];

        $dev = $input->getOption('live') === false;

        switch ($action) {
            case 'remove':
            case 'drop':
            case 'delete':
                $this->delete($database, $output);
                break;
            // first remove existing
            case 'reset':
                $this->delete($database, $output);
            // then install new
            case 'install':
                $this->install($database, $output, $dev);
            // and update
            case 'update':
                $this->migrate($database, $output);
                break;
            default:
                throw new InvalidArgumentException('Not a possible action');
        }

        return 0;
    }

    private function delete(string $database, OutputInterface $output): void
    {
        $pdo = $this->getContainer()->get('pdo');

        $output->writeln('DROP database `' . $database . '`');

        $pdo->exec('DROP DATABASE IF EXISTS `' . $database . '`');
    }

    private function install(string $database, OutputInterface $output, bool $dev = true): void
    {
        $pdo = $this->getContainer()->get('pdo');

        $filename = $this->getPathForDatabase($database) . 'structure' . DS . 'structure' . PS . 'sql';

        $output->writeln('Insert database structure for ' . $database);
        $this->insertSqlFile($output, $pdo, $filename);

        $output->writeln('Insert database data for ' . $database);
        $this->insertData($database, $output, $dev);
    }

    private function migrate(string $database, OutputInterface $output): void
    {
        $output->writeln('Start migrations');

        /** @var \PDO $pdo */
        $pdo = $this->get('pdo');

        $path = $this->getPathForDatabase($database) . 'migration' . DS;

        $migrations = [];

        foreach (glob($path . '*.sql') as $filename) {
            $migrations[] = pathinfo($filename, PATHINFO_BASENAME);
        }

        $pdo->exec('use ' . $database);

        $pdoStatement = $pdo->prepare('SELECT * FROM Migration ORDER BY name');
        $pdoStatement->execute();
        $result = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        $databaseMigrations = [];

        foreach ($result as $item) {
            $databaseMigrations[$item['name']] = true;
        }

        sort($migrations);

        $output->writeln('Migrating...');

        foreach ($migrations as $file) {
            if (!isset($databaseMigrations[$file])) {
                $output->write('Insert file ' . $file);

                $this->insertSqlFile($output, $pdo, $path . $file);

                $statement = $pdo->prepare("INSERT INTO Migration (name, executionTime) VALUES (?, ?)");

                $result = $statement->execute([$file, time()]);

                $output->writeln(' and adding migration to database, result: ' . ($result === true ? 'success' : 'failure'));
            }
        }
    }

    private function insertData(string $database, OutputInterface $output, bool $dev = true): void
    {
        /** @var PDO $pdo */
        $pdo = $this->get('pdo');

        $system = $dev ? 'dev' : 'live';

        $filename = $this->getPathForDatabase($database) . 'data' . DS . $system . PS . 'sql';

        $this->insertSqlFile($output, $pdo, $filename);
    }

    private function insertSqlFile(OutputInterface $output, PDO $pdo, string $filename): void
    {
        $temp = '';
        $lines = file($filename);

        foreach ($lines as $line) {
            // Skip it if it is a comment
            if (substr($line, 0, 2) === '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $temp .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                try {
                    $pdo->exec($temp);
                    $temp = '';
                } catch (Exception $e) {
                    $output->writeln(PHP_EOL . 'MySQLError ' . $e->getMessage());
                    exit(1);
                }
            }
        }
    }

    private function getPathForDatabase(string $database): string
    {
        return ROOT . 'database' . DS . $database . DS;
    }
}
