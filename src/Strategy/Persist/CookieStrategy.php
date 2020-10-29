<?php


namespace LocaleRouter\Strategy\Persist;

use LocaleRouter\Options\LanguageOptions;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Header\SetCookie;
use Laminas\Stdlib\RequestInterface;
use Laminas\Stdlib\ResponseInterface;

class CookieStrategy extends AbstractPersistStrategy
{
    const COOKIE_NAME = 'localeRouter';

    /** @var RequestInterface */
    protected $request;

    /**
     * The name of the cookie.
     *
     * @var string
     */
    protected $cookieName;

    /**
     * CookieStrategy constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(LanguageOptions $languageOptions, RequestInterface $request)
    {
        $this->request = $request;

        parent::__construct($languageOptions);
    }

    public function save($locale, ResponseInterface $response)
    {
        if (($locale = $this->getLanguage($locale))) {
            $cookieName   = $this->getCookieName();
            $cookieLocale = null;

            /** @var Cookie $cookie */
            $cookie = $this->request->getCookie();

            if ($cookie && $cookie->offsetExists($cookieName)) {
                $cookieLocale = $cookie->offsetGet($cookieName);
            }

            if ($cookieLocale !== $locale) {
                $path = '/';

                $setCookie = new SetCookie($cookieName, $locale, null, $path);
                $response->getHeaders()->addHeader($setCookie);
            }
        }

        return $response;
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

    public function setStrategyOptions(array $options = [])
    {
        if (array_key_exists('cookieName', $options)) {
            $this->cookieName = $options['cookieName'];
        }
    }
}
