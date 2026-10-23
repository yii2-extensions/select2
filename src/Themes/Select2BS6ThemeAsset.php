<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Themes;

use yii\web\AssetBundle;
use Yii2\Extensions\Select2\Select2Asset;

/**
 * Publishes the `bootstrap-6` Select2 theme stylesheet built on Bootstrap 6 design tokens.
 */
final class Select2BS6ThemeAsset extends AssetBundle
{
    /**
     * Published source directory of the packaged widget assets.
     */
    public $sourcePath = __DIR__ . '/../Assets';
    /**
     * Packaged Bootstrap 6 theme stylesheet.
     */
    public $css = ['select2-bootstrap-6-theme.css'];
    /**
     * Select2 bundle whose default stylesheet the theme extends.
     */
    public $depends = [Select2Asset::class];
}
