<?php


namespace LocaleRouterTest\Strategy\Extract;

use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Model\StrategyResultModel;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use PHPUnit\Framework\TestCase;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Http\Request;

class UserIdentityStrategyTest extends TestCase
{
    /** @var UserIdentityStrategy */
    private $strategy;

    public function setUp() : void
    {
        $languageOptions = new LanguageOptions();
        $this->strategy  = new UserIdentityStrategy($languageOptions);
    }

    public function testLocaleDetection()
    {
        // setup mock objects
        $userMockValidLocale = $this->createMock(LocaleUserInterface::class);
        $userMockValidLocale->expects($this->any())->method('getLocale')->will($this->returnValue('de_DE'));

        $authServiceMock = $this->createMock(AuthenticationServiceInterface::class);
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userMockValidLocale));
        $this->strategy->setAuthService($authServiceMock);

        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertEquals($locale->getLocale(), 'de_DE');
    }

    public function testCannotDetectLocale()
    {
        // setup mock objects
        $userMockValidLocale = $this->createMock(LocaleUserInterface::class);
        $userMockValidLocale->expects($this->any())->method('getLocale')->will($this->returnValue(null));

        $authServiceMock = $this->createMock(AuthenticationServiceInterface::class);
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userMockValidLocale));
        $this->strategy->setAuthService($authServiceMock);

        $request = new Request();
        $request->setUri('http://www.example.com/de/test/test2?lang=en');

        $baseurl = '';
        $locale  = $this->strategy->extractLocale($request, $baseurl);

        $this->assertInstanceOf(StrategyResultModel::class, $locale);
        $this->assertNull($locale->getLocale());
    }
}
