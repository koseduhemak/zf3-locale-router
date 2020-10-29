<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use Laminas\Http\Header\Cookie;
use Laminas\Stdlib\RequestInterface;

class CookieStrategy extends AbstractExtractStrategy
{
    const COOKIE_NAME = 'localeRouter';

    /**
     * The name of the cookie.
     *
     * @var string
     */
    protected $cookieName;

    public function setStrategyOptions(array $options = [])
    {
        if (array_key_exists('cookieName', $options)) {
            $this->setCookieName($options['cookieName']);
        }
    }

    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $result = new StrategyResultModel();
        $locale = null;

        $cookieName = $this->getCookieName();

        /** @var Cookie $cookie */
        $cookie = $request->getCookie();

        if ($cookie && $cookie->offsetExists($cookieName)) {
            $locale = $this->getLanguage($cookie->offsetGet($cookieName));
        }

        $result->setLocale($locale);

        return $result;
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        if (null === $this->cookieName) {
            return self::COOKIE_NAME;
        }

        return (string) $this->cookieName;
    }

    /**
     * @param string $cookieName
     *
     * @throws \InvalidArgumentException
     */
    public function setCookieName($cookieName)
    {
        if (! preg_match('/^(?!\$)[!-~]+$/', $cookieName)) {
            throw new \InvalidArgumentException(
                $cookieName . ' is not a vaild cookie name.'
            );
        }

        $this->cookieName = $cookieName;
    }
}
