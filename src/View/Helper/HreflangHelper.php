<?php

namespace LocaleRouter\View\Helper;


use LocaleRouter\Options\LanguageOptions;
use Laminas\View\Helper\AbstractHelper;

class HreflangHelper extends AbstractHelper
{
    /** @var LanguageOptions */
    protected $options;

    /**
     * HreflangHelper constructor.
     *
     * @param LanguageOptions $options
     */
    public function __construct(LanguageOptions $options)
    {
        $this->options = $options;
    }

    public function __invoke($url, $locale = null)
    {
        if (!$locale) {
            $locale = \Locale::getDefault();
        }

        $linksConfig = $this->options->getLinks();

        if (array_key_exists('scheme', $linksConfig[$locale])
            && array_key_exists('host', $linksConfig[$locale])
        ) {
            $href = $linksConfig[$locale]['scheme'] . '://'
                . $linksConfig[$locale]['host'] . $url;

            if ($this->options->isHreflangPrimaryLanguageOnly()) {
                $hreflang = \Locale::getPrimaryLanguage($locale);
            } else {
                $hreflang = $locale;
            }

            $this->getView()->headLink([
                'rel' => 'alternate',
                'hreflang' => $hreflang,
                'href' => $href
            ]);
        } else {
            throw new \Exception(sprintf('No configuration for server url helper provided! Configure the "links" config key accordingly for given locale: %s.',
                $locale));
        }
    }
}