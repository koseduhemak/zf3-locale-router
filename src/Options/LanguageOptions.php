<?php

namespace LocaleRouter\Options;

use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\AbstractOptions;

class LanguageOptions extends AbstractOptions
{
    /**
     * Array of languages allowed for language route. The key is the prefix
     * which is attached to the url (e.g. en), the value is the associated
     * locale  (e.g. 'en_US')
     * @var array
     */
    protected $languages = ['de' => 'de_DE', 'en' => 'en_US'];

    /**
     * Default locale.
     *
     * @var string
     */
    protected $defaultLocale = '';

    /**
     * Could be 'path' or 'domain'
     * @var string
     */
    protected $urlIdentifier = LanguageTreeRouteStack::URL_IDENTIFIER_PATH;

    /**
     * Links for the link view helper
     * @var array
     */
    protected $links = [];

    /**
     * Config for xdefault hreflang (same structure as $links property)
     * @var array
     */
    protected $xdefault = [];

    /**
     * Config for hreflang generation, if set to true, only language will be used as hreflang, otherwise locale
     * @var bool
     */
    protected $hreflangPrimaryLanguageOnly = false;

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

    public function getLanguages()
    {
        return $this->languages;
    }

    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }

    public function getRootLanguage()
    {
        return array_key_exists('root', $this->languages) ? $this->languages['root'] : null;
    }

    /**
     * @return string
     */
    public function getUrlIdentifier()
    {
        return $this->urlIdentifier;
    }

    /**
     * @param string $urlIdentifier
     */
    public function setUrlIdentifier($urlIdentifier)
    {
        $this->urlIdentifier = $urlIdentifier;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return array
     */
    public function getXdefault(): array
    {
        return $this->xdefault;
    }

    /**
     * @param array $xdefault
     */
    public function setXdefault(array $xdefault)
    {
        $this->xdefault = $xdefault;
    }

    /**
     * @return bool
     */
    public function isHreflangPrimaryLanguageOnly(): bool
    {
        return $this->hreflangPrimaryLanguageOnly;
    }

    /**
     * @param bool $hreflangPrimaryLanguageOnly
     */
    public function setHreflangPrimaryLanguageOnly(
        bool $hreflangPrimaryLanguageOnly
    ) {
        $this->hreflangPrimaryLanguageOnly = $hreflangPrimaryLanguageOnly;
    }
}
