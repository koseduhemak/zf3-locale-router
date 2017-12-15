<?php


namespace LocaleRouter\Strategy\Persist;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Stdlib\ResponseInterface;
use ZF2LanguageRoute\Entity\LocaleUserInterface;

class DoctrineStrategy extends AbstractPersistStrategy
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var AuthenticationServiceInterface */
    protected $authService;

    /**
     * DoctrineStrategy constructor.
     *
     * @param EntityManager $entityManager
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(EntityManager $entityManager,
        AuthenticationServiceInterface $authService
    ) {
        $this->entityManager = $entityManager;
        $this->authService   = $authService;
    }

    public function save($locale, ResponseInterface $response)
    {
        if ($this->authService->hasIdentity()) {
            $user = $this->authService->getIdentity();
            if ($user instanceof LocaleUserInterface
                && $locale !== $user->getLocale()
            ) {
                $user->setLocale($locale);

                $this->entityManager->persist($user);
                $this->entityManager->flush($user);
            }
        }

        return $response;
    }
}
