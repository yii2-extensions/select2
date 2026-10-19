<p align="center">
    <a href="https://github.com/yii2-extensions/select2" target="_blank">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://www.yiiframework.com/image/design/logo/yii3_full_for_dark.svg">
            <source media="(prefers-color-scheme: light)" srcset="https://www.yiiframework.com/image/design/logo/yii3_full_for_light.svg">
            <img src="https://www.yiiframework.com/image/design/logo/yii3_full_for_light.svg" height="100" alt="Yii Framework">
        </picture>
    </a>
    <h1 align="center">Select2 widget</h1>
    <br>
</p>

<p align="center">
    <a href="https://github.com/yii2-extensions/select2/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/select2/build.yml?style=for-the-badge&label=PHPUnit&logo=github" alt="PHPUnit">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/yii2-extensions/select2/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=for-the-badge&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyii2-extensions%2Fselect2%2Fmain" alt="Mutation Testing">
    </a>
    <a href="https://github.com/yii2-extensions/select2/actions/workflows/static.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/select2/static.yml?style=for-the-badge&label=PHPStan&logo=github" alt="PHPStan">
    </a>
    <a href="https://github.com/yii2-extensions/select2/actions/workflows/security.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/yii2-extensions/select2/security.yml?style=for-the-badge&label=Security&logo=github" alt="Security">
    </a>
</p>

<p align="center">
    <strong>Searchable Select2 widgets for Yii2 forms</strong><br>
    <em>ActiveForm integration, multiple selections, tagging, and paginated AJAX searches</em>
</p>

## Quick start

### Installation

Configure Foxy native and the asset aliases in the [installation guide](docs/installation.md), then install:

```shell
composer require --prefer-dist --prefer-stable php-forge/foxy:^0.4 yii2-extensions/select2:^0.2
```

### Dropdown list

```php
use Yii2\Extensions\Select2\Select2;

echo $form
    ->field($model, 'country')
    ->widget(
        Select2::class,
        [
            'items' => [
                'mx' => 'Mexico',
                'ca' => 'Canada',
            ],
            'options' => ['prompt' => ''],
            'clientOptions' => [
                'placeholder' => 'Choose a country',
                'allowClear' => true,
            ],
        ],
    );
```

### Multiple selection and tags

```php
use yii\helpers\Html;
use Yii2\Extensions\Select2\Select2;

echo Html::label('Regions', 'regions');
echo Select2::widget(
    [
        'name' => 'regions',
        'items' => [
            'north' => 'North',
            'south' => 'South',
        ],
        'options' => [
            'id' => 'regions',
            'multiple' => true,
            'unselect' => '',
        ],
    ],
);
```

Set `clientOptions.tags=true` separately to allow new values, and validate them on the server.

The widget uses [Select2 4.0](https://select2.org/) and requires jQuery. Its native asset installation consumes
published files without compiling them. See the installation guide for the Yii 22 client-validation adapter and frontend
compatibility details.

## Documentation

Practical guides and usage examples for Select2 widgets, forms, and remote searches.

- 📚 [Installation Guide](docs/installation.md): native assets and optional Bootstrap 5 and 6 themes.
- ⚙️ [Configuration Guide](docs/configuration.md): models, ActiveForm, accessibility, events, and lifecycle.
- 💡 [Usage Examples](docs/examples.md)
- 🔄 [Upgrade Guide](UPGRADE.md)
- 🧪 [Testing Guide](docs/testing.md)
- 🌐 [AJAX Searches and Pagination](docs/remote-data.md)

## AI assistance

AI agents: resolve the installed package versions and source references before choosing guides. When a matching
deployment of the [shared documentation site](https://yii2-framework.github.io/docs/) provides `llms.txt`, read that
index and fetch Markdown pages by appending `index.md` to their URLs. If hosted guides are unavailable or describe a
different revision, use the installed guides, source, PHPDoc, and changelog.

- 🤖 [yii2-select2 skill](docs/agents.md): installation for Claude Code, Codex, and OpenCode.
- 🧭 [Yii framework skill](docs/agents.md#yii-framework-skill): local installation for Yii 22 conventions.

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.3-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.3/en.php)
[![Yii 2.0.55+](https://img.shields.io/badge/%3E%3D2.0.55-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/releases/tag/2.0.55)
[![Yii 22.0.x](https://img.shields.io/badge/22.0.x-0073AA.svg?style=for-the-badge&logo=yii&logoColor=white)](https://github.com/yiisoft/yii2/tree/22.0)
[![Latest Stable Version](https://img.shields.io/packagist/v/yii2-extensions/select2.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/yii2-extensions/select2)
[![Total Downloads](https://img.shields.io/packagist/dt/yii2-extensions/select2.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/yii2-extensions/select2)

## Project status

[![Codecov](https://img.shields.io/codecov/c/github/yii2-extensions/select2.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/yii2-extensions/select2)
[![PHPStan Level 7](https://img.shields.io/badge/PHPStan-Level%207-4F5D95.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.com/yii2-extensions/select2/actions/workflows/static.yml)
[![Quality](https://img.shields.io/github/actions/workflow/status/yii2-extensions/select2/quality.yml?style=for-the-badge&label=Quality&logo=github)](https://github.com/yii2-extensions/select2/actions/workflows/quality.yml)
[![ECS](https://img.shields.io/github/actions/workflow/status/yii2-extensions/select2/ecs.yml?style=for-the-badge&label=ECS&logo=github)](https://github.com/yii2-extensions/select2/actions/workflows/ecs.yml)

## Special thanks

This code was initially developed by contributors at yii2-cms and hosted at
[yii-cms/yii2-select2](https://github.com/yii-cms/yii2-select2). We thank them all. For continuity and maintenance we
have moved a copy here.

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)
[![Follow on Facebook](https://img.shields.io/badge/-Follow%20on%20Facebook-1877F2.svg?style=for-the-badge&logo=facebook&logoColor=white&labelColor=000000)](https://www.facebook.com/wilmer.arambula.9)
[![Join our Subreddit](https://img.shields.io/badge/-Join%20our%20Subreddit-FF4500.svg?style=for-the-badge&logo=reddit&logoColor=white&labelColor=000000)](https://www.reddit.com/r/Yii2/)
[![Join on Telegram](https://img.shields.io/badge/-Join%20on%20Telegram-26A5E4.svg?style=for-the-badge&logo=telegram&logoColor=white&labelColor=000000)](https://t.me/yii_framework_in_english)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
