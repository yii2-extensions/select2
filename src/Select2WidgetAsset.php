<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2;

use yii\web\AssetBundle;

/**
 * Publishes the `yiiSelect2` jQuery plugin that initializes widgets, scopes their events, and mirrors ARIA state.
 */
final class Select2WidgetAsset extends AssetBundle
{
    /**
     * Published source directory of the packaged widget assets.
     */
    public $sourcePath = __DIR__ . '/Assets';
    /**
     * Integration script that provides the `yiiSelect2` jQuery plugin.
     */
    public $js = ['select2.yii.js'];
    /**
     * Select2 build and translation required by the integration script.
     */
    public $depends = [Select2Asset::class];
}
