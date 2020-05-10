<?php declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$providers = [
    Easy\ConfigProvider::class,
    new PhpFileProvider(CONFIG . 'database.php.dist'),
    new PhpFileProvider(CONFIG . 'database.php'),
    new PhpFileProvider(CONFIG . 'projects.php.dist'),
    new PhpFileProvider(CONFIG . 'projects.php'),
];

return (new ConfigAggregator($providers))->getMergedConfig();
