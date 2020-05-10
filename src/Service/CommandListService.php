<?php

declare(strict_types=1);

namespace Easy\Service;

class CommandListService
{
    private CommandResolver $resolver;
    private array $commands;

    public function __construct(array $config, CommandResolver $resolver)
    {
        $this->commands = $config['commands'];
        $this->resolver = $resolver;
    }

    public function __invoke(): array
    {
        return $this->resolver->resolveList($this->commands);
    }
}
