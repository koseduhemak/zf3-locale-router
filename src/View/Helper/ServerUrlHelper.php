<?php

namespace LocaleRouter\View\Helper;

use Zend\Console\Console;

class ServerUrlHelper extends \Zend\View\Helper\ServerUrl
{
    protected $config;

    public function __construct(array $cliConfig)
    {
        $this->config = $cliConfig;
    }

    public function __invoke($requestUri = null)
    {
        if (Console::isConsole()) {
            $locale = \Locale::getDefault();

            if (array_key_exists($locale, $this->config) && array_key_exists('scheme', $this->config[$locale]) && array_key_exists('host', $this->config[$locale])) {
                $result = $this->config[$locale]['scheme'] . '://' . $this->config[$locale]['host'] . $requestUri;
            } else {
                throw new \Exception('No configuration for server url helper provided! Configure the "links" config key accordingly.');
            }
        } else {
            $result = parent::__invoke($requestUri);
        }

        return $result;
    }
}
