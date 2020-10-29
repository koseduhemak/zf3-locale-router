<?php

namespace LocaleRouter\Factory\Strategy;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StrategyPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        return new $requestedName($container);
    }
}
