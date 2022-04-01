<?php declare(strict_types=1);

const DS = DIRECTORY_SEPARATOR;
const PS = '.';

const EASY_ROOT = __DIR__ . DS;

const EASY_CONFIG = EASY_ROOT . 'config' . DS;

define('EASY_LIB', file_exists(EASY_ROOT . '..' . DS . '..' . DS . '..' . DS . 'vendor' . DS . 'geste' . DS . 'easy-command'));

if (EASY_LIB) {
    if (!defined('ROOT')) {
        define('ROOT', realpath(EASY_ROOT . '..' . DS . '..' . DS . '..') . DS);
    }
    if (!defined('CONFIG')) {
        define('CONFIG', ROOT . 'config' . DS);
    }
} else {
    define('ROOT', EASY_ROOT);
    define('CONFIG', EASY_CONFIG);
}
