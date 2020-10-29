<?php

namespace LocaleRouter\View\Helper;


use LocaleRouter\Options\LanguageOptions;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\ServerUrl;

class LanguageLinkHelper extends AbstractHelper
{
    /** @var LanguageOptions */
    protected $options;

    /**
     * LanguageLinkHelper constructor.
     * @param LanguageOptions $options
     */
    public function __construct(LanguageOptions $options)
    {
        $this->options = $options;
    }

    public function __invoke($language)
    {
        $serverUrl = new ServerUrl();

        if (array_key_exists($language, $this->options->getLinks())) {
            $languageLink = $this->options->getLinks()[$language];
            if (is_string($languageLink)) {
                $res = $languageLink;
            } elseif (is_array($languageLink)) {
                if (array_key_exists('host', $languageLink)) {
                    $serverUrl->setHost($languageLink['host']);
                }
                if (array_key_exists('scheme', $languageLink)) {
                    $serverUrl->setScheme($languageLink['scheme']);
                }
                if (array_key_exists('port', $languageLink)) {
                    $serverUrl->setPort($languageLink['port']);
                }
                if (array_key_exists('path', $languageLink)) {
                    $res = $serverUrl->__invoke($languageLink['path']);
                } else {
                    $res = $serverUrl->__invoke(true);
                }
            }

            return $res;
        }

        throw new \Exception(sprintf('Please make sure the specified language %s has a configured url in config (option key "links".', $language));
    }
}