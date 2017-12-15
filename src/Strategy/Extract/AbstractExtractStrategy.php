<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use Zend\Stdlib\RequestInterface;

abstract class AbstractExtractStrategy implements ExtractStrategyInterface
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

    /**
     * @param RequestInterface $request
     * @param                  $baseUrl
     *
     * @return StrategyResultModel
     */
    abstract public function extractLocale(RequestInterface $request, $baseUrl);

    public function setStrategyOptions(array $options = [])
    {
    }

    public function getLanguage($param)
    {
        $languages = $this->options->getLanguages();

        return (array_key_exists($param, $languages)
            || ($param = array_search(
                $param,
                $languages
            )))
        && count($languages) > 1 ? $languages[$param] : null;
    }
}
