<?php

namespace LocaleRouter\Strategy;

use LocaleRouter\Factory\Strategy\AbstractExtractStrategyFactory;
use Zend\ServiceManager\AbstractPluginManager;

class StrategyPluginManager extends AbstractPluginManager
{
    protected $instanceOf = StrategyInterface::class;

    /**
     * {@inheritdoc}
     */
    protected $aliases = [
            // extract strategies
            'extract-cookie'         => Extract\CookieStrategy::class,
            'extract-host'           => Extract\HostStrategy::class,
            'extract-acceptlanguage' => Extract\HttpAcceptLanguageStrategy::class,
            'extract-query'          => Extract\QueryStrategy::class,
            'extract-uripath'        => Extract\UriPathStrategy::class,
            'extract-asset'          => Extract\AssetStrategy::class,
            'extract-useridentity'   => Extract\UserIdentityStrategy::class,

            // persist strategies
            'persist-doctrine'       => Persist\DoctrineStrategy::class,
        ];

    /**
     * {@inheritdoc}
     */
    protected $factories = [
            // extract strategies
            Extract\CookieStrategy::class             => AbstractExtractStrategyFactory::class,
            Extract\HostStrategy::class               => AbstractExtractStrategyFactory::class,
            Extract\HttpAcceptLanguageStrategy::class => AbstractExtractStrategyFactory::class,
            Extract\QueryStrategy::class              => AbstractExtractStrategyFactory::class,
            Extract\UriPathStrategy::class            => AbstractExtractStrategyFactory::class,
            Extract\AssetStrategy::class              => AbstractExtractStrategyFactory::class,
            Extract\UserIdentityStrategy::class       => \LocaleRouter\Factory\Strategy\Extract\UserIdentityStrategyFactory::class,

            // persist strategies
            Persist\DoctrineStrategy::class           => \LocaleRouter\Factory\Strategy\Persist\DoctrineStrategyFactory::class,
            Persist\CookieStrategy::class             => \LocaleRouter\Factory\Strategy\Persist\CookieStrategyFactory::class,
        ];
}
