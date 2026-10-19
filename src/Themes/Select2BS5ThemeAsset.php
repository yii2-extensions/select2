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
    public $sourcePath = '@npm/select2-bootstrap-5-theme/dist';
    public $css = ['select2-bootstrap-5-theme.min.css'];
    public $depends = [Select2Asset::class];
}
