<?php

namespace LocaleRouter\Factory\View\Helper;

use LocaleRouter\View\Helper\ServerUrlHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ServerUrlHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        if (!array_key_exists('localeRouter', $config) || !array_key_exists('links', $config['localeRouter'])) {
            throw new \InvalidArgumentException('Please add a "localeRouter[links]" array configuration to your project in order for cron tasks to generate emails with absolute URIs');
        }

        return new ServerUrlHelper($config['localeRouter']['links']);
    }
}
