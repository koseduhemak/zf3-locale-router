<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\HostStrategy;
use PHPUnit\Framework\TestCase;
use Laminas\Http\Request;

class HostStrategyTest extends TestCase
{
    /** @var HostStrategy */
    private $strategy;

    public function setUp() : void
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new HostStrategy($languageOptions);

        $this->strategy->setStrategyOptions([
            'domain'   => 'www.example.:locale',
            'aliases'  => [
                'de'  => 'de_DE',
                'com' => 'en_US',
            ],
        ]);
    }

    public function testLocaleDetection()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'en_US');

        $request = new Request();
        $request->setUri('http://www.example.de/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');
    }

    public function testCannotDetectLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.nl/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertNull($locale->getLocale());
    }
}
