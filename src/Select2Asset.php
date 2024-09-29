<?php

declare(strict_types=1);

namespace Yii2\Extension\Select2;

use yii\web\AssetBundle;


class Select2Asset extends AssetBundle
{
    public $sourcePath = '@npm/select2/dist';

    public $js = [
        'js/select2.full.min.js',
    ];

    public $css = [
        'css/select2.min.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $language = \Yii::$app->language;

        if (is_file(\Yii::getAlias("{$this->sourcePath}/js/i18n/{$language}.js"))) {
            $this->js[] = "js/i18n/{$language}.js";
        }
        parent::registerAssetFiles($view);
    }
}
