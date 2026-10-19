# Configuration and forms

The public PHP namespace is `Yii2\Extensions\Select2`. The widget extends `yii\widgets\InputWidget` and retains its
`model`, `attribute`, `name`, `value`, and HTML `options` contract.

| Property        | Meaning                                                                                                               |
| --------------- | --------------------------------------------------------------------------------------------------------------------- |
| `items`         | Yii dropdown items, including optgroups. Labels are HTML encoded by default.                                          |
| `options`       | Attributes on the original `<select>`; use a unique `id`, `multiple`, `disabled`, `prompt`, and ARIA attributes here. |
| `clientOptions` | Select2 4.0 options; `width` defaults to `100%` and `language` to the application language.                           |
| `clientEvents`  | Event names mapped to trusted JavaScript function strings or `yii\web\JsExpression` instances.                        |

Set `clientOptions` to `false` to skip the generated initialization while still registering the assets; `clientEvents`
are then not bound either.

## Model and ActiveForm

Define validation on the server even when client validation is enabled:

```php
use yii\base\Model;

final class SelectionForm extends Model
{
    public $country = '';
    public $regions = [];
    public $city = '2';
    public $tags = ['yii'];

    public const COUNTRIES = [
        'mx' => 'Mexico',
        'ca' => 'Canada',
        'es' => 'Spain',
    ];
    public const REGIONS = [
        'north' => 'North',
        'south' => 'South',
        'east' => 'East',
        'west' => 'West',
    ];
    public const CITIES = [
        '1' => 'Barcelona',
        '2' => 'Madrid',
        '3' => 'Malaga',
        '4' => 'Manila',
        '5' => 'Montreal',
        '6' => 'Monterrey',
    ];

    public function rules(): array
    {
        return [
            [['regions', 'tags'], 'filter', 'filter' => static fn ($value) => $value === '' ? [] : $value],
            [['country', 'city'], 'required'],
            ['country', 'in', 'range' => array_keys(self::COUNTRIES)],
            ['city', 'in', 'range' => array_map('strval', array_keys(self::CITIES))],
            ['regions', 'each', 'rule' => ['in', 'range' => array_keys(self::REGIONS), 'skipOnEmpty' => false]],
            ['tags', 'each', 'rule' => ['string', 'max' => 30, 'skipOnEmpty' => false]],
            ['tags', 'validateTagCount'],
        ];
    }

    public function validateTagCount(string $attribute): void
    {
        if (is_array($this->$attribute) && count($this->$attribute) > 5) {
            $this->addError($attribute, 'Choose at most five tags.');
        }
    }

    public function attributeLabels(): array
    {
        return ['country' => 'Country', 'regions' => 'Regions', 'city' => 'Remote city', 'tags' => 'Tags'];
    }
}
```

In an application view with `$form` and `$model`:

```php
use Yii2\Extensions\Select2\Select2;

echo $form
    ->field($model, 'country')
    ->widget(
        Select2::class,
        [
            'items' => [
                'mx' => 'Mexico',
                'ca' => 'Canada',
            ],
            'options' => ['prompt' => ''],
            'clientOptions' => [
                'placeholder' => 'Choose a country',
                'allowClear' => true,
            ],
        ],
    );

echo $form
    ->field($model, 'regions')
    ->widget(
        Select2::class,
        [
            'items' => ['north' => 'North', 'south' => 'South'],
            'options' => ['multiple' => true],
        ]
    );
```

A single-select placeholder and `allowClear` need an empty option; `options.prompt` supplies it. A multiple field posts
an array, and Yii renders an empty hidden value so clearing all selections can replace previous values. Multiple
selection does not enable free-form tags: set `clientOptions.tags=true` separately and validate every tag and the total
count in the model. Redisplay existing tags through `items` after submission.

For standalone named multiple selects, set `options.unselect=''` so clearing all selections submits an empty marker;
model-bound selects add it automatically.

Load and validate the model with `$model->load(Yii::$app->request->post()) && $model->validate()` before saving.
Validate selected IDs against the authorized dataset; a posted ID is never proof of access. For ActiveRecord attributes
containing arrays, map them to the application's relation or persistence format explicitly.

## Accessibility and validation

Give every control a visible label and a unique ID. ActiveForm supplies the model label automatically; standalone
widgets need `Html::label('Country', 'country')` and the matching `options.id`. Keep hints and errors in real DOM nodes
and reference their IDs through `options['aria-describedby']`.

