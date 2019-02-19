<?php

namespace LocaleRouterTest\Strategy\Persist;

use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Persist\CookieStrategy;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib\ResponseInterface;

class CookieStrategyTest extends TestCase
{
    /** @var CookieStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $request         = new Request();
        $this->strategy  = new CookieStrategy($languageOptions, $request);
    }

    public function testLocaleSaved()
    {
        $response = new Response();

        /** @var Response $resultResponse */
        $resultResponse = $this->strategy->save('de_DE', $response);

        $cookieName = (new SetCookie())->getFieldName();

        /** @var SetCookie $setCookie */
        $setCookie = $resultResponse->getHeaders()->get($cookieName)->current();

        $this->assertInstanceOf(ResponseInterface::class, $resultResponse);
        $this->assertEquals($setCookie->getValue(), 'de_DE');
    }

    public function testLocaleNotSaved()
    {
        $response = new Response();

        /** @var Response $resultResponse */
        $resultResponse = $this->strategy->save('nl_NL', $response);

        /** @var SetCookie $setCookie */
        $setCookie = $resultResponse->getHeaders()->get('SetCookie');

        $this->assertInstanceOf(ResponseInterface::class, $resultResponse);
        $this->assertFalse($setCookie);
    }

    public function testSetStrategyOptions()
    {
        $this->strategy->setStrategyOptions(['cookieName' => 'cookieTestParam']);

        $prop = new \ReflectionProperty(CookieStrategy::class, 'cookieName');
        $prop->setAccessible(true);
        $this->assertEquals('cookieTestParam', $prop->getValue($this->strategy));
    }

    public function testLocaleDetectionWithCustomParameter()
    {
        $this->strategy->setStrategyOptions(['cookieName' => 'cookieTestParam']);

        $response = new Response();

        /** @var Response $resultResponse */
        $resultResponse = $this->strategy->save('en_US', $response);

        $cookieName = (new SetCookie())->getFieldName();

        /** @var SetCookie $setCookie */
        $setCookie = $resultResponse->getHeaders()->get($cookieName)->current();

        $this->assertInstanceOf(ResponseInterface::class, $resultResponse);
        $this->assertEquals($setCookie->getValue(), 'en_US');
    }
}
