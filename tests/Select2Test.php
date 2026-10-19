<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;
use Yii2\Extensions\Select2\Select2;
use Yii2\Extensions\Select2\Select2Asset;
use Yii2\Extensions\Select2\Select2WidgetAsset;
use Yii2\Extensions\Select2\Themes\Select2BS5ThemeAsset;
use Yii2\Extensions\Select2\Themes\Select2BS6ThemeAsset;

use function array_filter;
use function array_keys;
use function array_values;
use function dirname;
use function implode;
use function preg_match;
use function sort;
use function str_starts_with;
use function substr_count;

/**
 * Unit tests for the {@see Select2} widget, its generated `yiiSelect2` call, and the {@see Select2Asset} translations.
 */
final class Select2Test extends TestCase
{
    public function testNamedInputPreservesSelectionAndEscapesLabels(): void
    {
        $html = Select2::widget(['name' => 'country', 'value' => 'mx', 'items' => ['mx' => '<Mexico>']]);

        self::assertStringContainsString(
            'name="country"',
            $html,
            'The configured input name must be rendered.',
        );
        self::assertStringContainsString(
            'value="mx" selected',
            $html,
            'The selected option must be preserved.',
        );
        self::assertStringContainsString(
            '&lt;Mexico&gt;',
            $html,
            'Option labels must be HTML-escaped.',
        );
    }

    public function testModelMultipleInputUsesArrayNameAndSelectedValues(): void
    {
        $model = new DynamicModel(['countries' => ['mx', 'ca']]);

        $html = Select2::widget(
            [
                'model' => $model,
                'attribute' => 'countries',
                'items' => ['mx' => 'Mexico', 'ca' => 'Canada'],
                'options' => ['multiple' => true],
            ],
        );

        self::assertStringContainsString(
            'name="DynamicModel[countries][]"',
            $html,
            'Multiple model inputs must use an array name.',
        );
        self::assertStringContainsString(
            'multiple',
            $html,
            'The select must allow multiple values.',
        );
        self::assertSame(
            2,
            substr_count($html, ' selected'),
            'Both model values must be selected.',
        );
    }

    public function testPromptAllowsEmptySelection(): void
    {
        $html = Select2::widget(['name' => 'country', 'items' => ['mx' => 'Mexico'], 'options' => ['prompt' => '']]);

        self::assertStringContainsString(
            '<option value=""></option>',
            $html,
            'An empty prompt option must be rendered.',
        );
    }

    public function testDisabledAndRequiredOptionsRemainOnTheNativeInput(): void
    {
        $html = Select2::widget(
            [
                'name' => 'country',
                'options' => [
                    'disabled' => true,
                    'required' => true,
                    'aria-describedby' => 'hint',
                ],
            ],
        );

        self::assertStringContainsString(
            ' disabled',
            $html,
            'The disabled attribute must be preserved.',
        );
        self::assertStringContainsString(
            ' required',
            $html,
            'The required attribute must be preserved.',
        );
        self::assertStringContainsString(
            'aria-describedby="hint"',
            $html,
            'The accessibility description must be preserved.',
        );
    }

    public function testNamedMultipleInputCanSubmitClearedSelection(): void
    {
        $html = Select2::widget(
            [
                'name' => 'regions',
                'items' => ['north' => 'North'],
                'options' => ['multiple' => true, 'unselect' => ''],
            ],
        );

        self::assertStringContainsString(
            '<input type="hidden" name="regions" value="">',
            $html,
            'Cleared multiple selections must submit an empty value.',
        );
        self::assertStringContainsString(
            'name="regions[]"',
            $html,
            'The select must use an array name.',
        );
    }

    public function testRenderedSelectCarriesOnlyConfiguredClasses(): void
    {
        $plain = Select2::widget(['name' => 'country', 'options' => ['id' => 'country']]);
        $styled = Select2::widget(['name' => 'city', 'options' => ['id' => 'city', 'class' => 'form-control']]);

        self::assertStringStartsWith(
            '<select id="country" name="country">',
            $plain,
            'Unconfigured classes must not be added.',
        );
        self::assertStringStartsWith(
            '<select id="city" class="form-control" name="city">',
            $styled,
            'Configured classes must be rendered.',
        );
    }

    public function testThrowInvalidConfigExceptionWhenNameAndModelAreMissing(): void
    {
        $this->expectException(InvalidConfigException::class);

        Select2::widget();
    }

