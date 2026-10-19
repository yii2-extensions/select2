# Remote data

Select2's full build supports AJAX searches and paginated results through `clientOptions.ajax`. The widget does not
create an endpoint or implement authorization. The [endpoint recipe](#search-endpoint) provides a small read-only
endpoint without a database.

In a view with `$form`, `$model`, and a validated existing city ID and label:

```php
use yii\helpers\Url;
use yii\web\JsExpression;
use Yii2\Extensions\Select2\Select2;

echo $form
  ->field($model, 'city')
  ->widget(
    Select2::class,
    [
        'items' => ['2' => 'Madrid'],
        'options' => ['prompt' => ''],
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
    ]
  );
```

The example ID and label must be replaced by the current model's authorized record in an application. AJAX cannot fetch
the label of an existing value merely from `$model->city`; include it in `items` on initial render and on validation
failure. For multiple preselected values, include all selected IDs and labels.

Return JSON with this shape, using string IDs and plain-text labels:

```json
{
  "results": [{ "id": "2", "text": "Madrid" }],
  "pagination": { "more": false }
}
```

Filter on the server and bound the page size. Preserve `page` and calculate `pagination.more` against the filtered
dataset. Query through Yii's parameterized query APIs; do not concatenate the search term into SQL. Apply the
application's normal authentication and access filters, and validate query types and lengths. The endpoint recipe uses
fixed public sample data.

Keep CSRF enabled for POST endpoints. A GET search should only read data. Handle network errors with appropriate HTTP
status codes and allow a subsequent search to retry; never return success with an empty list to hide a server failure.
Select2 displays its translated loading error for a failed request.

If an API uses another JSON shape, adapt it with a `JsExpression` in `ajax.processResults`. If replacing
`ajax.transport`, return an object with an `abort` method and call the success/failure callbacks correctly so stale
requests can be cancelled. See the [official AJAX documentation](https://select2.org/data-sources/ajax/).

## Search endpoint

Place this action in an application controller with the application's access and HTTP verb filters. The fixed public
sample data needs no database; replace it with an authorized dataset in a real application.

```php
public function actionSearch(): \yii\web\Response
{
    $query = \Yii::$app->request->get('q', '');
    $page = \Yii::$app->request->get('page', '1');

    if (
        !is_string($query)
        || strlen($query) > 100
        || !is_scalar($page)
        || !ctype_digit((string) $page)
        || (int) $page < 1
        || (int) $page > 100
    ) {
        throw new \yii\web\BadRequestHttpException('Provide a text query and a page between 1 and 100.');
    }

    $cities = [
        '1' => 'Barcelona',
        '2' => 'Madrid',
        '3' => 'Malaga',
        '4' => 'Manila',
        '5' => 'Montreal',
        '6' => 'Monterrey',
    ];
    $rows = [];

    foreach ($cities as $id => $text) {
        if ($query === '' || stripos($text, $query) !== false) {
            $rows[] = ['id' => (string) $id, 'text' => $text];
        }
    }

    $size = 2;

    return $this->asJson(
        [
            'results' => array_slice($rows, ((int) $page - 1) * $size, $size),
            'pagination' => ['more' => (int) $page * $size < count($rows)],
        ],
    );
}
```

[Back to documentation](../README.md#documentation)
