# Upgrade

## From 0.1

- Upgrade every application and CI runtime to PHP 8.3 or later; use Yii `~2.0.55 || ^22.0@dev`.
- Install the browser assets through Foxy: authorize `php-forge/foxy`, configure its manager and point `@npm` to its
  install directory as shown in the [installation guide](docs/installation.md). The package no longer requires
  `npm-asset/select2`, `npm-asset/select2-bootstrap-5-theme` or `yii2-extensions/asset-bootstrap5`; require them in the
  application only if it uses them directly.
- Load Bootstrap CSS in the application layout or `AppAsset`. `Select2Asset` and `Select2BS5ThemeAsset` no longer depend
  on any Bootstrap bundle, and no Bootstrap package is detected. To force stylesheet order, configure
  `assetManager.bundles[Select2BS5ThemeAsset::class]['depends']`.
- Set `'clientOptions' => ['theme' => 'bootstrap-5']` where the Bootstrap 5 theme is wanted. Without `theme`, the widget
  no longer selects `bootstrap-5` or `classic`; Select2's own `default` theme applies. Use `'bootstrap-6'` for
  Bootstrap 6.
- Keep the optional `select2-bootstrap-5-theme` frontend package installed when using `bootstrap-5`. Foxy's native
  manager skips it, with a warning, when the application installs Bootstrap 6.
- Pass `'options' => ['class' => 'form-control']` to standalone widgets that need the class; the widget no longer adds
  it. ActiveField still supplies it through `inputOptions`. The default `clientOptions.width` is now `100%`; set
  `'width' => 'resolve'` to restore Select2's default, and `'width' => 'style'` inside Bootstrap 5 input groups.
- Replace overrides of the removed protected `registerPlugin()` and `registerClientEvents()` methods. Generated scripts
  now call `jQuery(element).yiiSelect2(options, events)`; use the same plugin, and `.yiiSelect2('destroy')`, for manual
  initialization and cleanup.
- With `'clientOptions' => false`, pass the event handlers to your own `yiiSelect2()` call: `clientEvents` are bound
  only by the generated initialization.
- Match the typed properties when extending `Select2`: `array $items`, `array|false $clientOptions`,
  `array $clientEvents`, and `run(): string`.
- Customize `Select2Asset` and `Themes\Select2BS5ThemeAsset` through `assetManager.bundles`; both are now `final`.
- Expect one translation file: the exact regional file when Select2 ships it (`pt-BR.js`), otherwise the base language
  file (`es.js` for `es-MX`). The application language is passed as `clientOptions.language` unless set explicitly.
