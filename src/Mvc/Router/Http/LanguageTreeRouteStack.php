<?php

namespace LocaleRouter\Mvc\Router\Http;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use Zend\Mvc\I18n\Router\TranslatorAwareTreeRouteStack;
use Zend\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;
use Zend\Uri\Uri;

class LanguageTreeRouteStack extends \ZF2LanguageRoute\Mvc\Router\Http\LanguageTreeRouteStack
{
    /** @var array */
    protected $strategies = [];

    /** @var string */
    protected $redirect = '';

    public function match(RequestInterface $request, $pathOffset = null, array $options = [])
    {
        $locale = null;

        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }

        $oldBase = $this->baseUrl;

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

        if ((array_key_exists($pathParts[0], $languages) || ($pathParts[0] = array_search($pathParts[0], $languages))) && count($languages) > 1) {
            if ($oldLanguage === \Locale::getPrimaryLanguage($locale)) {
                $this->setBaseUrl($oldBase . '/' . $oldLanguage);
            } else {
                $this->setBaseUrl($oldBase . '/' . \Locale::getPrimaryLanguage($locale));

                // assemble redirect uri
                $newUri = $this->getNewRequestUri($request, $oldLanguage);

                $this->redirect = $this->getBaseUrl() . '/' . ltrim($newUri, '/');
            }
        } else {
            // assemble redirect uri
            $newUri = $this->getNewRequestUri($request);

            // need to redirect
            $this->redirect = '/' . \Locale::getPrimaryLanguage($locale) . '/'
                . $newUri;
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

        $start  = strpos($newUri, $reqUri->getHost()) + strlen($reqUri->getHost());
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
}
