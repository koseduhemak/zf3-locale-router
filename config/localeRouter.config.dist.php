<?php

$settings = [
    // configure default language
    //'defaultLocale' => 'de_DE',

    // configure supported languages
    // root key is special key. If you want example.com/myuri to be nl_NL (without "nl" segment in uri path, then you need to set the language for the "root" key)
    //'languages' => ['root' => 'nl_NL', 'de' => 'de_DE', 'en' => 'en_GB'],

    // specify "path" if you want urls getting redirected to /en or /de scheme
    // if anything other than path is used, f.e. domain, urls not getting any language path ("/en") added
    // 'urlIdentifier' => 'domain',

    // configure view helper
    // specify urls linked to the configured languages
    // required fields: host + scheme
    /*'links' => [
        'de_DE' => ['host' => 'mydomain.de', 'scheme' => 'https']
        'en_GB' => ['host' => 'mydomain.co.uk', 'scheme' => 'https']
    ],*/

    // is used for sitemap generation
    // 'xdefault' => ['host' => 'mydomain.co.uk', 'scheme' => 'https']

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
        [
            'name' => 'extract-host',
            'options' => [
                'domain' => 'mydomain.:locale',
                'aliases' => ['co.uk' => 'en_GB', 'de' => 'de_DE'],
            ],
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
