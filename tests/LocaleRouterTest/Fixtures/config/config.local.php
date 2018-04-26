<?php

return [
    'localeRouter' => [
        'defaultLocale' => 'de_DE',

        'languages' => ['de' => 'de_DE', 'en' => 'en_GB'],

        'extractStrategies' => [
            'extract-asset',
            'extract-query',
            [
                'name'    => \LocaleRouter\Strategy\Extract\UriPathStrategy::class,
                'options' => [
                    'redirect_when_found' => true,
                ],
            ],
            'extract-cookie',
            'extract-acceptlanguage',
        ],

        'persistStrategies' => [
            \LocaleRouter\Strategy\Persist\CookieStrategy::class,
        ],
    ],
];
