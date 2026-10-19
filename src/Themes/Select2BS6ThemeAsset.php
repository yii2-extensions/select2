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
    public $sourcePath = __DIR__ . '/../Assets';
    public $css = ['select2-bootstrap-6-theme.css'];
    public $depends = [Select2Asset::class];
}
