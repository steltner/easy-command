<?php declare(strict_types=1);

namespace Easy\Service;

use Easy\Command\AbstractCommand;
use InvalidArgumentException;

use function array_map;
use function is_callable;

class CommandResolver
{
    public function resolveList(array $items): array
    {
        return array_map([$this, 'resolve'], $items);
    }

    /**
     * @param string|callable $item
     * @return callable
     * @throws InvalidArgumentException
     */
    public function resolve($item): AbstractCommand
    {
        if (is_callable($item)) {
            return $item;
        } elseif (class_exists($item)) {
            return new $item();
        } else {
            throw new InvalidArgumentException();
        }
    }
}
