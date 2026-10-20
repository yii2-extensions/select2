---
name: yii2-select2
description:
  Install and integrate yii2-extensions/select2 with Yii ActiveForm, multiple selection, tags, remote data, events, and
  repeatable lifecycle handling.
---

# Yii2 Select2 integration

Use this skill when integrating `yii2-extensions/select2`. The public widget class is `Yii2\Extensions\Select2\Select2`;
do not use the singular `Extension` namespace or another Select2 wrapper's API.

## Read the relevant contract

In a root package checkout, use `composer show --self --format=json` for metadata. For installed dependencies, resolve
the real path and read the configured vendor directory's `composer/installed.php` (`versions[<package>]`):
`install_path`, `pretty_version` and Composer's selected `reference`. Do not blindly prefer `source.reference` over
`dist.reference`. For any live package checkout, including a symlinked path install, inspect its current `HEAD` and
files even when clean; report metadata mismatches and let the actual files govern. Uncommitted changes have no matching
SHA. Otherwise use the selected reference or a verified matching tag only after establishing its source provenance.
Compare `composer.lock`; it may differ from the inspected installation. Identify a lock-only state when no package files
are installed, and resolve dependencies separately. Use hosted pages, catalogs or snapshots only when their source
revision matches.

Resolve `docs/`, `src/`, and `CHANGELOG.md` from that package root, not from the application or copied skill directory.
Start with the `README.md` documentation index and read only the relevant installation, configuration, remote-data, or
upgrade guide. If the installed package lacks guides, use
`https://github.com/yii2-extensions/select2/blob/<verified-reference>/docs/<guide>.md` after verifying the reference's
source provenance. If matching guides are unavailable or offline, inspect local source/PHPDoc and the changelog, and
report the gap. If those are absent too, state that the exact package contract remains unverified. Resolve frontend and
Foxy documentation against their installed versions separately.

## Install the actual browser dependencies

Use PHP >=8.3, Yii `~2.0.55 || ^22.0@dev`, Composer >=2.10.2, and `php-forge/foxy:^0.4`. Configure the consuming
application's root `composer.json`: use `config.foxy.manager=native` for published assets when the app needs no
JavaScript build, and authorize `php-forge/foxy` and `yiisoft/yii2-composer`, and add `https://asset-packagist.org` as a
Composer repository for Yii 2.0's Bower assets. Then require yii2-extensions/select2:^0.2 with `--prefer-stable`.

Point `@npm` at the application's Foxy directory, normally `<Composer root>/node_modules`, and `@bower` at
`<configured vendor directory>/bower-asset` on Yii 2.0. Preserve jQuery: Select2 requires it and depends on Yii's
`JqueryAsset`. Do not add a second copy after Select2. The package consumes precompiled Select2 4.0 assets; native
installation needs PHP `zlib` and does not need Node.js or a build. Preserve an existing JavaScript manager when the
application needs build scripts or dependency layouts that native mode cannot supply. Keep `@npm` aligned with its
actual install directory; use a dedicated directory if selecting native mode. Preserve application locks and keep
`composer.lock`, `package.json`, and `foxy.lock` consistent. This integration task does not itself authorize staging or
committing them. Do not share the native install directory with another installer.

On Yii 22, require `yiisoft/yii2-jquery:^1.0@dev` and add `yii\jquery\Bootstrap::class` to the application's `bootstrap`
for ActiveForm client validation. Keep Select2 `~4.0.13` to preserve the pass-through 4.0 API. Before changing the
frontend graph, inspect the installed Yii manifest's jQuery range and Select2's supported dependencies and removed 4.0
options.

## Render and validate

Define model rules for required values and authorized IDs before writing the view. Normalize Yii's cleared multiple
value `''` to `[]`. Validate array members with `each`, disabling the embedded rule's `skipOnEmpty` when empty members
would bypass its allowlist or type check; validate every free-form tag and the total count. Use
`$model->load(...) && $model->validate()` before persistence. Keep validation errors when redisplaying malformed input,
but pass safe scalar or flat selections through `options.value` so nested values cannot break rendering. Remote choices
and hidden inputs are user input, not authorization.

In an existing ActiveForm view with `$form` and `$model`:

