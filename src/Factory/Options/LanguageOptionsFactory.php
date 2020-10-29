<?php

namespace LocaleRouter\Factory\Options;

use Interop\Container\ContainerInterface;
use LocaleRouter\Module;
use LocaleRouter\Options\LanguageOptions;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LanguageOptionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        $config = $container->get('Config');

        /** @var LanguageOptions $class */
        $class = new $requestedName(
            isset($config[Module::CONFIG_KEY]) ? $config[Module::CONFIG_KEY]
                : []
        );

        return $class;
    }
}
