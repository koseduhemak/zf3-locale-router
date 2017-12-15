<?php


namespace LocaleRouter\Strategy\Persist;

use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

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
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function save($locale, ResponseInterface $response)
    {
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
}
