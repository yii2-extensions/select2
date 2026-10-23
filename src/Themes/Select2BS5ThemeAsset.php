<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Themes;

use yii\web\AssetBundle;
use Yii2\Extensions\Select2\Select2Asset;

/**
 * Publishes the `bootstrap-5` Select2 theme stylesheet from the optional `select2-bootstrap-5-theme` package.
 */
final class Select2BS5ThemeAsset extends AssetBundle
{
    /**
     * Published distribution directory of the `select2-bootstrap-5-theme` npm package.
     */
    public $sourcePath = '@npm/select2-bootstrap-5-theme/dist';
    /**
     * Bootstrap 5 theme stylesheet.
     */
    public $css = ['select2-bootstrap-5-theme.min.css'];
    /**
     * Select2 bundle whose default stylesheet the theme extends.
     */
    public $depends = [Select2Asset::class];
}
