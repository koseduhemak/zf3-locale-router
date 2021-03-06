<?php

namespace LocaleRouter\Factory\View\Helper;

use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\View\Helper\ServerUrlHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ServerUrlHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var LanguageOptions $options */
        $options = $container->get(LanguageOptions::class);

        if (!is_array($options->getLinks()) || empty($options->getLinks())) {
            throw new \InvalidArgumentException('Please add a "localeRouter[links]" array configuration to your project in order for cron tasks to generate emails with absolute URIs');
        }

        return new ServerUrlHelper($options);
    }
}
