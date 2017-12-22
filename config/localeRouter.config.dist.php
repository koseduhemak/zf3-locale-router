<?php

$settings = [
    // configure default language
    //'defaultLocale' => 'de_DE',

    // configure supported languages
    //'languages' => ['de' => 'de_DE', 'en' => 'en_GB'],

    // Adding extract strategies
    /*'extractStrategies' => [
        [
            'name' => 'extract-asset',
            'options' => [
                'file_extensions' => [
                    'js', 'css', 'jpg', 'jpeg', 'gif', 'png'
                ]
            ]
        ],
        'extract-query',
        [
            'name'    => LocaleRouter\Strategy\Extract\UriPathStrategy::class,
            'options' => [
                'redirect_when_found' => true,
            ],
        ],
        'extract-cookie',
        'extract-acceptlanguage',
    ],*/

    // Adding persist strategies
    /*'persistStrategies' => [
        LocaleRouter\Strategy\Persist\DoctrineStrategy::class,
        LocaleRouter\Strategy\Persist\CookieStrategy::class,
    ],*/

    // configure auth service (for Extract/UserIdentityStrategy or Persist/DoctrineStrategy)
    //'authService' => 'zfcuser_auth_service',
];

/**
 * Do not edit below this line!
 */
return [
    'localeRouter' => $settings,
];
