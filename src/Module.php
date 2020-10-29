<?php

namespace LocaleRouter;

use LocaleRouter\Listener\RouteListener;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ResponseInterface;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    const CONFIG_KEY = 'localeRouter';

    public function onBootstrap(EventInterface $e)
    {
        if (! $e instanceof MvcEvent) {
            return;
        }

        $app          = $e->getApplication();
        $eventManager = $app->getEventManager();
        $container    = $app->getServiceManager();

        /* @var $routeListener RouteListener */
        $routeListener = $container->get(RouteListener::class);

        // execute here so \Locale::getDefault() is set with the locale... Otherwise detection is in some cases too late.
        //$routeListener->onRoute($e);

        /*$eventManager->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($app, $routeListener) {
            $result = $routeListener->onRoute($e);
            if ($result instanceof ResponseInterface) {
                return $result;
            } else {
                \Locale::setDefault($result);
            }
        }, PHP_INT_MAX);*/

        $routeListener->attach($eventManager, PHP_INT_MAX);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
