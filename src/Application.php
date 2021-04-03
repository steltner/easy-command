<?php declare(strict_types=1);

namespace Easy;

use BadMethodCallException;
use Easy\Service\CommandListService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as SymfonyApplication;

use function call_user_func_array;
use function is_callable;

class Application extends SymfonyApplication
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        $this->container = $container;

        $this->addCommands(($this->container->get(CommandListService::class))());

        parent::__construct($name, $version);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Calling a non-existant method on App checks to see if there's an item
     * in the container that is callable and if so, calls it.
     *
     * @param string $method
     * @param $args
     * @return mixed
     */
    public function __call(string $method, $args)
    {
        if ($this->container->has($method)) {
            $obj = $this->container->get($method);
            if (is_callable($obj)) {
                return call_user_func_array($obj, $args);
            }
        }

        throw new BadMethodCallException('Method ' . $method . ' is not a valid method');
    }
}
