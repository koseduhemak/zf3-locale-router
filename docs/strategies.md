# Extract strategies
The following strategies can be used to extract locale information.

## Asset strategy
This strategy is used to prevent the rewriting of Assets to an locale aware URI.

- config key: `extract-asset`
- options:
  - file_extensions (array): Specify file extensions which you don't want being rewritten to locale aware URIs. Default value: `'js', 'css', 'jpg', 'jpeg', 'gif', 'png'`.

F.e. if you don't want the URIs of your assets being redirected to locale aware URI apply the following config:

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

This prevents the URI `http://www.example.com/my/asset.css` from being rewritten to `http://www.example.com/LOCALE/my/asset.css`.


## User identity strategy
- config key: `extract-useridentity`

This strategy tries to extract locale information from a given logged in user. You have to specify a valid `authService` (which implements `Zend\Authentication\AuthenticationServiceInterface`) in your config:
```
'localeRouter' => [
    'extractStrategies' => [
        'extract-useridentity'
    ],    
    'authService' => 'zfcuser_auth_service', // for example ZfcUser's authservice (see: https://github.com/ZF-Commons/ZfcUser)
];
```

You also need to implement `LocaleRouter\Entity\LocaleUserInterface` in your user entity/model. To do so, you can just use the provided `LocaleRouter\Entity\LocaleUserTrait.php`.

## Uripath strategy
- config key: `extract-uripath`

This strategy tries to parse the locale from the provided request URI:
```
'localeRouter' => [
    // configure default language
    'defaultLocale' => 'de_DE',
    
    // configure supported languages
    'languages' => ['de' => 'de_DE', 'nl' => 'nl_NL'],
    
    'extractStrategies' => [
        'extract-uripath'
    ],    
];
```

Example:

```
Request URI: http://www.example.com/de/my/uri
=> resolves to "de_DE".

Request URI: http://www.example.com/nl/my/uri
=> resolves to "nl_NL".
```

## Query strategy
- config key: `extract-query`
- options
  - paramName (string): name of the parameter used for the detection. Default value: `lang`.

