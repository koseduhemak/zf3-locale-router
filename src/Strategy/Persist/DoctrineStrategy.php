<?php

namespace LocaleRouter\Strategy\Persist;

use Doctrine\Common\Persistence\ObjectManager;
use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Options\LanguageOptions;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Stdlib\ResponseInterface;

class DoctrineStrategy extends AbstractPersistStrategy
{
    /** @var ObjectManager */
    protected $entityManager;

    /** @var AuthenticationServiceInterface */
    protected $authService;

    /**
     * DoctrineStrategy constructor.
     *
     * @param ObjectManager $entityManager
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(LanguageOptions $languageOptions, ObjectManager $entityManager, AuthenticationServiceInterface $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService   = $authService;

        parent::__construct($languageOptions);
    }

    public function save($locale, ResponseInterface $response)
    {
        if (($locale = $this->getLanguage($locale))) {
            if ($this->authService->hasIdentity()) {
                $user = $this->authService->getIdentity();

                if ($user instanceof LocaleUserInterface && $locale !== $user->getLocale()) {
                    $user->setLocale($locale);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush($user);
                }
            }
        }

        return $response;
    }

    public function setStrategyOptions(array $options = [])
    {
    }
}
