# Installation

Requires PHP 8.3 or later and Yii `~2.0.55 || ^22.0@dev`.

Configure the consuming application's root `composer.json` before requiring the extension:

```json
{
  "repositories": [{ "type": "composer", "url": "https://asset-packagist.org" }],
  "config": {
    "allow-plugins": {
      "php-forge/foxy": true,
      "yiisoft/yii2-composer": true
    },
    "foxy": { "manager": "native" }
  }
}
```

```shell
composer require --prefer-dist --prefer-stable php-forge/foxy:^0.4 'yii2-extensions/select2:^0.2@dev'
```

Foxy requires Composer 2.10.2 or later and PHP's `zlib` extension. The native manager downloads the published JavaScript
and CSS; this package does not compile assets and needs no Node.js installation. Composer repositories and plugin
permissions belong to the application root; dependency configuration does not apply there.

For a `config/web.php` one directory below the Composer root:

```php
return [
    'aliases' => [
        '@npm' => dirname(__DIR__) . '/node_modules',
        '@bower' => dirname(__DIR__) . '/vendor/bower-asset',
    ],
];
```

Adjust these paths to the actual project layout. `@npm` must point to Foxy's install directory. Yii 2.0 uses its Bower
assets, including jQuery, from `@bower`; Asset Packagist supplies them. Select2 keeps the `yii\web\JqueryAsset`
dependency because jQuery is required at runtime. Do not load a second jQuery copy after Select2.

Commit the application's `composer.lock`, `package.json`, and `foxy.lock` together with `composer.json`.
`composer install` reuses matching frontend lock versions; `composer update` resolves them again. Foxy owns its install
directory and prunes entries absent from its lock. Keep an application's existing JavaScript manager when it needs build
scripts or nested dependency versions; otherwise use a separate native directory and point `@npm` to that directory. See
[Foxy's native manager reference](https://github.com/php-forge/foxy/blob/0.4.0/docs/config.md#native-manager).

## Yii 22

For the Yii 22 development line, require its client validation adapter and bootstrap it:

```shell
composer require 'yiisoft/yii2:^22.0@dev' 'yiisoft/yii2-jquery:^1.0@dev'
```

```php
return [
    'bootstrap' => [\yii\jquery\Bootstrap::class],
    'aliases' => ['@npm' => dirname(__DIR__) . '/node_modules'],
];
```

The adapter supplies ActiveForm's client scripts. Select2 itself still uses jQuery.

## Select2 and jQuery compatibility

The extension keeps Select2 `~4.0.13` to preserve its pass-through 4.0 API. Select2 4.1 removes `initSelection` and
`query` compatibility and renames `containerCssClass` to `selectionCssClass`; review custom templates and 4.1-only
options before changing an existing application's frontend version. See the
[official 4.1 release notes](https://github.com/select2/select2/releases/tag/4.1.0).

Yii 22 also constrains the shared jQuery version. Resolve its installed `package.json` before changing the frontend
graph: Foxy's native manager needs one version satisfying every package. The Select2 pin preserves the 4.0 API; it does
not imply that Yii itself requires a Select2 version.

## Bootstrap 5

Bootstrap is optional, and this package never loads Bootstrap CSS or detects Bootstrap packages. Without a `theme`,
Select2 uses its own `default` theme. Set the theme explicitly:

```php
'clientOptions' => ['theme' => 'bootstrap-5'],
```

The widget then registers `Yii2\Extensions\Select2\Themes\Select2BS5ThemeAsset`, which publishes the stylesheet of the
optional `select2-bootstrap-5-theme` frontend package after Select2's CSS. The application keeps loading Bootstrap 5 CSS
itself, for example through `yii\bootstrap5\BootstrapAsset` in its layout or `AppAsset`.

Inside a Bootstrap 5 `.input-group`, also set `'width' => 'style'`. That theme sizes the container with `flex-grow`, so
the default `100%` width would wrap it onto its own line.

## Bootstrap 6

For applications using [`yii2-extensions/bootstrap6`](https://github.com/yii2-extensions/bootstrap6), set:

```php
'clientOptions' => ['theme' => 'bootstrap-6'],
```

The widget registers `Yii2\Extensions\Select2\Themes\Select2BS6ThemeAsset`. This package ships the stylesheet: it
renders the selection like a Bootstrap 6 `.form-control` and reads Bootstrap's design tokens, so sizes, validation
states and color schemes follow the loaded Bootstrap 6 CSS. Bootstrap 6 CSS itself comes from the application's
`yii2\extensions\bootstrap6\asset\BootstrapAsset`.

Theme stylesheets do not depend on the Bootstrap CSS order. To load Bootstrap first anyway, configure the theme bundle's
dependencies:

```php
'components' => [
    'assetManager' => [
        'bundles' => [
            \Yii2\Extensions\Select2\Themes\Select2BS6ThemeAsset::class => [
                'depends' => [
                    \Yii2\Extensions\Select2\Select2Asset::class,
                    \yii2\extensions\bootstrap6\asset\BootstrapAsset::class,
                ],
            ],
        ],
    ],
],
```

The `select2-bootstrap-5-theme` frontend package requires Bootstrap 5, so this package declares it as optional. When the
application installs Bootstrap 6, Foxy's native manager skips it with a warning and installation continues; do not use
the `bootstrap-5` theme in that application.

## Verify installation

Run the [usage examples](examples.md), then check that jQuery, Select2's full build, and the `yiiSelect2` plugin load
successfully and that opening, selecting, clearing, and submitting a field works. Missing `@npm/select2/dist` means
frontend assets were not installed or `@npm` points at another directory.

[Back to documentation](../README.md#documentation)