The `yiiSelect2` plugin copies labels, descriptions, required state, and `aria-invalid` from the original select to the
visible selection and its search fields. It observes the changes that ActiveForm makes after validation. The widget adds
no CSS classes to the original select: Bootstrap ActiveField classes such as `form-control` and the error class come
from the form's `inputOptions` and validation state. Select2 emits `change` on the original select, which ActiveForm
uses for validation; server validation remains authoritative. For custom validation, update the original select's
`aria-invalid` and error message together.

When focusing an invalid enhanced field, focus its visible selection. Handle ActiveForm's `afterValidate` event for this
purpose:

```javascript
jQuery('#selection-form').on('afterValidate', function (event, messages, errors) {
  if (errors.length) {
    var field = jQuery(this).find(errors[0].input);
    var instance = field.data('select2');
    if (instance) {
      instance.$selection.trigger('focus');
    }
  }
});
```

Use keyboard navigation, Enter, Escape, and clearing in your application's theme. Screen reader behavior depends on
Select2, the browser, and the assistive technology; verify that combination before claiming accessibility compliance.

## Events and repeated rendering

```php
use yii\web\JsExpression;
use Yii2\Extensions\Select2\Select2;

echo Select2::widget(
    [
        'name' => 'country',
        'options' => ['id' => 'country'],
        'items' => ['mx' => 'Mexico', 'ca' => 'Canada'],
        'clientEvents' => [
            'select2:select.application' => new JsExpression('function (event) { console.log(event.params.data.id); }'),
        ],
    ]
);
```

Handlers run on the original select, with `this` referring to that element. Function strings and `JsExpression` are
executable code: never construct them from user input. Use `JsExpression` for function-valued options such as AJAX
callbacks; ordinary strings in `clientOptions` remain strings.

Generated initialization calls the `yiiSelect2` jQuery plugin once per widget. The plugin replaces an existing instance
on the same element and binds `clientEvents` once, in the `.yiiSelect2` namespace. The widget uses DOM IDs directly, so
IDs containing colons or brackets work without CSS escaping. Keep every widget ID unique, including when rendering the
same model attribute more than once.

Before removing a field or replacing a Pjax fragment, clean up each affected widget:

```javascript
jQuery(document.getElementById('country')).yiiSelect2('destroy');
// Replace the fragment, then let its generated widget scripts initialize it.
```

For manual initialization, set `clientOptions=false`, then run after the assets and DOM are ready:

```javascript
jQuery(document.getElementById('country')).yiiSelect2(
  { width: '100%' },
  {
    'change.application': function () {
      console.log(this.value);
    },
  },
);
```

Manual initialization uses the options and events you pass; `clientEvents` apply only to generated initialization. Pass
the desired `language` and `theme` explicitly, and register `Yii2\Extensions\Select2\Themes\Select2BS5ThemeAsset` or
`Select2BS6ThemeAsset` on the PHP view before choosing `theme: 'bootstrap-5'` or `theme: 'bootstrap-6'`.

`yiiSelect2('destroy')` can be called repeatedly. It removes only the plugin's handlers, observer, and Select2 instance;
external handlers remain the application's responsibility. Reinitialization removes the events bound by the previous
`yiiSelect2()` call, so pass them again. Direct `jQuery(...).select2('destroy')` bypasses the plugin's observer cleanup;
use `yiiSelect2('destroy')` for widgets initialized through this package. Keep separate namespaces on application
handlers and remove only those you own. See
[Select2's lifecycle documentation](https://select2.org/programmatic-control/methods/).

## Language and assets

The application language becomes the default `clientOptions.language`. One translation file is registered after the full
Select2 build: the exact regional file when Select2 ships it, for example `pt-BR.js` for `pt-BR`, otherwise the base
file, for example `es.js` for `es-MX`; never both. Missing translations fall back to Select2's English messages. Set an
explicit language only when its translation asset is also registered; the bundle loads the application's language, not
every widget's override.

Custom themes, dropdown parents for modals, matching, templates, minimum input length, and selection limits are passed
through `clientOptions`. Keep Select2's default escaping for untrusted text; jQuery objects returned by custom templates
must be built with safe text insertion. See the [Select2 options API](https://select2.org/configuration/options-api/).

[Back to documentation](../README.md#documentation)
