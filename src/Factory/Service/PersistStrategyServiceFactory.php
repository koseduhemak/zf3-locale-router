<?php

namespace LocaleRouter\Factory\Service;

use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Service\PersistStrategyService;
use LocaleRouter\Strategy\Persist\PersistStrategyInterface;
use LocaleRouter\Strategy\StrategyPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class PersistStrategyServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        /** @var PersistStrategyService $persistService */
        $persistService = new $requestedName();

        /** @var LanguageOptions $languageOptions */
        $languageOptions = $container->get(LanguageOptions::class);

        /** @var StrategyPluginManager $strategyPluginManager */
        $strategyPluginManager = $container->get(StrategyPluginManager::class);

        $strategyClasses = [];
        foreach ($languageOptions->getPersistStrategies() as $strategy) {
            if (is_string($strategy)
                || (is_array($strategy)
                    && array_key_exists('name', $strategy))
            ) {
                $strategyIdentifier = is_array($strategy) ? $strategy['name']
                    : $strategy;

                if ($strategyPluginManager->has($strategyIdentifier)) {
                    /** @var PersistStrategyInterface $strategyClass */
                    $strategyClass = $strategyPluginManager->get(
                        $strategyIdentifier
                    );

                    if (is_array($strategy)
                        && array_key_exists(
                            'options', $strategy
                        )
                    ) {
                        $strategyClass->setStrategyOptions(
                            $strategy['options']
                        );
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

        $persistService->setStrategies($strategyClasses);

        return $persistService;
    }
}
