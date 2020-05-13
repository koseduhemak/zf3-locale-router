<?php

namespace LocaleRouterTest\Strategy\Persist;

use Doctrine\ORM\EntityManager;
use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Persist\DoctrineStrategy;
use LocaleRouterTest\Mocks\EntityManagerMock;
use LocaleRouterTest\Mocks\UserMock;
use PHPUnit\Framework\TestCase;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Http\Response;

class DoctrineStrategyTest extends TestCase
{
    /** @var DoctrineStrategy */
    private $strategy;

    public function setUp() : void
    {
        // setup mock objects
        $userMockValidLocale = new UserMock();

        $authServiceMock = $this->createMock(AuthenticationServiceInterface::class);
        $authServiceMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userMockValidLocale));

        $entityManagerMock = new EntityManagerMock();

        $languageOptions = new LanguageOptions();
        $this->strategy  = new DoctrineStrategy($languageOptions, $entityManagerMock, $authServiceMock);
    }

    public function testLocaleSaved()
    {
        // test de_DE
        $response = new Response();

        $this->strategy->save('de_DE', $response);

        $entityManagerReflection = new \ReflectionProperty($this->strategy, 'entityManager');
        $entityManagerReflection->setAccessible(true);
        $entityManagerStrategy = $entityManagerReflection->getValue($this->strategy);

        /** @var LocaleUserInterface $identity */
        $identity = $entityManagerStrategy->getPersistedEntity();

        $this->assertEquals('de_DE', $identity->getLocale());

        // test en_US
        $response = new Response();

        $this->strategy->save('en_US', $response);
        $this->assertEquals('en_US', $identity->getLocale());
    }

    public function testLocaleNotSaved()
    {
        // test de_DE
        $response = new Response();

        $this->strategy->save('nl_NL', $response);

        $entityManagerReflection = new \ReflectionProperty($this->strategy, 'entityManager');
        $entityManagerReflection->setAccessible(true);
        $entityManagerStrategy = $entityManagerReflection->getValue($this->strategy);

        /** @var LocaleUserInterface $identity */
        $identity = $entityManagerStrategy->getPersistedEntity();

        $this->assertNull($identity);
    }
}
