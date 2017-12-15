<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use LocaleRouter\Strategy\Extract\HttpAcceptLanguageStrategy;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\AcceptLanguage;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class HttpAcceptLanguageStrategyTest extends TestCase
{
    /** @var HttpAcceptLanguageStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new HttpAcceptLanguageStrategy($languageOptions);
    }

    public function testLocaleDetection()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $header   = new AcceptLanguage();
        $header->addLanguage('de-DE', 0.6);
        $header->addLanguage('en-US', 1);
        $header->addLanguage('nl-NL', 0.8);

        $request->getHeaders()
            ->addHeader($header);

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'en_US');
    }

    public function testCannotDetectLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $header   = new AcceptLanguage();
        $header->addLanguage('null', 0.6);
        $header->addLanguage('blubb', 1);
        $header->addLanguage('nl-NL', 0.8);

        $request->getHeaders()
            ->addHeader($header);

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        // should be equally to null, because nl-NL is not configured language
        $this->assertNull($locale->getLocale());
    }
}
