<?php

namespace LocaleRouter\Strategy\Extract;

use LocaleRouter\Entity\LocaleUserInterface;
use LocaleRouter\Model\StrategyResultModel;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\RequestInterface;

class UserIdentityStrategy extends AbstractExtractStrategy
{
    /** @var AuthenticationService */
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
}
