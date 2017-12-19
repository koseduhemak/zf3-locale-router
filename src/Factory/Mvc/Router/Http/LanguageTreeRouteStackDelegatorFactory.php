<?php

namespace LocaleRouter\Factory\Mvc\Router\Http;

use Interop\Container\ContainerInterface;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\ExtractStrategyInterface;
use LocaleRouter\Strategy\StrategyPluginManager;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class LanguageTreeRouteStackDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $router = $callback();

        if (! $router instanceof LanguageTreeRouteStack) {
            return $router;
        }

        $languageOptions = $container->get(LanguageOptions::class);
        $router->setLanguageOptions($languageOptions);

        /*if ($container->has('zfcuser_auth_service')) {
            $router->setAuthenticationService(
                $container->get('zfcuser_auth_service')
            );
        }*/

        $strategyPluginManager = $container->get(StrategyPluginManager::class);

        $strategyClasses = [];
        foreach ($languageOptions->getExtractStrategies() as $strategy) {
            if (is_string($strategy) || (is_array($strategy) && array_key_exists('name', $strategy))) {
                $strategyIdentifier = is_array($strategy) ? $strategy['name'] : $strategy;

                if ($strategyPluginManager->has($strategyIdentifier)) {
                    /** @var ExtractStrategyInterface $strategyClass */
                    $strategyClass = $strategyPluginManager->get($strategyIdentifier);

                    if (is_array($strategy) && array_key_exists('options', $strategy)) {
                        $strategyClass->setStrategyOptions($strategy['options']);
                    }

                    $strategyClasses[] = $strategyClass;
                } else {
                    throw new \InvalidArgumentException(
                        'The strategy "' . $strategyIdentifier
                        . '" does not exist.'
                    );
                }
            }
        }

        $router->setStrategies($strategyClasses);

        return $router;
    }
}
