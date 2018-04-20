<?php


namespace LocaleRouterTest\Listener;


use LocaleRouter\Factory\Mvc\Router\Http\LanguageTreeRouteStackDelegatorFactory;
use LocaleRouter\Listener\RouteListener;
use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Module;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Service\PersistStrategyService;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use LocaleRouter\Strategy\StrategyPluginManager;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\I18n\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Router\SimpleRouteStack;
use Zend\ServiceManager\ServiceManager;

class RouteListenerTest extends TestCase
{
    const LANGUAGE_EN = 'en_GB';
    const LANGUAGE_DE = 'de_DE';

    /** @var MvcEvent */
    protected $event;

    /** @var Application */
    protected $app;

    public function setUp()
    {
        $serviceLocator = $this->getServiceLocator();
        $request = new Request();
        $response = new Response();
        $this->app = new Application($serviceLocator, new EventManager(), $request, $response);
        $this->event = new MvcEvent();
        $this->event->setApplication($this->app);
    }

    /*public function testRedirection()
    {
        $serviceManager = $this->getServiceLocator();
        $factory                      = new LanguageTreeRouteStackDelegatorFactory();
        $router = $factory->__invoke($serviceManager, 'HttpRouter', function () {
            return LanguageTreeRouteStack::factory();
        }, null);

        $request = new Request();
        $request->setUri('/en/test/test2');

        $response = new Response();
        $redirectResponse = new Response();
        $translator = new Translator(new \Zend\I18n\Translator\Translator());
        $persistStrategyService = new PersistStrategyService;
        $serviceManager->setService(RouteListener::class, new RouteListener($router, $request, $translator, $persistStrategyService));

        $eventManager = new EventManager();

        $app = new Application($serviceManager, $eventManager, $request, $response);

        $event = new MvcEvent();
        $event->setName(MvcEvent::EVENT_ROUTE);
        $event->setApplication($app);
        $event->setRequest($request);
        $event->setResponse($response);

        $module = new Module();
        $module->onBootstrap($event);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function ($e) {
            return 'test';
        });

        $response = $eventManager->triggerEvent($event);
        $redirectToLanguageUri = $response->first();
        $test2 = $response->last();
        $this->assertEquals($redirectToLanguageUri->getStatusCode(), 302);
        $response->next();
        $this->assertSame('test', $response->last());

        $this->assertEquals('de_DE', \Locale::getDefault());
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