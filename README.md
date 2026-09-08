# 2media Policies Builder

[![Tests](https://github.com/2media/policies-builder/actions/workflows/run-tests.yml/badge.svg)](https://github.com/2media/policies-builder/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/2media/policies-builder/actions/workflows/laravel-pint-fixer.yml/badge.svg)](https://github.com/2media/policies-builder/actions/workflows/laravel-pint-fixer.yml)

A PHP package to build and generate policies for landingpages and websites.
It currently supports the following policies:

- Terms of Service
- Imprint
- Privacy Policy
- Conditions of Participation

```php
'policies' => PoliciesConfiguration::make()
    ->languages(['de', 'fr', 'it', 'en'])
    ->domain('example.ch')
    ->brand('2media')
    ->snapshotPath('resources/policies-snapshot')
    ->types([
        TermsOfService::make(),
        Imprint::make()
            ->imageCopyrights([
                Copyright::make('Picasso', 'Adobe Stock', 'Hero Image 1'),
            ]),
    ]),
```

## Installation
The package can be installed via composer.

```shell
composer require 2media/policies-builder
```

## Usage

### Jigsaw

Before you can start generating policies with the package, you need to first configure your Jigsaw project.

#### Collection & Template

Add a new remote collection to your project. Add the following line to your projects `config.php`. (If you already use collections in your project, only add the `policies` key to your existing `collections`-array.)

```php
'collections' => [
    'policies' => [
        'items' => fn(Collection $config) => (new PoliciesCollection())->generate($config),
    ],
],
```

This remote collection will be responsible for generating all configured policies.

In addition create a new layout file under `source/_layouts.policy`. All generated policies will extend this layout file.

#### Policies Configuration

Next, add a `policies` key to your projects `config.php` with a `PoliciesConfiguration` instance.

```php
'policies' => PoliciesConfiguration::make()
    ->languages(['de'])
    ->domain('example.com')
    ->brand('2media')
    ->snapshotPath('resources/policies-snapshot')
    ->types([
        // Policies Objects
    ]),
```

The `PoliciesConfiguration`-object holds configuration values which are used by all policies. See below for supported methods.

##### `languages([])`

**Required**. Accepts an array of ISO-639-1 language codes for which policies should be generated.

We currently support:

- `de` (German)
- `fr` (French)
- `it` (Italian)
- `en` (English)
- `es` (Spanish)
- `pt` (Portuguese)
- `sr` (Serbian)
- `sq` (Albanian)
- `tr` (Turkish)
- `pl` (Polish)

##### `domain(string)`

**Required**. The domain of the project.

##### `brand(string)`

Optional. Defaults to `2media`. Define for which brand the policies should be generated. Depending on the brand, different policies are generated.

##### `variant(string)`

Optional. Defaults to `default`. Define which variant of policies you would like use in this project.

> TODO: Needs better documentation and examples.

##### `types([])`

**Required**. An array of configured policies. See [Supported Policies](#supported-policies) for details.

##### `snapshotPath(string)`

**Required**. Path to the directory holding your committed policy/translation snapshot. See [Snapshot Mode](#snapshot-mode) below.


#### Supported Policies

The following policies can currently be built with this package.

##### Terms of Service

By adding the following policy to the `type()` method of the `PoliciesConfiguration` a terms of service policy is being generated.

```php
TermsOfService::make(),
```

If the website is operated in cooperation with a different company, use the `inCooperationWith()` method to indicate this in the terms of service. (Note that not all variants support this feature.)

```php
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;

TermsOfService::make()
    ->inCooperationWith(CooperationPartner::make(
        'Legal Name',
        'Name',
        'https://example.com'
    ));
```

If the website is operated on behalf of a different company, use the `onBehalfOf()` method to indicate this in the terms of service. (Note that not all variants support this feature).

```php
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;

TermsOfService::make()
    ->onBehalfOf(CooperationPartner::make(
        'Legal Name',
        'Name',
        'https://example.com'
    ));
```

##### Imprint

To generate imprints, add the following code to the `type()` method of the `PoliciesConfiguration`.
Optionally, the package also generates an image copyright section for you.

```php
Imprint::make()
    ->imageCopyrights([
        Copyright::make('Author', 'source.com', 'Internal Note'),
    ]),
```


**`imageCopyrights([Stringable])`**

Optional. Use the `imageCopyrights()` method and the `Copyright`-object to define the image copyrights of the project. If the project contains Icons which you do not want to list each on it's own use the `IconCopyright`-object.

**Please always use the domain of the source instead of its name.**

(Note the example below assumes you use PHP 8.0 and [Named Arguments](https://stitcher.io/blog/php-8-named-arguments))

```php
// © Picasso / unsplash.com
Copyright::make(author: 'Picasso', source: 'unsplash.com', description: 'Hero Image');

// Icons © thenounproject.com
IconCopyright::make(source: 'thenounproject.com');
```

If none of the above classes solve the Copyright question for your project, feel free to create your own Copyright class. It just needs to implement the `Twomedia\PoliciesBuilder\Contracts\Stringable` interface – meaning just add a `__toString` method to your class.

```php
new class() implements Stringable {
    public function __toString()
    {
        return 'Anonymous Copyright Class';
    }
}
```

##### Privacy Policy

By adding the following policy to the `type()` method of the `PoliciesConfiguration` a privacy policy is being generated.

```php
PrivacyPolicy::make(),
```

*There are currently no specific configuration options available for `PrivacyPolicy`.*

##### Conditions of Participation

To generate a "Conditions of Participation" policy for competition campaigns, add the following block to the `type()` method of the `PoliciesConfiguration`. 

```php
ConditionsOfParticipation::make();
```

**`closingDate(string)`**

Use the `closingDate()` method to pass the end date of the competition to the policy.

```php
ConditionsOfParticipation::make()
    ->closingDate('31.12.2030');
```


#### Global Translations

Instead of defining the translations for the names of the policies ("Impressum", "Conditions d’utilisation") for the policies in your Jigsaw project, you can use the `GlobalTranslator` that comes with the package. It reads translation strings from a committed JSON snapshot (see [Snapshot Mode](#snapshot-mode) below) — no network access is involved.

**Add `transGlobal` function**
Add the following line to your projects `config.php` to expose the `GlobalTranslator` in your projects blade templates.

```php
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;
use Twomedia\PoliciesBuilder\Translations\LocalSnapshotTranslationSource;

'transGlobal' => function ($page, $key, array $replace = []) {
    $translator = new GlobalTranslator(new LocalSnapshotTranslationSource('resources/policies-snapshot'));

    return $translator->trans($page, $key, $replace);
},
```

In your templates, you can now use `transGlobal()` method to get translated strings for all the policies.

```blade
{{ $page->transGlobal('global.imprint') }}
{{ $page->transGlobal('global.terms') }}
{{ $page->transGlobal('global.privacy') }}
{{ $page->transGlobal('global.conditions_of_participation') }}
```

**Available Translations Keys**

The following translations keys are currently available:

- `global.imprint`
- `global.terms`
- `global.privacy`
- `global.conditions_of_participation`

#### Snapshot Mode

`PoliciesCollection` and `GlobalTranslator` read policy content and translations from a committed JSON snapshot. No network access is involved at build time.

**1. Opt in via `PoliciesConfiguration`**

```php
'policies' => PoliciesConfiguration::make()
    ->languages(['de', 'fr'])
    ->domain('example.ch')
    ->snapshotPath('resources/policies-snapshot')
    ->types([
        // ...
    ]),
```

No changes to your `collections` wiring are needed — `PoliciesCollection` reads from the snapshot once `snapshotPath()` is set.

**2. Provide the snapshot files**

Under the configured path, provide:

- `resources/policies-snapshot/policies/{locale}/{policy_type}.json` — one file per resolved policy: `policy_type`, `locale`, `meta_title`, `meta_description`, `content`, `path`.
- `resources/policies-snapshot/translations/{locale}.json` — the global translation strings used by `transGlobal()` (key → value map).

Commit these files to your repository.

> **Note:** versions of this package up to `v1.10.x` included a `bin/policies-snapshot` command and a remote (webservice-backed) mode that could generate these files for you automatically. As of `v2.0.0`, that remote code has been removed — the webservice it depended on (`v2.webservice.apy.ch`) has been decommissioned. If you need to update policy content or translations going forward, edit the committed JSON snapshot files directly.

### Statamic

> The package currently doesn't support Statamic yet.

## Install local version in a project

If you're working on the package locally and want to test thing in a demo project you can use the [composer path-repository format](https://getcomposer.org/doc/05-repositories.md#path).
Add the following snippet to the `composer.json` in your demo project.

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/policies-builder/",
            "options": {
                "symlink": true
            }
        }
    ],
}
```

And "install" the package with `composer require 2media/policies-builder` or `composer update 2media/policies-builder`. The package should now be symlinked in your demo project.

## Testing

```shell
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
- [Lena Fuchs](https://github.com/mlfuchs)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
