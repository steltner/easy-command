<?php declare(strict_types=1);

namespace Easy\Service;

use PDO;
use PDOException;
use Psr\Container\ContainerInterface;

use function current;

class DatabaseFactory
{
    public function __invoke(ContainerInterface $container): PDO
    {
        $settings = $container->get('config');

        $debug = $settings['debug'];

        $settings = $settings['database'];
        $settings = current($settings);

        $dsn = 'mysql:dbname=' . $settings['name'] . ';host=' . $settings['host'] . ';charset=utf8';
        $user = $settings['user'];
        $password = $settings['password'];

        try {
            $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => $settings['error'], PDO::ATTR_EMULATE_PREPARES => false]);
        } catch (PDOException $e) {
            if (!$debug || $e->getCode() !== 1049) {
                throw $e;
            }

            $dsn = 'mysql:dbname=mysql;host=' . $settings['host'] . ';charset=utf8';

            $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => $settings['error'], PDO::ATTR_EMULATE_PREPARES => false]);
        }

        return $pdo;
    }
}
