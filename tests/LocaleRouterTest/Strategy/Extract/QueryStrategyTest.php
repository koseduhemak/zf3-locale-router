<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use PHPUnit\Framework\TestCase;
use Laminas\Http\Request;
use Laminas\Stdlib\Parameters;

class QueryStrategyTest extends TestCase
{
    /** @var QueryStrategy */
    private $strategy;

    public function setUp()
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new QueryStrategy($languageOptions);
    }

    public function testLocaleDetection()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');
        $params = new Parameters([QueryStrategy::PARAM_NAME => 'en']);
        $request->setQuery($params);
        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'en_US');

        $request = new Request();
        $request->setUri('http://www.example.com/en/test/test2?lang=de');
        $params = new Parameters([QueryStrategy::PARAM_NAME => 'de']);
        $request->setQuery($params);
        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');
    }

    public function testCannotDetectLocale()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/detest/test2?lang=asd');
        $params = new Parameters([QueryStrategy::PARAM_NAME => 'asd']);
        $request->setQuery($params);
        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertNull($locale->getLocale());
    }

    public function testSetStrategyOptions()
    {
        $this->strategy->setStrategyOptions(['paramName' => 'testParam']);

        $prop = new \ReflectionProperty(QueryStrategy::class, 'paramName');
        $prop->setAccessible(true);
        $this->assertEquals('testParam', $prop->getValue($this->strategy));
    }

    public function testLocaleDetectionWithCustomParameter()
    {
        $this->strategy->setStrategyOptions(['paramName' => 'testParam']);

        $request = new Request();
        $request->setUri('http://www.example.com/en/test/test2?testParam=de');
        $params = new Parameters(['testParam' => 'de']);
        $request->setQuery($params);
        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');
    }
}
