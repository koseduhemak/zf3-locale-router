<?php

namespace LocaleRouterTest\Mvc\Router\Http;

use LocaleRouter\Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory;
use LocaleRouter\Factory\Strategy\StrategyPluginManagerFactory;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use LocaleRouter\Strategy\StrategyPluginManager;
use PHPUnit\Framework\TestCase;
use Laminas\Http\Header\AcceptLanguage;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\Parameters;

class LanguageTreeRouteStackRootLocaleTest extends TestCase
{
    const LANGUAGE_EN = 'en_GB';
    const LANGUAGE_DE = 'de_DE';

    /** @var LanguageTreeRouteStack */
    protected $languageTreeRouteStack;

    public function setUp() : void
    {
        $serviceManager = $this->getServiceLocator();

        $factory                      = new LanguageTreeRouteStackDelegatorFactory();
        $this->languageTreeRouteStack = $factory->__invoke($serviceManager, 'HttpRouter', function () {
            return LanguageTreeRouteStack::factory();
        }, null);
    }

    public function testRedirectFromENToRoot()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/en/test/test2?locale=de_DE');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'de_DE',
        ]));

        $this->languageTreeRouteStack->match($request);
        $redirect   = $this->languageTreeRouteStack->getRedirect();

        // should be static::LANGUAGE_EN, because we added query-strategy before uripathstrategy
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        // redirect should contain "/en/"
        $this->assertEquals('/test/test2', $redirect);
    }

    public function testChaininOfStrategies()
    {
        $request = new Request();
        $request->setUri('http://www.example.com/test/test2?locale=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);
        $redirect   = $this->languageTreeRouteStack->getRedirect();

        // should be static::LANGUAGE_EN, because we added query-strategy before uripathstrategy
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());

        // redirect should contain "/en/"
        $this->assertEquals('/en/test/test2', $redirect);

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'en_GB');

        $request->getHeaders()->addHeader($cookie);

        $this->languageTreeRouteStack->match($request);
        $redirect   = $this->languageTreeRouteStack->getRedirect();

        // should be static::LANGUAGE_DE, because we added uripath-strategy after query-strategy and before other strategies
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        // redirect should be empty, because de is root locale
        $this->assertEmpty($redirect);

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2');

        $cookie = new Cookie();
        $cookie->offsetSet(CookieStrategy::COOKIE_NAME, 'en_GB');

        $request->getHeaders()->addHeader($cookie);

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_DE, because if UriPathStrategy is used in combination with "root" language, it will match even if there is no locale in uri
        // => ['root' => 'de_DE', 'en' => 'en_GB'] => http://example.com/test would match to de_DE!
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2');

        $header   = new AcceptLanguage();
        $header->addLanguage('de-DE', 0.6);
        $header->addLanguage('en-US', 1);
        $header->addLanguage('nl-NL', 0.8);

        $request->getHeaders()
            ->addHeader($header);

        $this->languageTreeRouteStack->match($request);

        // should be static::LANGUAGE_DE, because if UriPathStrategy is used in combination with "root" language, it will match even if there is no locale in uri
        // => ['root' => 'de_DE', 'en' => 'en_GB'] => http://example.com/test would match to de_DE!
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());
    }

    /*public function testPhpunitDisableEnable()
    {
        // to test this module we set LOCALEROUTER_PHPUNIT server-constant to true (otherwise we cannot execute test cases for localeRouter). Therefore we test here what happens if we disable processing for phpunit tests (which is the default)
        $_SERVER['LOCALEROUTER_PHPUNIT'] = false;

        $request = new Request();
        $request->setUri('http://www.example.com/test/test2?locale=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);

        // should return default lang => static::LANGUAGE_DE, because we disabled processing on phpunit
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        $_SERVER['LOCALEROUTER_PHPUNIT'] = true;

        // now test with default behavior (should return english)
        $request = new Request();
        $request->setUri('http://www.example.com/test/test2?locale=en');
        $request->setQuery(new Parameters([
            QueryStrategy::PARAM_NAME => 'en',
        ]));

        $this->languageTreeRouteStack->match($request);

        // should return default lang => static::LANGUAGE_DE, because we disabled processing on phpunit
        $this->assertEquals(static::LANGUAGE_EN, $this->languageTreeRouteStack->getLastMatchedLocale());
    }*/

    protected function getServiceLocator(array $config = [])
    {
        $testCaseConfig = [
            'localeRouter' => [
                'defaultLocale' => static::LANGUAGE_DE,

                'languages' => ['root' => static::LANGUAGE_DE, 'en' => static::LANGUAGE_EN],

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
        //$serviceLocator->setFactory('HttpRouter', \Laminas\Router\Http\HttpRouterFactory::class);
        //$serviceLocator->setFactory(TreeRouteStack::class, \Laminas\Router\Http\HttpRouterFactory::class);
        //$serviceLocator->setFactory('RoutePluginManager', \Laminas\Router\RoutePluginManagerFactory::class);
        //$serviceLocator->setFactory(\Laminas\Router\RouteStackInterface::class, \Laminas\Router\RouteStackInterface::class);

        $serviceLocator->configure($moduleConfig['service_manager']);

        return $serviceLocator;
    }
}
