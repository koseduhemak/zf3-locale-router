<?php

namespace LocaleRouter\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\View\Helper\LanguageLinkHelper;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LanguageLinkHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var LanguageOptions $options */
        $options = $container->get(LanguageOptions::class);

        return new LanguageLinkHelper($options);
    }

}