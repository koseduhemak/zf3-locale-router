<?php

namespace LocaleRouterTest\Factory;

use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use LocaleRouter\Strategy\Persist\DoctrineStrategy;
use LocaleRouter\Strategy\StrategyPluginManager;
use PHPUnit\Framework\TestCase;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;

class FactoryTest extends TestCase
{
    const LANGUAGE_EN = 'en_GB';
    const LANGUAGE_DE = 'de_DE';

    /** @var ServiceManager */
    protected $serviceManager;

    public function setUp()
    {
        $config            = include 'tests/LocaleRouterTest/Fixtures/config/application.test.config.php';

        $config['module_listener_options']['config_glob_paths'] = [
            'tests/LocaleRouterTest/Fixtures/config/{{,*.}local}.php',
        ];

        $bootstrap            = \Laminas\Mvc\Application::init($config);
        $this->serviceManager = $bootstrap->getServiceManager();
    }

    public function testFactoryReturnsCorrectInstance()
    {
        $config = include 'config/module.config.php';

        // test factories
        foreach ($config['service_manager']['factories'] as $instance => $factory) {
            $receivedInstance = $this->serviceManager->get($instance);

            $this->assertInstanceOf($instance, $receivedInstance);
        }

        // test delegators and special stuff
        $httpRouter = $this->serviceManager->get('HttpRouter');
        $this->assertInstanceOf(LanguageTreeRouteStack::class, $httpRouter);

        $treeRouteStack = $this->serviceManager->get(TreeRouteStack::class);
        $this->assertInstanceOf(LanguageTreeRouteStack::class, $treeRouteStack);
    }

    public function testUserIdentityStrategy()
    {
        $config                                = $this->serviceManager->get('Config');
        $config['localeRouter']['authService'] = 'zfcuser_auth_service';

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Config', $config);

        $userMockValidLocale = $this->createMock(LocaleUserInterface::class);
        $userMockValidLocale->expects($this->any())->method('getLocale')->will($this->returnValue('de_DE'));

        $authServiceMock = $this->createMock(AuthenticationServiceInterface::class);
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userMockValidLocale));

        $this->serviceManager->setService('zfcuser_auth_service', $authServiceMock);
        $this->serviceManager->setService(AuthenticationService::class, $authServiceMock);

        $userIdentityStrategy =  $this->serviceManager->get(StrategyPluginManager::class)->get(UserIdentityStrategy::class);

        $this->assertInstanceOf(UserIdentityStrategy::class, $userIdentityStrategy);
    }

    public function testDoctrineStrategy()
    {
        $config                                = $this->serviceManager->get('Config');
        $config['localeRouter']['authService'] = 'zfcuser_auth_service';

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('Config', $config);

        $userMockValidLocale = $this->createMock(LocaleUserInterface::class);
        $userMockValidLocale->expects($this->any())->method('getLocale')->will($this->returnValue('de_DE'));

        $authServiceMock = $this->createMock(AuthenticationServiceInterface::class);
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userMockValidLocale));

        $this->serviceManager->setService('zfcuser_auth_service', $authServiceMock);
        $this->serviceManager->setService(AuthenticationService::class, $authServiceMock);

        $doctrineStrategy =  $this->serviceManager->get(StrategyPluginManager::class)->get(DoctrineStrategy::class);

        $this->assertInstanceOf(DoctrineStrategy::class, $doctrineStrategy);
    }
}
