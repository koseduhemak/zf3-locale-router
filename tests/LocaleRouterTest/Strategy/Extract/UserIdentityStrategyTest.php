<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class UserIdentityStrategyTest extends TestCase
{
    /** @var UserIdentityStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new UserIdentityStrategy($languageOptions);
    }

    public function testLocaleDetection()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'en_US');

        $request->getHeaders()->addHeader($cookie);

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'en_US');
    }

    public function testCannotDetectLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'foo');

        $request->getHeaders()->addHeader($cookie);

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertNull($locale->getLocale());
    }

    public function testSetStrategyOptions()
    {
        $this->strategy->setStrategyOptions(['cookie_name' => 'cookieTestParam']);

        $prop = new \ReflectionProperty(CookieStrategy::class, 'cookieName');
        $prop->setAccessible(true);
        $this->assertEquals('cookieTestParam', $prop->getValue($this->strategy));
    }

    public function testLocaleDetectionWithCustomParameter()
    {
        $this->strategy->setStrategyOptions(['cookie_name' => 'cookieTestParam']);

        $request = new Request();
        $request->setUri('http://www.example.com/en/test/test2?testParam=de');

        $cookie = new Cookie();
        $cookie->offsetSet('cookieTestParam', 'de');

        $request->getHeaders()->addHeader($cookie);

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');
    }
}
