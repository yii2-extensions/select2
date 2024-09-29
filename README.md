<p align="center">
    <a href="https://github.com/yii2-extensions/selectize" target="_blank"png
        <img src=">https://select2.org/user/pages/images/logo.png" height="100px;"> 
    </a>
    <h1 align="center">Select2 widget for Yii2 framework</h1>
    <br>
</p>

<p align="center">
    <a href="https://www.php.net/releases/8.1/en.php" target="_blank">
        <img src="https://img.shields.io/badge/PHP-%3E%3D8.1-787CB5" alt="php-version">
    </a>
    <a href="https://github.com/yii2-extensions/select2/actions/workflows/build.yml" target="_blank">
        <img src="https://github.com/yii2-extensions/select2/actions/workflows/build.yml/badge.svg" alt="PHPUnit">
    </a>
    <a href="https://github.com/yii2-extensions/select2/actions/workflows/compatibility.yml" target="_blank">
        <img src="https://github.com/yii2-extensions/select2/actions/workflows/compatibility.yml/badge.svg" alt="PHPUnit">
    </a>        
    <a href="https://codecov.io/gh/yii2-extensions/select2" target="_blank">
        <img src="https://codecov.io/gh/yii2-extensions/select2/branch/main/graph/badge.svg?token=MF0XUGVLYC" alt="Codecov">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/yii2-extensions/select2/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyii2-extensions%2Fselect2%2Fmain" alt="Infection">
    </a>          
</p>

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```shell
composer require --prefer-dist yii2-extensions/select2:^0.1
```

or add

```json
"yii2-extensions/select2": "^0.1"
```

to the require section of your `composer.json` file.

## Usage

### Dropdown list 

```php
use Yii2\Extension\Select2\Select2;

<?= $form->field($model, 'user_id')->widget(Select2::className(), [
        'items' => ArrayHelper::map(User::find()->all(), 'id', function($model) {
            return $model->username . " <{$model->email}>";
        }),
        'clientOptions' => [
            'placeholder' => 'User',
            'allowClear' => true,
        ],
        'clientEvents' => [
            'change'=>'function (e) {
                console.log("Select2 change.");
            }',
        ],
    ]) ?>
```

### Multiple aka Tag mode input

```php
use Yii2\Extension\Select2\Select2;

<?= Select2::widget([
        'name' => 'InputName',
        'items' => ArrayHelper::map(User::find()->all(), 'id', function($model) {
            return $model->fio . " <{$model->email}>";
        }),
        'options' => [ 
            'multiple' => true,
        ]
        'clientOptions' => [
            'placeholder' => 'User',
            'allowClear' => true,
        ]
    ]) ?>
```

### Properties of the widget

 For more details see: https://github.com/select2/select2

### Special Thanks
This code was initially developed by contributors at yii2-cms and hosted at repo: https://github.com/yii-cms/yii2-select2. We thank them all. For continuity and maintenance we have moved a copy here. 

## Quality code

[![static-analysis](https://github.com/yii2-extensions/select2/actions/workflows/static.yml/badge.svg)](https://github.com/yii2-extensions/select2/actions/workflows/static.yml)
[![phpstan-level](https://img.shields.io/badge/PHPStan%20level-7-blue)](https://github.com/yii2-extensions/select2/actions/workflows/static.yml)
[![StyleCI](https://github.styleci.io/repos/720718108/shield?branch=main)](https://github.styleci.io/repos/720718108?branch=main)

## Support versions Yii2

[![Yii20](https://img.shields.io/badge/Yii2%20version-2.0-blue)](https://github.com/yiisoft/yii2/tree/2.0.49.3)
[![Yii22](https://img.shields.io/badge/Yii2%20version-2.2-blue)](https://github.com/yiisoft/yii2/tree/2.2)

## Testing

[Check the documentation testing](docs/testing.md) to learn about testing.

## Our social networks

[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiupdates)

## License

The MIT License. Please see [License File](LICENSE) for more information.