<?php declare(strict_types=1);

namespace Easy;

use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use PDO;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
                Command\BehatCommand::class,
                Command\CodeSnifferCommand::class,
                Command\DatabaseCommand::class,
                Command\DockerCommand::class,
                Command\MessDetectorCommand::class,
                Command\PhpUnitCommand::class,
                Command\WebserverCommand::class,
            ],
            'dependencies' => [
                'aliases' => [
                    PDO::class => 'pdo',
                ],
                'invokables' => [
                    Service\CommandResolver::class,
                ],
                'factories' => [
                    'database' => Service\DatabaseFactory::class,
                    'pdo' => Service\PdoFactory::class,
                    'query' => Service\QueryFactory::class,

                    Service\CommandListService::class => ConfigAbstractFactory::class,
                ],
            ],
            ConfigAbstractFactory::class => [
                Service\CommandListService::class => [
                    'config',
                    Service\CommandResolver::class,
                ],
            ]
        ];
    }
}
