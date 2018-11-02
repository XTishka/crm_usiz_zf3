(function ($) {

    var defaultParams = {
        title      : 'Are you sure?',
        message    : 'Do you want to continue?',
        type       : 'confirm',
        cancelText : 'Cancel',
        confirmText: 'Confirm',
        okayText   : 'OK',
        onCancel   : undefined,
        onConfirm  : undefined,
        onOkay     : undefined
    };

    $.modalConfig = function (params) {
        defaultParams = $.extend(defaultParams, params);
    };

    $.fn.modal = function (params) {

        var opt = $.extend(defaultParams, params);

        var $trigger = $(this),
            template = '<div class="modal-overlay">' +
                '<div class="modal-wrapper">' +
                '<div class="modal-header">{title}<button class="modal-close"></button></div>' +
                '<div class="modal-content">{message}</div>' +
                '<div class="modal-footer"></div></div>';

        function close($modal) {
            $modal.fadeOut(250, function () {
                if (typeof opt.onCancel === "function") {
                    opt.onCancel();
                }
                $(this).remove();
            });
        }

        function show($modal) {
            $('body').append($modal.css('visibility', 0));
            var $wrapper = $modal.find('.modal-wrapper');
            $modal.find('button:first-child').focus();
            $wrapper.css({
                top : ($(window).height() - $wrapper.height()) / 2 + 'px',
                left: ($(window).width() - $wrapper.width()) / 2 + 'px'
            });
            $(window).on('resize', function () {
                $wrapper.css({
                    top : ($(window).height() - $wrapper.height()) / 2 + 'px',
                    left: ($(window).width() - $wrapper.width()) / 2 + 'px'
                });
            });
            $modal.fadeIn(250, function () {
                $(document).on('keyup', function (e) {
                    if (e.keyCode === 27) {
                        close($modal);
                    }
                });
            });
        }

        function renderTemplate(data, template) {
            $.each(data, function (key, val) {
                template = template.replace(new RegExp('{' + key + '}', 'g'), val);
            });
            var $modal  = $(template),
                $footer = $modal.find('.modal-footer'),
                $close  = $modal.find('.modal-close');
            $close.on('click', function (e) {
                e.preventDefault();
                if (typeof opt.onCancel === "function") {
                    opt.onCancel();
                }
                close($modal);
            });

            if (opt.type === 'confirm') {
                var $confirm = $('<button class="modal-confirm">').text(opt.confirmText).appendTo($footer);
                $confirm.on('click', function (e) {
                    e.preventDefault();
                    if (typeof opt.onConfirm === "function") {
                        opt.onConfirm();
                    }
                    close($modal);
                });
                var $cancel = $('<button class="modal-cancel">').text(opt.cancelText).appendTo($footer);
                $cancel.on('click', function (e) {
                    e.preventDefault();
                    close($modal);
                });
            } else if (opt.type === 'alert') {
                var $okay = $('<button class="modal-okay">').text(opt.okayText).appendTo($footer);
                $okay.on('click', function (e) {
                    e.preventDefault();
                    if (typeof opt.onOkay === "function") {
                        opt.onOkay();
                    }
                    close($modal);
                });
            } else if (opt.type === 'window') {
                if ($trigger.attr('href')) {
                    $.get($trigger.attr('href'), function (data) {
                        var $content = $modal.find('.modal-content');
                        $content.html(data);
                        var $wrapper = $modal.find('.modal-wrapper');
                        $wrapper.css({
                            top : ($(window).height() - $wrapper.height()) / 2 + 'px',
                            left: ($(window).width() - $wrapper.width()) / 2 + 'px'
                        });
                        $content.find('input[type=submit]').addClass('btn btn-success');
                        $content.find('form').on('submit', function (e) {
                            e.preventDefault();
                            var $form = $(this);
                            $.ajax($(this).attr('action'), {
                                type   : $form.attr('method'),
                                data   : $form.serialize(),
                                success: close($modal)
                            });
                        });
                    });
                }
            }
            return $modal;
        }

        $(this).each(function () {

        $(this).on('click', function (e) {
                e.preventDefault();

                if ($(this).data('modal') !== undefined) {
                    opt.type = $(this).data('modal');
                }

                var title = $(this).data('title');
                if (title !== undefined && title.length) {
                    opt.title = title;
                }

                var message = $(this).data('message');
                if (message !== undefined && message.length) {
                    opt.message = message;
                }

                if (typeof opt.onConfirm !== "function") {
                    var href = $(this).attr('href');
                    if (href !== undefined && href.length) {
                        opt.onConfirm = function () {
                            window.location.href = href;
                        }
                    }
                }
                show(renderTemplate({title: opt.title, message: opt.message}, template));
            });

        });

        return this;
    }

})(jQuery);

$(document).ready(function () {
    $('[data-modal]').modal();
});