<?php

namespace LocaleRouter\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\View\Helper\HreflangHelper;
use Laminas\ServiceManager\Factory\FactoryInterface;

class HreflangHelperFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        /** @var LanguageOptions $options */
        $options = $container->get(LanguageOptions::class);

        if (!is_array($options->getLinks()) || empty($options->getLinks())) {
            throw new \InvalidArgumentException('Please add a "localeRouter[links]" array configuration to your project in order for cron tasks to generate emails with absolute URIs');
        }

        return new HreflangHelper($options);
    }
}
