(function ($) {

    $.fn.slideMenu = function () {

        var $li = $(this).children('li');

        $li.filter(':not(.active)').filter(':not(.no-hide)').find('ul').hide();

        $li.on('click', function () {
            $(this).children('ul').slideToggle(function () {
                var $current = $(this).parent('li');
                if ($(this).is(':visible')) {
                    $current.addClass('active');
                    $current.siblings('li:not(.no-hide)').removeClass('active').children('ul').slideUp();
                } else {
                    $current.removeClass('active');
                }
            });
        });

    }

})(jQuery);