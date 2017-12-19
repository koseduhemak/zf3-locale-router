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
];
