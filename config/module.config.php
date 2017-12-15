<?php

namespace LocaleRouter;

use LocaleRouter\Options\LanguageOptions;
use Zend\Router\Http\TreeRouteStack;

return [
    'router' => [
        'router_class' => Mvc\Router\Http\LanguageTreeRouteStack::class,
    ],
    'service_manager' => [
        'delegators' => [
            'HttpRouter'          => [Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory::class],
            TreeRouteStack::class => [Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory::class],
        ],
        'factories' => [
            Strategy\StrategyPluginManager::class => Factory\Strategy\StrategyPluginManagerFactory::class,
            LanguageOptions::class                => Factory\Options\LanguageOptionsFactory::class,
            Listener\RouteListener::class         => Factory\Listener\RouteListenerFactory::class,
            Service\PersistStrategyService::class => Factory\Service\PersistStrategyServiceFactory::class,
        ],
    ],
    'localeRouter' => [
        'defaultLocale' => 'de_DE',

        'languages' => ['de' => 'de_DE', 'en' => 'en_GB'],

        'extractStrategies' => [
            'extract-asset',
            'extract-query',
            [
                'name'    => Strategy\Extract\UriPathStrategy::class,
                'options' => [
                    'redirect_when_found' => true,
                ],
            ],
            'extract-cookie',
            'extract-acceptlanguage',
        ],

        'persistStrategies' => [
            Strategy\Persist\DoctrineStrategy::class,
            Strategy\Persist\CookieStrategy::class,
        ],

        'authService' => 'zfcuser_auth_service',
    ],
];