    public function testScriptInitializesThePluginWithEncodedIdAndDefaultOptions(): void
    {
        Select2::widget(['name' => 'country', 'options' => ['id' => "country:part'[]</script>"]]);

        self::assertSame(
            [
                <<<JS
                jQuery(document.getElementById("country:part\u0027[]\u003C\/script\u003E")).yiiSelect2({"language":"en-US","width":"100%"});
                JS,
            ],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'The encoded element ID and default options must be used.',
        );
    }

    public function testExplicitLanguageAndWidthAreKept(): void
    {
        Select2::widget(
            [
                'name' => 'country',
                'options' => ['id' => 'country'],
                'clientOptions' => ['width' => 'style', 'language' => 'de', 'placeholder' => 'Choose'],
            ],
        );

        self::assertSame(
            [
                <<<JS
                jQuery(document.getElementById("country")).yiiSelect2({"width":"style","language":"de","placeholder":"Choose"});
                JS,
            ],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'Explicit client options must override the defaults.',
        );
    }

    public function testApplicationLanguageIsNormalizedForSelect2(): void
    {
        Yii::$app->language = 'pt_BR';

        Select2::widget(['name' => 'country', 'options' => ['id' => 'country']]);

        self::assertSame(
            ['jQuery(document.getElementById("country")).yiiSelect2({"language":"pt-BR","width":"100%"});'],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'The normalized application language must be used.',
        );
    }

    public function testEventsArePassedAsJavaScriptFunctions(): void
    {
        Select2::widget(
            [
                'name' => 'country',
                'options' => ['id' => 'country'],
                'clientOptions' => ['matcher' => new JsExpression('function (params, data) { return data; }')],
                'clientEvents' => [
                    'change' => 'function () { window.changed = true; }',
                    'select2:select.app select2:unselect' => new JsExpression(
                        'function () { window.selected = true; }',
                    ),
                ],
            ],
        );

        self::assertSame(
            [
                <<<JS
                jQuery(document.getElementById("country")).yiiSelect2({"matcher":function (params, data) { return data; },"language":"en-US","width":"100%"}, {"change":function () { window.changed = true; },"select2:select.app select2:unselect":function () { window.selected = true; }});
                JS,
            ],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'Client options and events must be emitted as JavaScript functions.',
        );
    }

    public function testFalseClientOptionsRegistersThePluginWithoutInitialization(): void
    {
        Select2::widget(
            [
                'name' => 'country',
                'clientOptions' => false,
                'clientEvents' => ['change' => 'function () {}'],
            ],
        );

        self::assertArrayHasKey(
            Select2WidgetAsset::class,
            Yii::$app->view->assetBundles,
            'The widget asset bundle must still be registered.',
        );
        self::assertSame(
            [],
            Yii::$app->view->js,
            'Disabling client options must skip initialization scripts.',
        );
    }

    public function testMultipleWidgetsKeepTheirIdsAndConfiguration(): void
    {
        $first = Select2::widget(['name' => 'first', 'clientOptions' => ['placeholder' => 'First']]);
        $second = Select2::widget(['name' => 'second', 'clientOptions' => ['placeholder' => 'Second']]);

        preg_match('/id="([^"]+)"/', $first, $firstId);
        preg_match('/id="([^"]+)"/', $second, $secondId);

        self::assertNotSame(
            $firstId[1],
            $secondId[1],
            'Each widget must have a unique ID.',
        );
        self::assertSame(
            [
                strtr(
                    <<<'JS'
                    jQuery(document.getElementById("__ID__")).yiiSelect2({"placeholder":"First","language":"en-US","width":"100%"});
                    JS,
                    ['__ID__' => $firstId[1]],
                ),
                strtr(
                    <<<'JS'
                    jQuery(document.getElementById("__ID__")).yiiSelect2({"placeholder":"Second","language":"en-US","width":"100%"});
                    JS,
                    ['__ID__' => $secondId[1]],
                ),
            ],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'Each widget must retain its own client options.',
        );
    }

    #[TestWith(['bootstrap-5', Select2BS5ThemeAsset::class])]
    #[TestWith(['bootstrap-6', Select2BS6ThemeAsset::class])]
    public function testBootstrapThemeRegistersOnlyItsThemeBundle(string $theme, string $bundle): void
    {
        Select2::widget(['name' => 'country', 'clientOptions' => ['theme' => $theme]]);

        self::assertSame(
            self::sorted([JqueryAsset::class, Select2Asset::class, Select2WidgetAsset::class, $bundle]),
            self::sorted(array_keys(Yii::$app->view->assetBundles)),
            'Only the configured theme bundle and its dependencies must be registered.',
        );
        self::assertSame(
            [Select2Asset::class],
            Yii::$app->view->assetBundles[$bundle]->depends,
            'The theme bundle must depend on the Select2 assets.',
        );
        self::assertSame(
            [JqueryAsset::class],
            Yii::$app->view->assetBundles[Select2Asset::class]->depends,
            'The Select2 assets must depend on jQuery.',
        );
    }

