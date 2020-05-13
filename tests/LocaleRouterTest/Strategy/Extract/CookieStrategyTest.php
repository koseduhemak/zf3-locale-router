<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use PHPUnit\Framework\TestCase;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Request;

class CookieStrategyTest extends TestCase
{
    /** @var CookieStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new CookieStrategy($languageOptions);
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
        $this->strategy->setStrategyOptions(['cookieName' => 'cookieTestParam']);

        $prop = new \ReflectionProperty(CookieStrategy::class, 'cookieName');
        $prop->setAccessible(true);
        $this->assertEquals('cookieTestParam', $prop->getValue($this->strategy));
    }

    public function testLocaleDetectionWithCustomParameter()
    {
        $this->strategy->setStrategyOptions(['cookieName' => 'cookieTestParam']);

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
