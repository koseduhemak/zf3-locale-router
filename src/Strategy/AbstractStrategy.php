<?php


namespace LocaleRouter\Strategy;

use LocaleRouter\Options\LanguageOptions;

abstract class AbstractStrategy
{
    /** @var LanguageOptions */
    protected $options;

    /**
     * UriPathStrategy constructor.
     *
     * @param LanguageOptions $languageOptions
     */
    public function __construct(LanguageOptions $languageOptions)
    {
        $this->options = $languageOptions;
    }

    public function getLanguage($param)
    {
        $languages = $this->options->getLanguages();

        return (array_key_exists($param, $languages) || ($param = array_search($param, $languages))) && count($languages) > 1 ? $languages[$param] : null;
    }
}
