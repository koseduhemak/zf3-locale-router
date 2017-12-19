<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Zend\Stdlib\RequestInterface;

class HostStrategy extends AbstractExtractStrategy
{
    const LOCALE_KEY = ':locale';

    private $domain;
    private $aliases;

    public function setStrategyOptions(array $options = [])
    {
        if (array_key_exists('domain', $options)) {
            $this->domain = (string) $options['domain'];
        }
        if (array_key_exists('aliases', $options)) {
            $this->aliases = (array) $options['aliases'];
        }
    }

    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $resultModel = new StrategyResultModel();
        $locale      = null;

        $domain = $this->getDomain();
        if (! null === $domain) {
            throw new \InvalidArgumentException(
                'The strategy must be configured with a domain option'
            );
        }
        if (strpos($domain, self::LOCALE_KEY) === false) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The domain %s must contain a locale key part "%s"',
                    $domain, self::LOCALE_KEY
                )
            );
        }

        $host    = $request->getUri()->getHost();
        $pattern = str_replace(self::LOCALE_KEY, '([a-zA-Z-_.]+)', $domain);
        $pattern = sprintf('@%s@', $pattern);
        $result  = preg_match($pattern, $host, $matches);

        if ($result) {
            $localeCandidate = $matches[1];

            $aliases = $this->getAliases();
            if (null !== $aliases
                && array_key_exists(
                    $localeCandidate, $aliases
                )
            ) {
                $locale = $this->getLanguage($aliases[$localeCandidate]);
            }
        }

        $resultModel->setLocale($locale);

        return $resultModel;
    }

    protected function getDomain()
    {
        return $this->domain;
    }

    protected function getAliases()
    {
        return $this->aliases;
    }
}
