(function ($) {

    $.fn.between = function (params) {

        var opt = $.extend({
            separator : '-',
            count     : 2,
            type      : 'number',
            colWrapper: '<div class="col-md">',
            rowWrapper: '<div class="row">'
        }, params);

        var $base = $(this);

        $base.each(function () {
            var $row   = $(opt.rowWrapper).addClass('between-wrapper'),
                $input = $(this).attr('type', 'hidden'),
                value  = $input.val();

            $row.insertBefore($input);

            if (value) value = value.split(opt.separator);

            var $mask = $(document.createElement('input')).attr({
                type: opt.type,
                min : 0,
                max : 100,
                step: 0.001
            });

            var setInputValue = function () {

                var $maskSet = $row.find('input[data-order]'),
                    values   = $maskSet.map(function () {
                        return this.value;
                    }).toArray();

                for (var i = 0; i < values.length; i++) {
                    if (undefined !== values[i + 1] && values[i + 1] < values[i]) {
                        values[i + 1] = values[i];
                        $maskSet.get(i + 1).value = values[i];
                    }
                }

                $input.val(values.join(opt.separator));
            };

            $mask.on('change', setInputValue);

            for (var i = 0; i < opt.count; i++) {
                var $curMask = $mask.clone(true);
                $curMask.attr('name', $input.attr('name') + '-between-order-' + i).attr('data-order', i);
                if (undefined !== value[i]) $curMask.val(value[i]); else $curMask.val(value[i - 1]);
                $row.append($(opt.colWrapper).append($curMask));
            }

            setInputValue();

            var $state = {
                betweenDestroy: function () {
                    $input.siblings('.between-wrapper').remove();
                },
                betweenUpdate : function (params) {
                    $input.siblings('.between-wrapper').remove();
                    //$input.off('between()');
                    $input.between(params);
                }
            };

            $base.extend($state);
        });

        return $base;
    };


})(jQuery);