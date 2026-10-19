<?php

declare(strict_types=1);

namespace Yii2\Extensions\Select2\Tests;

use Yii;
use yii\base\DynamicModel;
use yii\web\View;
use yii\widgets\ActiveForm;
use Yii2\Extensions\Select2\Select2;

use function array_values;
use function ob_get_clean;
use function ob_start;

/**
 * Unit tests for {@see Select2} rendered through {@see \yii\widgets\ActiveField::widget()} with validation errors.
 */
final class Select2ActiveFieldTest extends TestCase
{
    public function testActiveFieldSuppliesClassesAndValidationState(): void
    {
        $model = new DynamicModel(['country' => 'forged', 'regions' => ['west']]);

        $model
            ->addRule('country', 'required')
            ->addRule('country', 'in', ['range' => ['mx', 'ca']])
            ->addRule('regions', 'each', ['rule' => ['in', 'range' => ['north', 'south']]]);

        self::assertFalse(
            $model->validate(),
            'Invalid model data must fail validation.',
        );

        ob_start();

        $form = ActiveForm::begin(
            [
                'action' => '/submit',
                'enableClientScript' => false,
                'errorCssClass' => 'is-invalid',
                'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
            ],
        );

        echo $form
            ->field($model, 'country')
            ->widget(
                Select2::class,
                [
                    'items' => ['mx' => 'Mexico', 'ca' => 'Canada'],
                    'options' => ['prompt' => '', 'aria-describedby' => 'country-hint'],
                ],
            );
        echo $form
            ->field($model, 'regions')
            ->widget(
                Select2::class,
                [
                    'items' => ['north' => 'North', 'south' => 'South'],
                    'options' => ['multiple' => true],
                ],
            );

        ActiveForm::end();

        $html = (string) ob_get_clean();

        self::assertStringContainsString(
            <<<HTML
            <select id="dynamicmodel-country" class="form-control is-invalid" name="DynamicModel[country]" aria-describedby="country-hint" aria-required="true" aria-invalid="true">
            HTML,
            $html,
            'The country select must include its validation attributes.',
        );
        self::assertStringContainsString(
            <<<HTML
            <select id="dynamicmodel-regions" class="form-control is-invalid" name="DynamicModel[regions][]" multiple size="4" aria-invalid="true">
            HTML,
            $html,
            'The multiple region select must retain its invalid state.',
        );
        self::assertStringContainsString(
            <<<HTML
            <div class="help-block">Country is invalid.</div>
            HTML,
            $html,
            'The country validation error must be rendered.',
        );
        self::assertSame(
            [
                <<<JS
                jQuery(document.getElementById("dynamicmodel-country")).yiiSelect2({"language":"en-US","width":"100%"});
                JS,
                <<<JS
                jQuery(document.getElementById("dynamicmodel-regions")).yiiSelect2({"language":"en-US","width":"100%"});
                JS,
            ],
            array_values(Yii::$app->view->js[View::POS_READY]),
            'Each field must register its expected Select2 initialization script.',
        );
    }
}
