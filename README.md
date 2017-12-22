#LocaleRouter
[![Build Status](https://travis-ci.org/koseduhemak/zf3-locale-router.svg?branch=master)](https://travis-ci.org/koseduhemak/zf3-locale-router)
[![Coverage Status](https://coveralls.io/repos/github/koseduhemak/zf3-locale-router/badge.svg?branch=master)](https://coveralls.io/github/koseduhemak/zf3-locale-router?branch=master)

This module is intended to implement uris like `http://www.example.com/de/path/path2` or `http://www.example.com/en/path/path2`.
To achieve this, multiple strategies can be used to extract the best suited locale for a user.
If you need to extract the current locale, you can use `\Locale::getDefault();` to return the current set locale (f.e. `de_DE`).

The strategies are processed in the order you configured them and is stopped as soon as the locale could be extracted.

## Extract strategies
The following strategies can be used to extract locale information.

### Asset strategy
This strategy is used to prevent the rewriting of Assets to an locale aware uri.

- config key: `extract-asset`
- options:
  - file_extensions (array): Specify file extensions which you don't want being rewritten to locale aware uris. 

F.e. if you don't want the uris of your assets being redirected to locale aware uri apply the following config:

```
'localeRouter' => [
    'extractStrategies' => [
        [
            'name' => 'extract-asset',
            'options' => [
                'file_extensions' => [
                    'js', 'css', 'jpg', 'jpeg', 'gif', 'png'
                ]
            ]
        ],
        ...
    ]
]
```

This prevents the uri `http://www.example.com/my/asset.css` from being rewritten to `http://www.example.com/LOCALE/my/asset.css`.


### User identity strategy
- config key: `extract-useridentity`

### Uripath strategy
- config key: `extract-uripath`

### Query strategy
- config key: `extract-query`

### AcceptLanguage strategy
- config key: `extract-acceptlanguage`

### Host strategy
- config key: `extract-host`

### Cookie strategy
- config key: `extract-cookie`


## Persist strategies
The following strategies can be used to persist locale information. 

## Config
```php
$settings = [
    // configure default language
    'defaultLocale' => 'de_DE',

    // configure supported languages
    'languages' => ['de' => 'de_DE', 'en' => 'en_GB'],

    // Adding extract strategies
    'extractStrategies' => [
        'extract-asset',
        'extract-query',
        [
            'name'    => LocaleRouter\Strategy\Extract\UriPathStrategy::class,
            'options' => [
                'redirect_when_found' => true,
            ],
        ],
        'extract-cookie',
        'extract-acceptlanguage',
    ],

    // Adding persist strategies
    'persistStrategies' => [
        LocaleRouter\Strategy\Persist\DoctrineStrategy::class,
        LocaleRouter\Strategy\Persist\CookieStrategy::class,
    ],

    // configure auth service (for Extract/UserIdentityStrategy or Persist/DoctrineStrategy)
    'authService' => 'zfcuser_auth_service',
];
```


