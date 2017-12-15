<?php

namespace LocaleRouter\Factory\Strategy\Persist;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use LocaleRouter\Options\LanguageOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserIdentityStrategyFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        /** @var LanguageOptions $languageOptions */
        $languageOptions = $container->get(LanguageOptions::class);

        $authServiceIdentifier = $languageOptions->getAuthService();

        if ($container->has($authServiceIdentifier)) {
            $authService = $container->get($authServiceIdentifier);
        } else {
            throw new \InvalidArgumentException(
                'The authentication service "' . $authServiceIdentifier
                . '" was not found. If you want to use this strategy, make sure to configure an authentication service.'
            );
        }

        return new $requestedName($authService);
    }
}
