<?php

namespace LocaleRouterTest\Factory;

use LocaleRouter\Mvc\Router\Http\LanguageTreeRouteStack;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Router\Http\TreeRouteStack;

class FactoryTest extends TestCase
{
    const LANGUAGE_EN = 'en_GB';
    const LANGUAGE_DE = 'de_DE';

    /** @var ContainerInterface */
    protected $serviceManager;

    public function setUp()
    {
        $config            = include 'tests/LocaleRouterTest/Fixtures/config/application.test.config.php';

        $config['module_listener_options']['config_glob_paths'] = [
            'tests/LocaleRouterTest/Fixtures/config/{{,*.}local}.php',
        ];


        $bootstrap            = \Zend\Mvc\Application::init($config);
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
}
