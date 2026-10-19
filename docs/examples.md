# Usage examples

These snippets run in an application view. Define the application model and validate submitted values as shown in the
[configuration guide](configuration.md#model-and-activeform).

## Local selections

```php
use yii\helpers\Html;
use Yii2\Extensions\Select2\Select2;

echo Html::label('Country', 'country');
echo Select2::widget(
    [
        'name' => 'country',
        'items' => ['mx' => 'Mexico', 'ca' => 'Canada', 'es' => 'Spain'],
        'options' => ['id' => 'country', 'prompt' => ''],
        'clientOptions' => ['placeholder' => 'Choose a country', 'allowClear' => true],
    ],
);
```

## Multiple values and tags

Normalize a cleared multiple value to an empty array and validate every member. For tags, also validate string lengths
and the total count on the server.

```php
use yii\helpers\Html;
use Yii2\Extensions\Select2\Select2;

echo Html::label('Regions', 'regions');
echo Select2::widget(
    [
        'name' => 'regions',
        'items' => ['north' => 'North', 'south' => 'South'],
        'options' => ['id' => 'regions', 'multiple' => true, 'unselect' => ''],
    ],
);

echo Html::label('Tags', 'tags');
echo Select2::widget(
    [
        'name' => 'tags',
        'items' => ['yii' => 'yii', 'php' => 'php'],
        'options' => ['id' => 'tags', 'multiple' => true, 'unselect' => ''],
        'clientOptions' => ['tags' => true, 'maximumSelectionLength' => 5],
    ],
);
```

Redisplay only safe scalar selections after validation errors. See the
[form guide](configuration.md#accessibility-and-validation) for labels, hints and error focus.

## Remote search

Provide the application's authorized [search endpoint](remote-data.md#search-endpoint) and preserve labels for
preselected IDs when rendering the widget.

```php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use Yii2\Extensions\Select2\Select2;

echo Html::label('Remote city', 'remote-city');
echo Select2::widget(
    [
        'name' => 'city',
        'value' => '2',
        'items' => ['2' => 'Madrid'],
        'options' => ['id' => 'remote-city', 'prompt' => ''],
        'clientOptions' => [
            'placeholder' => 'Search cities',
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['city/search']),
                'dataType' => 'json',
                'delay' => 250,
                'data' => new JsExpression(
                    'function (params) { return {q: params.term || "", page: params.page || 1}; }'
                ),
            ],
        ],
    ],
);
```

[Back to documentation](../README.md#documentation)
