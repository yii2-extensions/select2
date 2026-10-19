<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use Yii2\Extensions\Select2\Themes\Select2BS5ThemeAsset;
use Yii2\Extensions\Select2\Themes\Select2BS6ThemeAsset;

use function implode;
use function is_string;
use function str_replace;

/**
 * Renders a Select2 dropdown bound to a model attribute or a named input.
 */
class Select2 extends InputWidget
{
    /**
     * Theme asset bundles registered for the matching `clientOptions['theme']` value.
     */
    private const THEME_ASSETS = [
        'bootstrap-5' => Select2BS5ThemeAsset::class,
        'bootstrap-6' => Select2BS6ThemeAsset::class,
    ];

    /**
     * @var array<array-key, mixed> Items accepted by {@see Html::dropDownList()}, including optgroups.
     */
    public array $items = [];
    /**
     * @var array<string, mixed>|false Select2 options, or `false` to skip automatic initialization. `width` defaults
     * to `100%` and `language` to the application language; the `bootstrap-5` and `bootstrap-6` themes register
     * their stylesheets.
     * @see https://select2.org/configuration/options-api/
     */
    public array|false $clientOptions = [];
    /**
     * @var array<string, string|JsExpression> JavaScript handlers bound to the original select, keyed by event names.
     */
    public array $clientEvents = [];

    public function run(): string
    {
        $html = $this->hasModel()
            ? Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options)
            : Html::dropDownList($this->name, $this->value, $this->items, $this->options);

        $this->registerClientScript();

        return $html;
    }

    /**
     * Registers the plugin assets, the selected theme, and one `yiiSelect2` initialization call.
     */
    private function registerClientScript(): void
    {
        $view = $this->getView();

        Select2WidgetAsset::register($view);

        if ($this->clientOptions === false) {
            return;
        }

        $options = $this->clientOptions;
        $options['language'] ??= str_replace('_', '-', Yii::$app->language);
        $options['width'] ??= '100%';
        $theme = $options['theme'] ?? null;

        if (is_string($theme) && isset(self::THEME_ASSETS[$theme])) {
            self::THEME_ASSETS[$theme]::register($view);
        }

        $arguments = [Json::htmlEncode($options)];

        if ($this->clientEvents !== []) {
            $events = [];

            foreach ($this->clientEvents as $event => $handler) {
                $events[$event] = is_string($handler) ? new JsExpression($handler) : $handler;
            }

            $arguments[] = Json::htmlEncode($events);
        }

        $id = Json::htmlEncode($this->options['id']);

        $view->registerJs("jQuery(document.getElementById($id)).yiiSelect2(" . implode(', ', $arguments) . ');');
    }
}
