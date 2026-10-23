# Upgrade Guide

## 0.2.0

- Upgrade PHP to 8.3 or later and Yii to `~2.0.55 || ^22.0@dev`.
- Install the browser assets through Foxy: authorize `php-forge/foxy`, configure its manager and point `@npm` to its
  install directory as shown in the [installation guide](docs/installation.md). Require `npm-asset/select2`,
  `npm-asset/select2-bootstrap-5-theme` or `yii2-extensions/asset-bootstrap5` in the application only if it uses them
  directly.
- Load Bootstrap CSS in the application layout or `AppAsset`; the asset bundles no longer depend on Bootstrap. Configure
  `assetManager.bundles[Select2BS5ThemeAsset::class]['depends']` to force stylesheet order.
- Set `'clientOptions' => ['theme' => 'bootstrap-5']` (or `'bootstrap-6'`) where a Bootstrap theme is wanted; otherwise
  Select2's `default` theme applies. Keep the `select2-bootstrap-5-theme` frontend package installed for `bootstrap-5`.
- Pass `'options' => ['class' => 'form-control']` to standalone widgets that need the class. The default
  `clientOptions.width` is `100%`; set `'width' => 'resolve'` to restore Select2's default, or `'width' => 'style'`
  inside Bootstrap 5 input groups.
- Replace overrides of the removed `registerPlugin()` and `registerClientEvents()` methods. Use
  `jQuery(element).yiiSelect2(options, events)` and `.yiiSelect2('destroy')` for manual initialization and cleanup; with
  `'clientOptions' => false`, pass `clientEvents` to your own call.
- Match the typed properties when extending `Select2`: `array $items`, `array|false $clientOptions`,
  `array $clientEvents` and `run(): string`. Customize the now `final` `Select2Asset` and `Themes\Select2BS5ThemeAsset`
  through `assetManager.bundles`.
- Only one translation file is loaded: the exact regional file when Select2 ships it (`pt-BR.js`), otherwise the base
  language file (`es.js` for `es-MX`).
