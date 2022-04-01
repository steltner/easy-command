<?php

declare(strict_types=1);

namespace Easy;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager as LaminasServiceManager;
use function array_key_exists;

class ServiceManager extends LaminasServiceManager
{
    public function get($name)
    {
        if (str_contains($name, '.')) {
            $config = $this->services;
            foreach (explode('.', $name) as $segment) {
                if (!array_key_exists($segment, $config)) {
                    throw new ServiceNotFoundException('Could not get config from container: ' . $name);
                }

                $config = &$config[$segment];
            }

            return $config;
        }

        return parent::get($name);
    }
}
