DoctorNet = {
    Basket: {
        calculateAjax: null,
        options: {
            basketSelector: '.js-small-basket',
            productAddSelector: '.js-add-to-cart',
            favoriteAddSelector: '.js-add-to-favorite',
            favoriteRemoveSelector: '.js-remove-from-favorite'
        },
        add: function (e, id) {
            e.preventDefault();

            $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'add',
                    id: id
                },
                success: function (data) {
                    if (!data.error) {
                        $(DoctorNet.Basket.options.basketSelector).replaceWith(data);
                    }
                }
            });
        },
        addToFavorite: function (e, id, domElement) {
            e.preventDefault();

            $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'add',
                    id: id,
                    favorite: 'y'
                },
                success: function (data) {
                    $(DoctorNet.Basket.options.basketSelector).replaceWith(data);
                }
            });
        },
        removeFromFavorite: function (e, id, domElement) {
            e.preventDefault();

            $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'delete',
                    favorite: 'y',
                    productId: id
                },
                success: function (data) {
                    DoctorNet.Basket.reload();
                }
            });
        },
        reload: function () {
            $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'reload'
                },
                success: function (data) {
                    $(DoctorNet.Basket.options.basketSelector).replaceWith(data);
                }
            });
        },
        init: function () {
            var body = $('body');

            body.on('click', DoctorNet.Basket.options.productAddSelector, function (e) {
                DoctorNet.Basket.add(e, $(this).data('id'));
            });

            body.on('click', DoctorNet.Basket.options.favoriteAddSelector, function (e) {
                DoctorNet.Basket.addToFavorite(e, $(this).data('id'), this);
            });

            body.on('click', DoctorNet.Basket.options.favoriteRemoveSelector, function (e) {
                DoctorNet.Basket.removeFromFavorite(e, $(this).data('id'), this);
            });

            body.on('click change', '.js-update', function (e) {
                DoctorNet.Basket.changeQuantity(e, this);
            });

            body.on('click', '.js-delete', function (e) {
                DoctorNet.Basket.delete(e, this);
            });
        },
        recalculate: function () {
            var sum = 0;

            $('.js-basket-row').each(function () {
                $this = $(this);

                cost = DoctorNet.Basket.getQuantity($this) * parseInt($this.data('price'));
                sum += cost;
                $this.find('.js-price').html(DoctorNet.Tools.formatPrice(cost));
            });

            $('.js-sum').html(DoctorNet.Tools.formatPrice(sum));
        },
        getQuantity: function ($row) {
            var $quantity = $row.find('.js-quantity'),
                quantity = $quantity.prop('tagName') == 'INPUT' ? $quantity.val() : $quantity.text();

            return isNaN(parseInt(quantity)) ? 1 : parseInt(quantity);
        },
        changeQuantity: function (e, domElement) {
            e.preventDefault();

            var $row = $(domElement).parents('.js-basket-row'),
                id = $row.data('id'),
                quantity = DoctorNet.Basket.getQuantity($row);

            if (DoctorNet.Basket.calculateAjax !== null) {
                DoctorNet.Basket.calculateAjax.abort();
            }

            DoctorNet.Basket.calculateAjax = $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'recalculate',
                    id: id,
                    quantity: quantity
                }
            });

            DoctorNet.Basket.recalculate();
        },
        delete: function (e, domElement) {
            e.preventDefault();

            if (!confirm('Вы действительно хотите удалить этот товар из корзины?')) {
                return;
            }

            var $row = $(domElement).parents('.js-basket-row'), id = $row.data('id');

            $.ajax({
                url: '/local/tools/basket.php',
                type: 'post',
                data: {
                    action: 'delete',
                    id: id
                }
            });

            $row.remove();

            if ($('.js-basket-row').length > 0) {
                DoctorNet.Basket.recalculate();
            } else {
                Location.reload();
            }
        }
    },
    Tools: {
        options: {
            priceFormat: {
                decimal: 0,
                separator: ' ',
                decimalPoint: '.',
                formatString: '# <span class="b-rub">Р</span>'
            }
        },
        formatPrice: function (price) {
            var decimal = DoctorNet.Tools.options.priceFormat.decimal,
                separator = DoctorNet.Tools.options.priceFormat.separator,
                decpoint = DoctorNet.Tools.options.priceFormat.decimalPoint,
                format_string = DoctorNet.Tools.options.priceFormat.formatString;

            var r = parseFloat(price);

            var exp10 = Math.pow(10, decimal);
            r = Math.round(r * exp10) / exp10;

            rr = Number(r).toFixed(decimal).toString().split('.');

            b = rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g, "\$1" + separator);

            r = (rr[1] ? b + decpoint + rr[1] : b);
            return format_string.replace('#', r);
        }
    }
};

$(function () {
    DoctorNet.Basket.init();
});