    #[TestWith(['classic'])]
    #[TestWith(['default'])]
    #[TestWith([null])]
    public function testOtherThemesRegisterNoThemeBundle(string|null $theme): void
    {
        $clientOptions = $theme === null ? [] : ['theme' => $theme];

        Select2::widget(['name' => 'country', 'options' => ['id' => 'country'], 'clientOptions' => $clientOptions]);

        self::assertSame(
            self::sorted([JqueryAsset::class, Select2Asset::class, Select2WidgetAsset::class]),
            self::sorted(array_keys(Yii::$app->view->assetBundles)),
            'Built-in themes must not register a theme-specific bundle.',
        );

        $script = Yii::$app->view->js[View::POS_READY];

        if ($theme === null) {
            self::assertStringNotContainsString(
                '"theme"',
                implode("\n", $script),
                'The default theme must not be explicitly configured.',
            );
        } else {
            self::assertStringContainsString(
                "\"theme\":\"$theme\"",
                implode("\n", $script),
                'The configured built-in theme must be preserved.',
            );
        }
    }

    #[TestWith(['es-MX', 'js/i18n/es.js'])]
    #[TestWith(['pt_BR', 'js/i18n/pt-BR.js'])]
    #[TestWith(['pt-BR', 'js/i18n/pt-BR.js'])]
    #[TestWith(['zh-TW', 'js/i18n/zh-TW.js'])]
    #[TestWith(['sr-Cyrl', 'js/i18n/sr-Cyrl.js'])]
    #[TestWith(['en-US', 'js/i18n/en.js'])]
    #[TestWith(['zh-HK', null])]
    #[TestWith(['xx', null])]
    #[TestWith(['../../etc', null])]
    #[TestWith(['../select2.full', null])]
    #[TestWith(['de-CH;q=0.9', null])]
    #[TestWith(["es-MX\n", null])]
    public function testAssetLoadsTheExactOrBaseTranslation(string $language, string|null $translation): void
    {
        Yii::$app->language = $language;

        $asset = Select2Asset::register(Yii::$app->view);
        $asset->registerAssetFiles(Yii::$app->view);
        $expected = $translation === null ? [] : [$translation];

        self::assertSame(
            $expected,
            self::translations($asset->js),
            'Only the exact or base translation file must be selected.',
        );

        foreach ($expected as $file) {
            self::assertArrayHasKey(
                "$asset->baseUrl/$file",
                Yii::$app->view->jsFiles[View::POS_END],
                'The selected translation file must be registered.',
            );
        }
    }

    public function testTranslationIsAddedOnceWhenRegisteredTwice(): void
    {
        Yii::$app->language = 'es-MX';

        Select2Asset::register(Yii::$app->view);
        $asset = Select2Asset::register(Yii::$app->view);

        self::assertSame(
            ['js/i18n/es.js'],
            self::translations($asset->js),
            'Repeated registration must not duplicate the translation.',
        );
    }

    #[TestWith(['es-MX', 'js/i18n/es.js'])]
    #[TestWith(['pt_BR', 'js/i18n/pt-BR.js'])]
    public function testPredeployedBundleLoadsTranslations(string $language, string $translation): void
    {
        Yii::$app->language = $language;

        Yii::$app->assetManager->bundles[Select2Asset::class] = [
            'sourcePath' => null,
            'basePath' => dirname(__DIR__) . '/node_modules/select2/dist',
            'baseUrl' => '/static/select2',
        ];

        $asset = Select2Asset::register(Yii::$app->view);

        $asset->registerAssetFiles(Yii::$app->view);

        self::assertSame(
            [$translation],
            self::translations($asset->js),
            'The predeployed bundle must select the language translation.',
        );
        self::assertArrayHasKey(
            "/static/select2/$translation",
            Yii::$app->view->jsFiles[View::POS_END],
            'The translation must use the predeployed asset URL.',
        );
    }

    /**
     * @param array<array-key, mixed> $files
     *
     * @return list<mixed>
     */
    private static function translations(array $files): array
    {
        return array_values(
            array_filter($files, static fn (mixed $file): bool => str_starts_with((string) $file, 'js/i18n/')),
        );
    }

    /**
     * @param array<array-key, string> $values
     *
     * @return array<array-key, string>
     */
    private static function sorted(array $values): array
    {
        sort($values);

        return $values;
    }
}
