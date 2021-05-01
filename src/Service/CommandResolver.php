<?php declare(strict_types=1);

namespace Easy\Service;

use Easy\Command\AbstractCommand;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function array_map;

class CommandResolver
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function resolveList(array $items): array
    {
        return array_map([$this, 'resolve'], $items);
    }

    /**
     * @param string $item
     * @return AbstractCommand
     * @throws InvalidArgumentException
     */
    public function resolve(string $item): AbstractCommand
    {
        if (!$this->container->has($item)) {
            throw new InvalidArgumentException();
        }

        return $this->container->get($item);
    }
}
