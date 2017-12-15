<?php

namespace LocaleRouter\Factory\Strategy;

use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

class AbstractExtractStrategyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        /** @var LanguageOptions $options */
        $options = $container->get(LanguageOptions::class);

        return new $requestedName($options);
    }
}
