/**
 * Created by Varfann on 03.01.2016.
 */
$(function () {
    $('body').on('click', '.js-load-prods-btn', function (e) {
        e.preventDefault();
        var a = $(this),
            prevblock = a.parent().parent().find('.js-section-list'),
            win = $(window);
        $.ajax({
            url: a.attr('data-url'),
            method: 'GET',
            cache: true,
            success: function (html) {
                if (win.width() <= 960) {
                    $('body').animate({
                        scrollTop: a.offset().top - 70
                    }, 800);
                } else {
                    $('body').animate({
                        scrollTop: a.offset().top - 20
                    }, 800);
                }
                var pagen = a.data('pagen'),
                    $sections = $(html).find('.js-section-list'),
                    $urls = $(html).find('.js-load-prods-btn'),
                    $section = $sections.filter('[data-pagen=' + pagen + ']'),
                    $url = $urls.filter('[data-pagen=' + pagen + ']');
                prevblock.append($section.find('>*'));
                var dataUrl = $url.attr('data-url');
                if (dataUrl != undefined) {
                    a.attr('data-url', dataUrl);
                } else {
                    a.hide();
                }
            }
        });
    });
});