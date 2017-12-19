<?php

namespace LocaleRouterTest;

use LocaleRouter\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testCanGetConfig()
    {
        $module = new Module();
        $this->assertTrue(is_array($module->getConfig()));
    }
}
