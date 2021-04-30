# 2media Policies Builder

[![Tests](https://github.com/2media/policies-builder/actions/workflows/run-tests.yml/badge.svg)](https://github.com/2media/policies-builder/actions/workflows/run-tests.yml)
[![Psalm](https://github.com/2media/policies-builder/actions/workflows/psalm.yml/badge.svg)](https://github.com/2media/policies-builder/actions/workflows/psalm.yml)
[![Check & fix styling](https://github.com/2media/policies-builder/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/2media/policies-builder/actions/workflows/php-cs-fixer.yml)

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
    ->types([
        TermsOfService::make(),
        Imprint::make()
            ->imageCopyrights([
                Copyright::make('Picasso', 'Adobe Stock', 'Hero Image 1'),
            ]),
    ]),
```

## Installation
The package can be installed via composer. However, as this is a private package, it is not generally available by simply calling `composer require`.

Add a `repositories` key like below to your `composer.json`.

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://packages.2media.ch"
    }
],
```

Now you can install the package by running.

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

##### `domain(string)`

**Required**. The domain of the project.

##### `brand(string)`

Optional. Defaults to `2media`. Define for which brand the policies should be generated. Depending on the brand, different policies are generated.

##### `variant(string)`

Optional. Defaults to `default`. Define which variant of policies you would like use in this project.

> TODO: Needs better documentation and examples.

##### `types([])`

**Required**. An array of configured policies. See [Supported Policies](#supported-policies) for details.


#### Supported Policies

The following policies can currently be built with this package.

##### Terms of Service

By adding the following policy to the `type()` method of the `PoliciesConfiguration` a terms of service policy is being generated.

```php
TermsOfService::make(),
```

*There are currently no specific configuration options available for `TermsOfService`.*

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

If non of the above classes solve the Copyright question for your project, feel free to create your own Copyright class. It just needs to implement the `Twomedia\PoliciesBuilder\Contracts\Stringable` interface – meaning just add a `__toString` method to your class.

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

**`endDate(string)`**

Use the `endDate()` method to pass the end date of the competition to the policy.

```php
ConditionsOfParticipation::make()
    ->endDate('31.12.2030');
```


#### Global Translations

Instead of defining the translations for the names of the policies ("Impressum", "Conditions d’utilisation") for the policies in your Jigsaw project, you can use the `GlobalTranslator` that comes with the package.

The `GlobalTranslator` connects with our Webservice and gets the translations from a central place. If you follow these directions, the HTTP requests won't have any impact on the build time, as the requests/responses are cached for 24 hours on your machine.

**Setup Caching**
As Jigsaw doesn't expose a Cache system like in a normal Laravel application, we have to do it ourselves. Add the following line to your `bootstrap.php` file to register the packages cache into the Jigsaw Container.

```php
$events->beforeBuild(\Twomedia\PoliciesBuilder\Cms\Jigsaw\Listeners\RegisterCacheInContainer::class);
```

**Add `transGlobal` function**
Add the following line to your projects `config.php` to expose the `GlobalTranslator` in your projects blade templates.

```php
'transGlobal' => function ($page, $key, array $replace = []) {
    return Container::getInstance()->make(GlobalTranslator::class)->trans($page, $key, $replace);
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

The key are defined by the Webservice app. You can find the German version of the available keys [here](https://github.com/2media/webservice-neo/blob/master/public/lang/de/policies.json).

### Statamic

> The package currently doesn't support Statamic yet.


## Install the Package in GitHub Actions Workflows
GitHub Actions Workflows of projects where this package is installed will fail to run `composer install` as the package is not public.
To solve this, add the following lines to your GitHub Actions Workflow YAML file **before** `composer install` is executed. ([Example Workflow](https://github.com/2media/jigsaw-starterkit/blob/7197af8ecb2417c01087a87b2b356d5f7a35cf12/.github/workflows/integrate.yml#L16-L34))

```yaml
-   name: Setup composer auth.json
    run: composer config github-oauth.github.com ${{ secrets.COMPOSER_GITHUB_TOKEN }}
```

The `run` command will use a Github Private Access Token – issued by [2media-bot](https://github.com/2media-bot) – to authenticate composer with our private GitHub repositories. (More about this in [the composer docs](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth))

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
