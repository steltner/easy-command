<?php declare(strict_types=1);

namespace Easy;

use PDO;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases' => [
                    PDO::class => 'pdo',
                ],
                'invokables' => [
                ],
                'factories' => [
                    'database' => Service\DatabaseFactory::class,
                    'pdo' => Service\PdoFactory::class,
                    'query' => Service\QueryFactory::class,
                ],
            ],
        ];
    }
}
