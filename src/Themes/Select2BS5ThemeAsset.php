<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Themes;

use yii\web\AssetBundle;


class Select2BS5ThemeAsset extends AssetBundle
{
    public $sourcePath = '@npm/select2-bootstrap-5-theme/dist';

    public $js = [];

    public $css = [
        'select2-bootstrap-5-theme.min.css',
    ];

    public function init()
    {
        parent::init();

        //which BS version is installed (from highest to lowest)
        if (class_exists('yii\bootstrap5\BootstrapAsset')) { //BS5 official
            $this->depends[] = 'yii\bootstrap5\BootstrapAsset';
        } else if (class_exists('Yii2\Asset\BootstrapAsset')) { //BS5 Yii2-Ext
            $this->depends[] = 'Yii2\Asset\BootstrapAsset';
        }
    }
}
