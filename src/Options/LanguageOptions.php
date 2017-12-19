<?php

namespace LocaleRouter\Options;

use Zend\Authentication\AuthenticationService;
use ZF2LanguageRoute\Options\LanguageRouteOptions;

class LanguageOptions extends LanguageRouteOptions
{
    /**
     * Default locale.
     *
     * @var string
     */
    protected $defaultLocale = '';

    /**
     * Strategies, which are used for extracting locale
     *
     * @var array
     */
    protected $extractStrategies = [];

    /**
     * Strategies, which are used to save locale to
     *
     * @var array
     */
    protected $persistStrategies = [];

    /**
     * @var string
     */
    protected $authService = AuthenticationService::class;

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $defaultLocale
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function getStrategyOptions($class)
    {
        $result = null;
        foreach ($this->getExtractStrategies() as $strategy) {
            if (is_array($strategy) && array_key_exists('name', $strategy)
                && array_key_exists('options', $strategy)
            ) {
                if ($strategy['name'] === $class) {
                    $result = $strategy['options'];
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getExtractStrategies()
    {
        return $this->extractStrategies;
    }

    /**
     * @param array $extractStrategies
     */
    public function setExtractStrategies($extractStrategies)
    {
        foreach ($extractStrategies as $strategy) {
            $this->extractStrategies[] = $strategy;
        }
    }

    /**
     * @return array
     */
    public function getPersistStrategies()
    {
        return $this->persistStrategies;
    }

    /**
     * @param array $persistStrategies
     */
    public function setPersistStrategies($persistStrategies)
    {
        $this->persistStrategies = $persistStrategies;
    }

    /**
     * @return string
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * @param string $authService
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;
    }
}