```php
use Yii2\Extensions\Select2\Select2;

echo $form->field($model, 'country')->widget(Select2::class, [
    'items' => ['mx' => 'Mexico', 'ca' => 'Canada'],
    'options' => ['prompt' => '', 'aria-describedby' => 'country-hint'],
    'clientOptions' => ['placeholder' => 'Choose a country', 'allowClear' => true],
]);
```

Provide a real `country-hint` element. For standalone fields use `name`, `value`, and a visible label matching a unique
`options.id`. `options.multiple=true` posts an array; it does not enable tags. Set `clientOptions.tags=true` only for a
field whose server rules and persistence accept free-form values. Redisplay selected tags through `items`. The widget's
public properties are typed (`array $items`, `array|false $clientOptions`, `array $clientEvents`); keep subclass
overrides compatible. `clientOptions.width` defaults to `100%` and `language` to the application language.

The widget adds no CSS class to the `<select>`: Bootstrap ActiveField `inputOptions` supply `form-control` and the
validation class. Pass `options.class` explicitly for standalone widgets that need it.

Bootstrap is optional and never detected or loaded by this package. Choose a theme explicitly:
`clientOptions.theme='bootstrap-5'` registers `Themes\Select2BS5ThemeAsset` (optional `select2-bootstrap-5-theme`
frontend package), `'bootstrap-6'` registers `Themes\Select2BS6ThemeAsset` (stylesheet shipped by this package, built on
Bootstrap 6 tokens). Without `theme`, Select2's `default` theme applies. The application loads Bootstrap CSS itself;
configure `assetManager.bundles[<theme bundle>]['depends']` only to force stylesheet order.

For standalone named multiple selects, set `options.unselect=''` so clearing all selections submits an empty marker;
model-bound selects add it automatically.

## Remote data and events

Put AJAX configuration in `clientOptions.ajax`. Use `yii\web\JsExpression` for function-valued options; ordinary strings
remain strings. Pass the term as `q`, the page as `page`, and use a bounded server page size. Return
`{"results":[{"id":"2","text":"Madrid"}],"pagination":{"more":false}}`. Include authorized preselected IDs and labels in
`items` because AJAX cannot recover their labels from the model value alone. Keep CSRF for POST, authenticate and
authorize real endpoints, validate query types, and report failures through HTTP errors.

`clientEvents` maps event names to trusted JavaScript function strings or `JsExpression` objects. Handlers receive
Select2 events on the original select. Do not interpolate user input into JavaScript callbacks or disable escaping for
remote labels. Multiple widgets need distinct IDs, even for the same model attribute.

## Lifecycle and accessibility

Generated initialization calls `jQuery(element).yiiSelect2(options, events)`, which replaces an existing instance and
binds `clientEvents` once. Before removing or replacing an element, call
`jQuery(document.getElementById(id)).yiiSelect2('destroy')`. It disconnects the observer and removes plugin-managed
events; external handlers remain the application's responsibility. For manual initialization use `clientOptions=false`,
then call `jQuery(document.getElementById(id)).yiiSelect2(options, events)` after the DOM and assets are ready;
`clientEvents` are not bound in that mode. Pass the events again after manual reinitialization. Avoid direct Select2
destruction for a plugin-initialized widget because it bypasses observer cleanup.

With `clientOptions=false`, choose language and theme in manual JavaScript, and register the matching theme bundle in
PHP when using `bootstrap-5` or `bootstrap-6`.

Use visible labels, actual hint/error nodes, and unique IDs. The plugin synchronizes `aria-describedby`, labels,
required state, and `aria-invalid` with the visible selection. Focus the visible selection when ActiveForm reports an
invalid field. Check keyboard navigation, clearing, disabled controls, and error recovery in the application's theme. Do
not claim screen reader or accessibility-standard conformance from DOM checks alone.

## Verify the integration

Run PHP tests, PHPStan, dependency checks, and formatting. Exercise a real browser with desktop and mobile viewports:
submit empty and invalid values, select multiple options, create and reject tags, paginate searches, recover from an
HTTP error, and destroy/reinitialize several times while other widgets retain state. Count event callbacks to detect
duplicate bindings. Keep private evaluation fixtures and results out of public packages.

Reference: [Select2 APIs](https://select2.org/) and the installed Foxy's `docs/config.md#native-manager` in
<https://github.com/php-forge/foxy>, at its resolved reference.
