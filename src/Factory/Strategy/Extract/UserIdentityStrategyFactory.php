<?php

namespace LocaleRouter\Factory\Strategy\Extract;

use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use LocaleRouter\Strategy\Extract\UserIdentityStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserIdentityStrategyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var LanguageOptions $languageOptions */
        $languageOptions = $container->get(LanguageOptions::class);

        /** @var UserIdentityStrategy $class */
        $class = new $requestedName($languageOptions);

        $authServiceIdentifier = $languageOptions->getAuthService();

        if ($container->has($authServiceIdentifier)) {
            $authService = $container->get($authServiceIdentifier);

            $class->setAuthService($authService);
        } else {
            throw new \InvalidArgumentException(
                'The authentication service "' . $authServiceIdentifier
                . '" was not found. If you want to use this strategy, make sure to configure an authentication service.'
            );
        }

        return $class;
    }
}
