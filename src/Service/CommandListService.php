<?php

declare(strict_types=1);

namespace Easy\Service;

class CommandListService
{
    public function __construct(private array $config, private CommandResolver $resolver)
    {
        $this->config = $config['commands'];
    }

    public function __invoke(): array
    {
        return $this->resolver->resolveList($this->config);
    }
}
