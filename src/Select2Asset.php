<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2;

use Yii;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use yii\web\JqueryAsset;

use function explode;
use function is_file;
use function preg_match;
use function str_replace;

/**
 * Publishes Select2's full browser build and the translation matching the application language.
 */
final class Select2Asset extends AssetBundle
{
    /**
     * Published distribution directory of the Select2 npm package.
     */
    public $sourcePath = '@npm/select2/dist';
    /**
     * Full Select2 build; {@see publish()} appends the matching translation.
     */
    public $js = ['js/select2.full.min.js'];
    /**
     * Default Select2 stylesheet.
     */
    public $css = ['css/select2.min.css'];
    /**
     * jQuery, required by Select2.
     */
    public $depends = [JqueryAsset::class];

    /**
     * Publishes the bundle and appends the exact regional translation, or else its base-language translation.
     *
     * @param AssetManager $am Asset manager that publishes the bundle.
     */
    public function publish($am): void
    {
        parent::publish($am);

        $language = str_replace('_', '-', Yii::$app->language);

        if (preg_match('/^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/D', $language) !== 1) {
            return;
        }

        foreach ([$language, explode('-', $language)[0]] as $locale) {
            if (is_file("$this->basePath/js/i18n/$locale.js")) {
                $this->js[] = "js/i18n/$locale.js";

                break;
            }
        }
    }
}
