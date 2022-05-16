$(function() {
    var products = [];
    var sign = '€';
    var currency = 'EUR';
    var totalAmount = 0.00;
    var $cart = $("#cart");
    var $cartQuantity = $("#cartQuantity");
    var $paypalBtnContainer = $("#paypal-button-container");
    var renderCart = function() {
        $cart.html('<p class="text-center mb-2 alert alert-warning py-1">Количката е празна.</p>');
        $paypalBtnContainer.addClass('d-none');
        $cartQuantity.text('');

        var $output = '';
        totalAmount = 0.00;
        var quantity = 0;

        for (let product of products) {
            $output += '<li class="list-group-item list-group-item-action"><div class="row" data-id="' + product.id + '">'
                + '<div class="col-5">' + product.title + '</div>'
                + '<div class="col-2 text-center">&times;' + product.quantity + '</div>'
                + '<div class="col-3 text-end"><strong>' + sign + product.price.toFixed(2) + '</strong></div>'
                + '<div class="col-2 text-nowrap text-end">'
                + '<button class="remove btn btn-danger btn-sm px-1 py-0"><i class="fa-solid fa-xmark fa-fw"></i></button>'
                + '</div></li>'
            ;

            totalAmount += (product.price * product.quantity);
            quantity += product.quantity;
        }

        if (quantity > 0) {
            $cartQuantity.text(quantity);

            $cart.html('<ul class="list-group mb-3">' + $output + '</ul>');
            $cart.append(
                '<ul class="list-group mb-3 pb-3 border-bottom"><li class="list-group-item list-group-item-action list-group-item-primary">'
                + '<div class="row">'
                + '  <div class="col">Общо:</div>'
                + '  <div class="col-auto text-end"><strong>' + sign + totalAmount.toFixed(2) + '</strong></div>'
                + '</div></li>'
                + '</ul>'
            );
            $paypalBtnContainer.removeClass('d-none');
        }
    };

    $("#cart").on('click', ".remove", function(e) {
        var $item = $(this).closest('.row');
        var id = parseInt($item.data('id'), 10);

        products = products.filter(product => product.id !== id).slice();
        renderCart();
    });

    $(".add-to-cart").on("click", function(e) {
        var $product = $(this).closest('.card');
        var id = parseInt($product.data('id'), 10);
        var title = $product.data('title');
        var quantity = parseInt($product.data('quantity'), 10);
        var price = parseFloat($product.data('price'));

        var found = products.findIndex(product => product.id === id);
        if (found >= 0 && quantity > products[found].quantity) {
            products[found].quantity += 1;
        } else {
            products.push({
                id: id,
                title: title,
                price: price,
                quantity: 1,
            });
        }

        renderCart();
    });

    renderCart();
    paypal.Buttons({
        style: {
            layout:  'vertical',
            color:   'gold',
            shape:   'pill',
            label:   'pay'
        },
        onInit: function() {
            renderCart();
        },
        createOrder: function(data, actions) {
            if (totalAmount < 0.00)  {
                return null;
            }

            return actions.order.create({
                intent: "CAPTURE",
                purchase_units: [{
                    description: "Products from AdvanceAcademy.bg",
                    amount: {
                        value: totalAmount.toFixed(2),
                        currency_code: currency,
                        breakdown: {
                            item_total: {
                                currency_code: currency,
                                value: totalAmount.toFixed(2),
                            }
                        }
                    },
                    items: products.map(product => {
                        return {
                            name: product.title,
                            unit_amount: {
                                currency_code: currency,
                                value: product.price,
                            },
                            quantity: product.quantity,
                            sku: 'PRODUCT-ID-' + product.id
                        }
                    }),
                }],
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                console.log("Transaction Details", details);
                products = [];
                renderCart();
                // alert('Transaction completed by ' + details.payer.name.given_name);
            });
        },
        onCancel: function(data) {
            console.log("Transaction Canceled", data);
        },
        onError: function (error) {
            alert(error);
        }
    }).render('#paypal-button-container');

});
