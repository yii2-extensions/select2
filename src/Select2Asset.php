<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2;

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
    ];

    public function init()
    {
        parent::init();

        //which BS version is installed (from highest to lowest)
        if (class_exists('yii\bootstrap5\BootstrapAsset')) { //BS5 official
            $this->depends[] = 'yii\bootstrap5\BootstrapAsset';
        } else if (class_exists('Yii2\Asset\BootstrapAsset')) { //BS5 Yii2-Ext
            $this->depends[] = 'Yii2\Asset\BootstrapAsset';
        } else if (class_exists('yii\bootstrap4\BootstrapAsset')) {
            $this->depends[] = 'yii\bootstrap4\BootstrapAsset';
        } else {
            $this->depends[] = 'yii\bootstrap\BootstrapAsset';
        }
    }

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
