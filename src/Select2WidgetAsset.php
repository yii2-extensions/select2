<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2;

use yii\web\AssetBundle;

/**
 * Publishes the `yiiSelect2` jQuery plugin that initializes widgets, scopes their events, and mirrors ARIA state.
 */
final class Select2WidgetAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/Assets';
    public $js = ['select2.yii.js'];
    public $depends = [Select2Asset::class];
}
