<?php

namespace LocaleRouter\View\Helper;

use LocaleRouter\Options\LanguageOptions;
use Zend\Console\Console;

class ServerUrlHelper extends \Zend\View\Helper\ServerUrl
{
    /** @var LanguageOptions */
    protected $options;

    /**
     * ServerUrlHelper constructor.
     * @param LanguageOptions $options
     */
    public function __construct(LanguageOptions $options)
    {
        $this->options = $options;
    }

    public function __invoke($requestUri = null)
    {
        if (Console::isConsole()) {
            $locale = \Locale::getDefault();

            $linksConfig = $this->options->getLinks();

            if (array_key_exists($locale, $linksConfig) && array_key_exists('scheme', $linksConfig[$locale]) && array_key_exists('host', $linksConfig[$locale])) {
                $linksLocaleConfig = $linksConfig[$locale];
            } elseif (!empty($locale = $this->options->getDefaultLocale()) && array_key_exists($locale, $linksConfig) && array_key_exists('scheme', $linksConfig[$locale]) && array_key_exists('host', $linksConfig[$locale])) {
                $linksLocaleConfig = $linksConfig[$locale];
            } else {
                throw new \Exception('No configuration for server url helper provided! Configure the "links" config key accordingly.');
            }

            $result = $linksLocaleConfig['scheme'] . '://' . $linksLocaleConfig['host'] . $requestUri;
        } else {
            $result = parent::__invoke($requestUri);
        }

        return $result;
    }
}
