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
    'view_helpers' => [
        'factories'=> [
            View\Helper\LanguageLinkHelper::class => Factory\View\Helper\LanguageLinkHelperFactory::class,
            View\Helper\ServerUrlHelper::class              => Factory\View\Helper\ServerUrlHelperFactory::class,
            View\Helper\HreflangHelper::class => Factory\View\Helper\HreflangHelperFactory::class
            ],
        'aliases' => [
            'getLanguageLink' => View\Helper\LanguageLinkHelper::class,
            'serverUrl'           => View\Helper\ServerUrlHelper::class,
            'serverurl'           => View\Helper\ServerUrlHelper::class,
            'ServerUrl'           => View\Helper\ServerUrlHelper::class,
            'getHreflang'         => View\Helper\HreflangHelper::class
        ]
    ]
];
