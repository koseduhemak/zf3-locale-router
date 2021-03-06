<?php

namespace LocaleRouter\Mvc\Router\Http;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Mvc\I18n\Router\TranslatorAwareTreeRouteStack;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\RequestInterface;
use Laminas\Uri\Uri;

class LanguageTreeRouteStack extends TranslatorAwareTreeRouteStack
{
    const URL_IDENTIFIER_DOMAIN = 'domain';
    const URL_IDENTIFIER_PATH = 'path';

    /** @var LanguageOptions */
    protected $languageOptions;

    /** @var AuthenticationServiceInterface */
    protected $authenticationService;

    /** @var string */
    protected $lastMatchedLocale;

    /** @var array */
    protected $strategies = [];

    /** @var string */
    protected $redirect = '';

    /**
     * Returns the locale that was found in the last matched URL. It is also
     * stored if no RouteMatch instance is provided (e.g. 404 error)
     * @return string
     */
    public function getLastMatchedLocale()
    {
        return $this->lastMatchedLocale;
    }

    public function assemble(array $params = [], array $options = [])
    {
        //$res = parent::assemble($params, $options);
        // Assuming, this stack can only orrur on top level
        // TODO is there any way to ensure that this is called only for top level?

        // get translator
        $translator = null;
        if (isset($options['translator'])) {
            $translator = $options['translator'];
        } elseif ($this->hasTranslator() && $this->isTranslatorEnabled()) {
            $translator = $this->getTranslator();
        }

        $languages = $this->getRouteLanguages();

        $oldBase = $this->baseUrl; // save old baseUrl
        // only add language key when more than one language is supported
        if (count($languages) > 1) {
            if (isset($params['locale'])) {
                // use parameter if provided
                $locale = $params['locale'];
                // get key for locale
                $key = array_search($locale, $languages);
            } elseif (is_callable([$translator, 'getLocale'])) {
                // use getLocale if possible
                $locale = $translator->getLocale();
                // get key for locale
                $key = array_search($locale, $languages);
            }

            if (isset($key) && $key === 'root') {
                $key = '';
            }

            if (! empty($key)) {
                // add key to baseUrl
                $this->setBaseUrl($oldBase . '/'.$key);
            }
        }

        $res = parent::assemble($params, $options);
        // restore baseUrl
        $this->setBaseUrl($oldBase);

        return $res;
    }

    public function match(RequestInterface $request, $pathOffset = null, array $options = [])
    {
        $locale         = null;
        $this->redirect = null;

        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }

        // disable on phpunit (you can force processing by setting $_SERVER['LOCALEROUTER_PHPUNIT'] = true
        /*if (preg_match('/.*\/phpunit$/i', $_SERVER['SCRIPT_NAME']) && (! array_key_exists('LOCALEROUTER_PHPUNIT', $_SERVER) || (array_key_exists('LOCALEROUTER_PHPUNIT', $_SERVER) && false === $_SERVER['LOCALEROUTER_PHPUNIT']))) {
            $this->lastMatchedLocale = $this->getLanguageOptions()->getDefaultLocale();

            return null;
        }*/

        $oldBase = $this->baseUrl;

        // set default locale
        \Locale::setDefault($this->languageOptions->getDefaultLocale());

        // process strategies here
        foreach ($this->strategies as $strategy) {
            /** @var StrategyResultModel $result */
            $result = $strategy->extractLocale($request, $this->getBaseUrl());
            $locale = $result->getLocale();

            if ($result->isProcessingStopped()) {
                return TranslatorAwareTreeRouteStack::match($request, $pathOffset, $options);
            }

            // stop processing if a strategy found a locale
            if (! empty($locale) && is_string($locale)) {
                \Locale::setDefault($locale);

                break;
            }
        }

        // if no locale was found, retrieve default locale
        if (! $locale) {
            $locale = $this->getLanguageOptions()->getDefaultLocale();
        }

        $uri           = $request->getUri();
        $baseUrlLength = strlen($this->getBaseUrl());
        $path          = ltrim(substr($uri->getPath(), $baseUrlLength), '/');
        $pathParts     = explode('/', $path);
        $oldLanguage   = $pathParts[0];

        $languages = $this->getLanguageOptions()->getLanguages();

        // if locale was found in uri path
        if ($this->getLanguageOptions()->getUrlIdentifier() === static::URL_IDENTIFIER_PATH && array_key_exists($pathParts[0], $languages) || ($pathParts[0] = array_search($pathParts[0], $languages) && count($languages) > 1)) {
            // if locale was found in configured languages and previous locale is current locale
            if ($oldLanguage === \Locale::getPrimaryLanguage($locale)) {
                $this->setBaseUrl($oldBase . '/' . $oldLanguage);
            } else {
                // if locale has changed after last request
                // if root locale is configured && extracted locale matches root locale
                if (array_key_exists('root', $languages) && $locale === $languages['root']) {
                    $this->setBaseUrl($oldBase . '/');
                } else {
                    $this->setBaseUrl($oldBase . '/' . \Locale::getPrimaryLanguage($locale));
                }

                // assemble redirect uri
                $newUri = $this->getNewRequestUri($request, $oldLanguage);

                $this->redirect = $this->getBaseUrl() . '/' . ltrim($newUri, '/');
            }
        } else {
            // if root locale is configured && extracted locale matches root locale
            if (array_key_exists('root', $languages) && ((is_array($languages['root']) && in_array($locale, $languages['root'])) || (is_string($languages['root']) && $locale === $languages['root']))) {
                $this->setBaseUrl($oldBase . '/');
            } else {
                // redirect to correct uri
                // assemble redirect uri
                $newUri = $this->getNewRequestUri($request);
                // if no root locale is configured or if root locale is configured but current locale is not root locale

                // need to redirect
                $this->redirect = '/' . \Locale::getPrimaryLanguage($locale) . '/' . $newUri;
            }
        }

        // set the last matched locale
        $this->lastMatchedLocale = $locale;

        // match route
        $res = TranslatorAwareTreeRouteStack::match($request, $pathOffset, $options);

        $this->setBaseUrl($oldBase);

        if ($res instanceof RouteMatch && ! empty($locale)) {
            $res->setParam('locale', $locale);
        }

        return $res;
    }

    public function getNewRequestUri(RequestInterface $request, $oldLanguage = '')
    {
        /** @var Uri $reqUri */
        $reqUri = $request->getUri();
        $params = $reqUri->getQueryAsArray();

        $queryParamName = null;
        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof QueryStrategy) {
                $queryParamName = $strategy->getParamName();
                break;
            }
        }

        if ($queryParamName) {
            unset($params[$queryParamName]);
        }

        $reqUri->setQuery($params);
        $newUri = $reqUri->toString();

        if (null !== $reqUri->getHost()) {
            $start = strpos($newUri, $reqUri->getHost()) + strlen($reqUri->getHost());
        } else {
            $start = 0 + strlen($reqUri->getHost());
        }

        $newUri = substr($newUri, $start);
        $newUri = substr($newUri, strlen($oldLanguage) + 1);


        return $newUri;
    }

    /**
     * @return array
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param array $strategies
     */
    public function setStrategies($strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param string $redirect
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function getLanguageOptions()
    {
        return $this->languageOptions;
    }

    public function setLanguageOptions(LanguageOptions $languageOptions)
    {
        $this->languageOptions = $languageOptions;
    }

    protected function getRouteLanguages()
    {
        if (! empty($this->getLanguageOptions())) {
            return $this->getLanguageOptions()->getLanguages();
        }

        return [];
    }
}
