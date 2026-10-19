(function ($) {
    'use strict';

    var NAMESPACE = '.yiiSelect2';
    var OBSERVER = 'yiiSelect2Observer';
    var OBSERVED_ATTRIBUTES = [
        'aria-label',
        'aria-labelledby',
        'aria-describedby',
        'aria-invalid',
        'aria-required',
        'required',
    ];

    function syncAccessibility($input) {
        var instance = $input.data('select2');
        if (!instance) {
            return;
        }

        var element = $input[0];
        var labels = Array.from(element.labels || []).map(function (label, index) {
            label.id = label.id || element.id + '-label-' + index;
            return label.id;
        });
        var ariaLabel = element.getAttribute('aria-label');
        var labelledBy = element.getAttribute('aria-labelledby') || (ariaLabel ? '' : labels.join(' '));
        var rendered = instance.$selection.find('.select2-selection__rendered').attr('id');
        var required = String(element.required || element.getAttribute('aria-required') === 'true');
        var $controls = instance.$selection
            .add(instance.$selection.find('.select2-search__field'))
            .add(instance.$dropdown.find('.select2-search__field'));

        $controls.each(function () {
            var $control = $(this);
            var names = labelledBy ? [labelledBy] : [];
            if (rendered && this === instance.$selection[0] && !element.multiple && (labelledBy || !ariaLabel)) {
                names.push(rendered);
            }
            if (names.length) {
                $control.attr('aria-labelledby', names.join(' '));
            } else {
                $control.removeAttr('aria-labelledby');
            }
            ['aria-label', 'aria-describedby', 'aria-invalid'].forEach(function (attribute) {
                var value = element.getAttribute(attribute);
                if (value === null) {
                    $control.removeAttr(attribute);
                } else {
                    $control.attr(attribute, value);
                }
            });
            $control.attr('aria-required', required);
        });
    }

    function destroy($input) {
        var observer = $input.data(OBSERVER);
        if (observer) {
            observer.disconnect();
            $input.removeData(OBSERVER);
        }
        $input.off(NAMESPACE);
        if ($input.data('select2')) {
            $input.select2('destroy');
        }
    }

    function bind($input, events) {
        Object.keys(events).forEach(function (event) {
            var names = event
                .split(/\s+/)
                .filter(Boolean)
                .map(function (name) {
                    return name + NAMESPACE;
                })
                .join(' ');
            $input.on(names, events[event]);
        });
    }

    function initialize($input, options, events) {
        destroy($input);
        $input.select2(options || {});
        bind($input, events || {});

        // Select2 keeps the synchronized nodes across changes and opening; only attribute changes need a re-sync.
        var observer = new MutationObserver(function () {
            syncAccessibility($input);
        });
        observer.observe($input[0], { attributes: true, attributeFilter: OBSERVED_ATTRIBUTES });
        $input.data(OBSERVER, observer);
        syncAccessibility($input);
    }

    /**
     * Initializes Select2 on each matched element, or removes it with the `'destroy'` command.
     *
     * Re-initialization destroys the previous instance first, so handlers passed in `events` are bound exactly once.
     *
     * @param {Object|string} options Select2 options, or `'destroy'`.
     * @param {Object} [events] Event handlers keyed by space-separated event names.
     * @returns {jQuery} The matched elements, for chaining.
     */
    $.fn.yiiSelect2 = function (options, events) {
        if (options === 'destroy') {
            return this.each(function () {
                destroy($(this));
            });
        }

        return this.each(function () {
            initialize($(this), options, events);
        });
    };
})(jQuery);
