<?php declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$providers = [
    Easy\ConfigProvider::class,
    new PhpFileProvider(EASY_CONFIG . 'commands.php.dist'),
    new PhpFileProvider(EASY_CONFIG . 'commands.php'),
];

if (EASY_LIB) {
    $providers[] = new PhpFileProvider(CONFIG . 'commands.php.dist');
    $providers[] = new PhpFileProvider(CONFIG . 'commands.php');
}

$providers[] = new PhpFileProvider(CONFIG . 'database.php.dist');
$providers[] = new PhpFileProvider(CONFIG . 'database.php');
$providers[] = new PhpFileProvider(CONFIG . 'projects.php.dist');
$providers[] = new PhpFileProvider(CONFIG . 'projects.php');

return (new ConfigAggregator($providers))->getMergedConfig();
