(function ($) {

    $.fn.dropdown = function (params) {

        var opt = $.extend({
            autoHide      : true,
            mouseOver     : false,
            leaveTimeout  : 3500,
            toggleSelector: '.dropdown-toggle',
            listSelector  : '.dropdown-list'
        }, params);

        $(this).each(function () {

            var timeout,
                $dropdown = $(this).attr('aria-expanded', 'false'),
                $toggle = $(this).find(opt.toggleSelector),
                $list = $(this).find(opt.listSelector);

            $toggle.on(opt.mouseOver ? 'mouseover' : 'click', function (e) {
                $list.slideDown(100, function () {
                    $dropdown.attr('aria-expanded', 'true');
                });
                clearTimeout(timeout);
            });

            /*
            var listWidth = $list.outerWidth(true);
            $list.css({left: ($toggle.outerWidth(true) - listWidth) / 2});
            */

            $list.hide();

            $list.on('click', function (e) {
                //e.stopPropagation();
            });

            $dropdown.on('mouseleave', function () {
                timeout = setTimeout(function () {
                    $list.slideUp(100, function () {
                        $dropdown.attr('aria-expanded', 'false');
                    });
                }, opt.leaveTimeout);
            }).on('mouseenter', function () {
                clearTimeout(timeout);
            });

            $(document).on('click', function () {
                if (opt.autoHide === true) {
                    $dropdown.filter('[aria-expanded=true]').find(opt.listSelector).slideUp(250, function () {
                        $dropdown.attr('aria-expanded', 'false');
                    });
                }
            });

        });

        return this;
    }

})(jQuery);