<?php

$settings = [
    // configure default language
    //'defaultLocale' => 'de_DE',

    // configure supported languages
    // root key is special key. If you want example.com/myuri to be nl_NL (without "nl" segment in uri path, then you need to set the language for the "root" key)
    //'languages' => ['root' => 'nl_NL', 'de' => 'de_DE', 'en' => 'en_GB'],

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
        'extract-uripath',
        'extract-useridentity',
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
