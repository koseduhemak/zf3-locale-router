<?php

namespace LocaleRouterTest\Mvc\Router\Http;

use LocaleRouter\Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory;
use LocaleRouter\Factory\Strategy\StrategyPluginManagerFactory;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use LocaleRouter\Strategy\StrategyPluginManager;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\AcceptLanguage;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class LanguageTreeRouteStackTest extends TestCase
{
    const LANGUAGE_EN = 'en_GB';
    const LANGUAGE_DE = 'de_DE';

    /** @var LanguageTreeRouteStack */
    protected $languageTreeRouteStack;

    public function setUp()
    {
        $serviceManager = $this->getServiceLocator();

        $factory                      = new LanguageTreeRouteStackDelegatorFactory();
        $this->languageTreeRouteStack = $factory->__invoke($serviceManager, 'HttpRouter', function () {
            return LanguageTreeRouteStack::factory();
        }, null);
    }

    public function testChaininOfStrategies()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_EN, because we added query-strategy before uripathstrategy
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());

        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'en_GB');

        $request->getHeaders()->addHeader($cookie);

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_DE, because we added uripath-strategy after query-strategy and before other strategies
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'en_GB');

        $request->getHeaders()->addHeader($cookie);

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_EN, because we added a cookie
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2');

        $header   = new AcceptLanguage();
        $header->addLanguage('de-DE', 0.6);
        $header->addLanguage('en-US', 1);
        $header->addLanguage('nl-NL', 0.8);

        $request->getHeaders()
            ->addHeader($header);

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_EN, because even if we did not configure "en-US" as valid locale, "en" is the spoken language according to AcceptLanguage headers. Therefore we choose "en_GB" in favor of "de_DE"
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());
    }

    public function testPhpunitDisableEnable()
    {
        // to test this module we set LOCALEROUTER_PHPUNIT server-constant to true (otherwise we cannot execute test cases for localeRouter). Therefore we test here what happens if we disable processing for phpunit tests (which is the default)
        $_SERVER['LOCALEROUTER_PHPUNIT'] = false;

        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);

        // should return default lang => static::LANGUAGE_DE, because we disabled processing on phpunit
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        $_SERVER['LOCALEROUTER_PHPUNIT'] = true;

        // now test with default behavior (should return english)
        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);

        // should return default lang => static::LANGUAGE_DE, because we disabled processing on phpunit
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());
    }

    protected function getServiceLocator(array $config = [])
    {
        $testCaseConfig = [
            'localeRouter' => [
                'defaultLocale' => static::LANGUAGE_DE,

                'languages' => ['de' => static::LANGUAGE_DE, 'en' => static::LANGUAGE_EN],

                'extractStrategies' => [
                    'extract-asset',
                    'extract-query',
                    [
                        'name'    => \LocaleRouter\Strategy\Extract\UriPathStrategy::class,
                        'options' => [
                            'redirect_when_found' => true,
                        ],
                    ],
                    'extract-cookie',
                    'extract-acceptlanguage',
                ],

                'persistStrategies' => [
                    \LocaleRouter\Strategy\Persist\DoctrineStrategy::class,
                    \LocaleRouter\Strategy\Persist\CookieStrategy::class,
                ],

                'authService' => 'zfcuser_auth_service',
            ],
        ];

        $moduleConfig = array_merge(include 'config/module.config.php', $testCaseConfig);

        $serviceLocator = new ServiceManager();

        $serviceLocator->setService('Config', $moduleConfig);

        //$serviceLocator->setFactory(StrategyPluginManager::class, StrategyPluginManagerFactory::class);
        //$serviceLocator->setFactory('HttpRouter', \Zend\Router\Http\HttpRouterFactory::class);
        //$serviceLocator->setFactory(TreeRouteStack::class, \Zend\Router\Http\HttpRouterFactory::class);
        //$serviceLocator->setFactory('RoutePluginManager', \Zend\Router\RoutePluginManagerFactory::class);
        //$serviceLocator->setFactory(\Zend\Router\RouteStackInterface::class, \Zend\Router\RouteStackInterface::class);

        $serviceLocator->configure($moduleConfig['service_manager']);

        return $serviceLocator;
    }
}