Similar to [Uripath strategy](#uripath-strategy), but tries to extract the locale from a query string:
```
'localeRouter' => [
    // configure default language
    'defaultLocale' => 'de_DE',
    
    // configure supported languages
    'languages' => ['de' => 'de_DE', 'en' => 'en_US'],
    
    'extractStrategies' => [
        [
            'name' => 'extract-query',
            'options' => [
                'paramName' => 'myLangParam'
            ]
        ],
    ],    
];
```

Example:
```
Request URI: http://www.example.com/my/uri?myLangParam=de
=> resolves to "de_DE".

Request URI: http://www.example.com/my/uri?myLangParam=de_DE
=> resolves to "de_DE".

Request URI: http://www.example.com/my/uri?myLangParam=en
=> resolves to "en_US".

Request URI: http://www.example.com/my/uri?myLangParam=en_US
=> resolves to "en_US".
```


## AcceptLanguage strategy
- config key: `extract-acceptlanguage`

This strategy tries to extract the locale information from the given Accept-Language header.

```
'localeRouter' => [
    // configure default language
    'defaultLocale' => 'de_DE',
    
    // configure supported languages
    'languages' => ['de' => 'de_DE', 'en' => 'en_US'],
    
    'extractStrategies' => [
        'extract-acceptlanguage'
    ],    
];
```

Example header:
```
Accept-Language: de-DE, en;q=0.8, fr;q=0.7, *;q=0.5
=> resolves to "de_DE"

Accept-Language: en;q=0.8, fr;q=0.7, *;q=0.5
=> resolves to "en_US"
```


## Host strategy
- config key: `extract-host`
- options:
  - domain (string): specify your domain schema in respect of the locale which should be used. F.e. if you have multiple top-level-domains (TLDs), one for each language, you can specify `www.example.:locale` to tell the module to use the TLD for locale recognition.
  It is also possible to use differen subdomains per locale (`:locale.example.com`).
  - aliases (array): map TLDs or subdomains to locales.

This strategy extracts locale information from TLDs or subdomain of the request URI.
TLDs have to be mapped to a locale. F.e. if you want the locale for your `.com` TLD to be `en_US`, you have to map `'com' => 'en_US'`:

```
'localeRouter' => [
    // configure default language
    'defaultLocale' => 'de_DE',
    
    // configure supported languages
    'languages' => ['de' => 'de_DE', 'en' => 'en_US'],
    
    'extractStrategies' => [
        [
            'name'    => 'extract-host',
            'options' => [
                'domain'  => 'www.example.:locale'
                'aliases' => ['com' => 'en_US', 'co.uk' => 'en_GB', 'de' => 'de_DE'],
            ],
        ],
    ],
],
```

## Cookie strategy
- config key: `extract-cookie`
- options:
  - cookieName (string): The name of the cookie. Default value: `localeRouter`.

This strategy tries to extract the locale information from a cookie.

```
'localeRouter' => [
    // configure default language
    'defaultLocale' => 'de_DE',
    
    // configure supported languages
    'languages' => ['de' => 'de_DE', 'en' => 'en_US'],
    
    'extractStrategies' => [
        [
            'name'    => 'extract-cookie',
            'options' => [
                'cookieName'  => 'myCookie'
            ],
        ],
    ],
],
```

If a cookie with a locale was set previously, this strategy will read the correpsonding cookie.

# Persist strategies
The following strategies can be used to persist locale information. 

## Doctrine strategy
- config key: `persist-doctrine` or `LocaleRouter\Strategy\Persist\DoctrineStrategy::class`

This strategy does persist the extracted locale to a user entity. F.e. you want to save locale information per user in your application, you can first use one of the [extract strategies](#extract-strategies) to extract the locale and then save that information to the current user entity.
It is the counterpart of the extract [Useridentity strategy](#user-identity-strategy).
You have to specify a valid `authService` (which implements `Zend\Authentication\AuthenticationServiceInterface`) in your config (because the strategy retrieves the user entity of the logged in user from the auth service).
You also need to implement `LocaleRouter\Entity\LocaleUserInterface` in your user entity/model. To do so, you can just use the provided `LocaleRouter\Entity\LocaleUserTrait.php`.

```
'localeRouter' => [
    'persistStrategies' => [
        'persist-doctrine'
    ],    
    'authService' => 'zfcuser_auth_service', // for example ZfcUser's authservice (see: https://github.com/ZF-Commons/ZfcUser)
];
```

## Cookie strategy (Persist)
- config key: `persist-cookie` or `LocaleRouter\Strategy\Persist\CookieStrategy::class`

This strategy does persist the extracted locale within a cookie.
It is the counterpart of the extract [Cookie strategy](#cookie-strategy).
Make sure if you use the custom `cookieName`-parameter to configure both strategies with the same parameter name.

```
'localeRouter' => [
    'persistStrategies' => [
        [
            'name'    => 'persist-cookie',
            'options' => [
                'cookieName'  => 'myCookie'
            ],
        ],
    ], 
    'authService' => 'zfcuser_auth_service', // for example ZfcUser's authservice (see: https://github.com/ZF-Commons/ZfcUser)
];
```


# Example Config
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
        'extract-uripath',
        'extract-host',
        'extract-useridentity',
        'extract-cookie',
        'extract-acceptlanguage'
    ],

    // Adding persist strategies
    'persistStrategies' => [
        LocaleRouter\Strategy\Persist\DoctrineStrategy::class,
        LocaleRouter\Strategy\Persist\CookieStrategy::class,
    ],

    // configure auth service (for Extract/UserIdentityStrategy or Persist/DoctrineStrategy)
    'authService' => 'zfcuser_auth_service' // for example ZfcUser's authservice (see: https://github.com/ZF-Commons/ZfcUser)
];
```