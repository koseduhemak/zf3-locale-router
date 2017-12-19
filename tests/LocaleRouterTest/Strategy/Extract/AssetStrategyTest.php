<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\AssetStrategy;
use PHPUnit\Framework\TestCase;
use Zend\Http\Request;

class AssetStrategyTest extends TestCase
{
    /** @var AssetStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new AssetStrategy($languageOptions);
    }

    public function testIfNotRedirectedToLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2/my_css.css');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertTrue($locale->isProcessingStopped());
    }

    public function testIfRedirectedToLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertFalse($locale->isProcessingStopped());
        $this->assertNull($locale->getLocale());
    }

    public function testSetStrategyOptions()
    {
        $this->strategy->setStrategyOptions(['file_extensions' => ['jpg']]);

        $prop = new \ReflectionProperty(AssetStrategy::class, 'file_extensions');
        $prop->setAccessible(true);
        $this->assertCount(1, $prop->getValue($this->strategy));
        $this->assertEquals(['jpg'], $prop->getValue($this->strategy));
    }

    public function testLocaleDetectionWithCustomParameter()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/test/test2.jpg');
        $baseurl = '';

        // first test if the user gets redirected / not prevented from redirect without parameter
        // the default config should prevent jpg's from being rewritten
        $locale = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertTrue($locale->isProcessingStopped());
        $this->assertNull($locale->getLocale());

        // secondly test if the user gets redirected (after parameter set was modified to prevent gif's being rewritten)
        $this->strategy->setStrategyOptions(['file_extensions' => ['gif']]);

        $locale = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertFalse($locale->isProcessingStopped());
        $this->assertNull($locale->getLocale());
    }
}
