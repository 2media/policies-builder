# PHP Package to generate policies for websites

[![Latest Version on Packagist](https://img.shields.io/packagist/v/twomedia/policies-builder.svg?style=flat-square)](https://packagist.org/packages/twomedia/policies-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/twomedia/policies-builder/Tests?label=tests)](https://github.com/twomedia/policies-builder/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/twomedia/policies-builder.svg?style=flat-square)](https://packagist.org/packages/twomedia/policies-builder)


A PHP package to build and generate policies (Terms of Service, Privacy Policy, Imprint, Terms of Participations) for landingpages and websites.

## Installation

You can install the package via composer:

```bash
composer require twomedia/policies-builder
```

## Usage

### Jigsaw

Internally some HTTP requests to APIs and resources will be cached to ensure faster build times.
As Jigsaw doesn't expose a Cache system like in a normal Laravel application, we have to do it ourselves. Add the following line to your `bootstrap.php` file to register the register the packages cache into the Jigsaw Container.

```php
$events->beforeBuild(\Twomedia\PoliciesBuilder\Cms\Jigsaw\Listeners\RegisterCacheInContainer::class);
```




### Statamic

> TBD

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Stefan Zweifel](https://github.com/stefanzweifel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
