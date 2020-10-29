<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Model\StrategyResultModel;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Stdlib\RequestInterface;

class UserIdentityStrategy extends AbstractExtractStrategy
{
    /** @var AuthenticationServiceInterface */
    protected $authService;

    public function extractLocale(RequestInterface $request, $baseUrl)
    {
        $result = new StrategyResultModel();
        $locale = null;

        if ($this->authService->hasIdentity()) {
            $identity = $this->authService->getIdentity();

            if ($identity instanceof LocaleUserInterface) {
                $locale = $this->getLanguage($identity->getLocale());
            }
        }

        $result->setLocale($locale);

        return $result;
    }

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * @param AuthenticationServiceInterface $authService
     */
    public function setAuthService(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }
}
