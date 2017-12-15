<?php


namespace LocaleRouterTest\Strategy\Extract;


use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\UriPathStrategy;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;

class UriPathStrategyTest extends TestCase
{
    /** @var UriPathStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy = new UriPathStrategy($languageOptions);
    }

    public function testLocaleDetection()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2');
        $baseurl = "";
        $locale = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');

        $request = new Request();
        $request->setUri('http://www.example.com/en/test/test2');
        $baseurl = "";
        $locale = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'en_US');
    }

    public function testCannotDetectLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/detest/test2');
        $baseurl = "";
        $locale = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertNull($locale->getLocale());
    }
}