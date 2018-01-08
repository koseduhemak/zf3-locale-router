#LocaleRouter
[![Build Status](https://travis-ci.org/koseduhemak/zf3-locale-router.svg?branch=master)](https://travis-ci.org/koseduhemak/zf3-locale-router)
[![Coverage Status](https://coveralls.io/repos/github/koseduhemak/zf3-locale-router/badge.svg?branch=master)](https://coveralls.io/github/koseduhemak/zf3-locale-router?branch=master)

This module is intended to implement URIs like `http://www.example.com/de/path/path2` or `http://www.example.com/en/path/path2` in your ZF3 application.
To achieve this, multiple strategies can be used to extract the best suited locale for a user (and redirect him to the correct uri). This is f.e. necessary for SEO.
If you need to extract the current locale, you can use `\Locale::getDefault();` to return the current set locale (f.e. `de_DE`).

The strategies are processed in the **order you configured** them and is stopped as soon as the locale could be extracted.

Thanks to the creators of the modules https://github.com/basz/SlmLocale and https://github.com/xelax90/zf2-language-route, from which I took a little bit inspiration from.

If you like my module, you can buy me a beer or some coffee: https://www.paypal.me/koseduhemak

## Installation
Install via composer:
```
$ composer require koseduhemak/zf3-locale-router
```

## Usage
Read more about configuration options and different extracting / persisting strategies: [configuration manual](docs/strategies.md).

## Buy me a beer / coffee
If you like my module, you can buy me a beer or some coffee: https://www.paypal.me/koseduhemak