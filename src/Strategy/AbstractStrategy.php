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

        switch (true) {
            case array_key_exists('root', $languages) && $key = array_search($param, $languages['root']):
                $res = $languages['root'][$key];
                break;
            case array_key_exists($param, $languages) || ($param = array_search($param, $languages)) && count($languages) > 1:
                $res = $languages[$param];
                break;
            default: $res = null;
        }
        return $res;

        //return (array_key_exists($param, $languages) || ($param = array_search($param, $languages))) && count($languages) > 1 ? $languages[$param] : null;
    }
}
