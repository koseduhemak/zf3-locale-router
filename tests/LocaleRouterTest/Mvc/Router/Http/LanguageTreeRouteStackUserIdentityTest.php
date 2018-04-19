<?php

namespace LocaleRouterTest\Mvc\Router\Http;

use LocaleRouter\Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory;
use LocaleRouter\Factory\Strategy\StrategyPluginManagerFactory;
use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Strategy\Extract\CookieStrategy;
use LocaleRouter\Strategy\Extract\QueryStrategy;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use LocaleRouter\Strategy\StrategyPluginManager;
use PHPUnit\Framework\TestCase;
use Zend\Http\Header\AcceptLanguage;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class LanguageTreeRouteStackUserIdentityTest extends TestCase
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

    public function testRedirectFromENToRoot()
    {
        $request = new Request();
        $request->setUri('/en/test/test2?locale=en_GB');

        $this->languageTreeRouteStack->match($request);
        $redirect   = $this->languageTreeRouteStack->getRedirect();

        // should be static::LANGUAGE_DE, because we added mock of useridentity-strategy before uripathstrategy
        $this->assertEquals(static::LANGUAGE_DE, $this->languageTreeRouteStack->getLastMatchedLocale());

        // redirect should not contain "/en/"
        $this->assertEquals('/test/test2', $redirect);

        $locale = \Locale::getDefault();
        $this->assertEquals('de_DE', $locale);
    }

    protected function getServiceLocator(array $config = [])
    {
        $testCaseConfig = [
            'localeRouter' => [
                'defaultLocale' => static::LANGUAGE_DE,

                'languages' => ['root' => static::LANGUAGE_DE, 'en' => static::LANGUAGE_EN],

                'extractStrategies' => [
                    'extract-asset',
                    'extract-query',
                    'extract-useridentity',
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

        // mock useridentity strategy
        $mock = $this->getMockBuilder(UserIdentityStrategy::class)->disableOriginalConstructor()->getMock();
        $result = new StrategyResultModel();
        $result->setLocale('de_DE');
        $mock->expects($this->any())->method('extractLocale')->willReturn($result);

        $serviceLocator->get(StrategyPluginManager::class)->setAllowOverride(true);
        $serviceLocator->get(StrategyPluginManager::class)->setService(UserIdentityStrategy::class, $mock);

        return $serviceLocator;
    }
}
