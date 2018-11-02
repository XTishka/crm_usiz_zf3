(function ($) {

    $.fn.rower = function (params) {

        var opt = $.extend({
            templateSelector: '.rower-template',
            rowSelector     : '.form-row',
            removeSelector  : 'button[name$="[remove]"]',
            addButtonClass  : 'btn btn-secondary',
            addButtonText   : 'Add new row',
            placeholder     : '__index__',
            onInsert        : undefined
        }, params);

        var resetBrackets = function ($rowset) {
            $rowset.each(function (key) {
                $(this).find(':input').attr("name", function () {
                    if ($(this).attr("name") !== undefined) {
                        return $(this).attr("name").replace(/\[[^\]]+\]/, '[' + key + ']');
                    }
                });
            });
        };

        $(this).each(function () {

            var $section   = $(this),
                $addButton = $(document.createElement('span')).addClass(opt.addButtonClass).text(opt.addButtonText)/*.hide()*/,
                $template  = $section.find(opt.templateSelector);

            $section.on('click', opt.removeSelector, function (e) {
                e.preventDefault();
                $(this).closest(opt.rowSelector).fadeOut(250, function () {
                    $(this).detach();
                    var $rowset = $section.find(opt.rowSelector);
                    !$rowset.length && $addButton.fadeIn() || resetBrackets($rowset);
                });
            });

            $section.append($addButton);

            $section.on('keyup', opt.rowSelector + ':last', function (e) {
                $(this).find(':input').serializeArray().map(function (object) {
                    return object.value;
                }).join('').length /*&& $addButton.fadeIn() || $addButton.fadeOut();*/
            });

            $section.find(opt.rowSelector + ':last :input').serializeArray().map(function (object) {
                return object.value;
            }).join('').length /*&& $addButton.fadeOut() || $addButton.fadeIn();*/

            $addButton.on('click', function (e) {
                e.preventDefault();
                var $html = $($template.html().replace(new RegExp(opt.placeholder, 'g'), $section.find(opt.rowSelector).length));
                $(this)/*.hide()*/.before($html);
                if (typeof opt.onInsert  === "function") {
                    opt.onInsert($html);
                }
            });

        });

        return this;
    }

})(jQuery);