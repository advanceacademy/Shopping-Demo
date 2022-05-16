$(function() {
    var products = [];
    var sign = '';
    var signRight = ' лв.';
    var currency = 'EUR';
    var totalAmount = 0.00;
    var currencyRatio = 1.95583;
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
                + '<div class="col-3 text-end"><strong>' + sign + product.price.toFixed(2) + signRight +  '</strong></div>'
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
                + '  <div class="col-auto text-end"><strong>' + sign + totalAmount.toFixed(2) + signRight + '</strong></div>'
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

    $("#menu").each(function(i, v) {
        var $menu = $(v);
        var $modalLogin = $("#modalLogin");
        var $modalRegister = $("#modalRegister");
        var $loginForm = $modalLogin.find("form");
        var $registerForm = $modalRegister.find("form");
        var localStorageName = 'user';

        function login(name, suername) {
            $menu.find('.guest').addClass('d-none');
            $menu.find('.profile').removeClass('d-none');
            $("#userName").text(name + ' ' + suername);
            $modalLogin && $modalLogin.modal('hide');
        }

        function logout() {
            $menu.find('.guest').removeClass('d-none');
            $menu.find('.profile').addClass('d-none');
            $("#userName").text('');
            try {
                localStorage.removeItem(localStorageName);
            } catch (e) {}
        }

        $loginForm.on("submit", function(e) {
            e.preventDefault();

            $loginForm.find('.alert').addClass('d-none');

            var email = $loginForm.find('#inputEmail').val() || '';
            var password = $loginForm.find('#inputPassword').val() || '';

            if (!email.length || !password.length) {
                $loginForm.find('.alert')
                    .removeClass('d-none')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text('Попълнете Email и Парола');
            } else {
                $.ajax({
                    url: '/api/users/token',
                    method: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    success: function(data, textStatus) {
                        localStorage.setItem(localStorageName, JSON.stringify(data));
                        login(data.user.name, data.user.surname);
                    },
                    error: function(jqXHR, textStatus) {
                        $loginForm.find('.alert')
                            .removeClass('d-none')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .text(((jqXHR || {}).responseJSON || {}).message);
                    }
                });
            }

            return false;
        });

        $registerForm.on("submit", function(e) {
            e.preventDefault();

            $registerForm.find('.alert').addClass('d-none');

            var email = $registerForm.find('#inputRegisterEmail').val() || '';
            var password = $registerForm.find('#inputRegisterPassword').val() || '';
            var name = $registerForm.find('#inputRegisterName').val() || '';
            var surname = $registerForm.find('#inputRegisterSurname').val() || '';
            var phone = $registerForm.find('#inputRegisterPhone').val() || '';

            if (!email.length || !password.length || !name.length || !surname.length || !phone.length) {
                $registerForm.find('.alert')
                    .removeClass('d-none')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text('Попълнете всички полета.');
            } else {
                $.ajax({
                    url: '/api/users',
                    method: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({
                        email: email,
                        password: password,
                        name: name,
                        surname: surname,
                        phone: phone,
                    }),
                    success: function(data, textStatus) {
                        $modalRegister && $modalRegister.modal('hide');
                    },
                    error: function(jqXHR, textStatus) {
                        $registerForm.find('.alert')
                            .removeClass('d-none')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .text(((jqXHR || {}).responseJSON || {}).message);
                    }
                });
            }

            return false;
        })

        $("#logoutUser").on("click", function(e) {
            logout();
        });

        try {
            var data = JSON.parse(localStorage.getItem(localStorageName));
            if (data && ((data || {}).user || {}).id) {
                $.ajax({
                    url: '/api/users/' + data.user.id,
                    method: 'GET',
                    contentType: 'application/json',
                    dataType: 'json',
                    headers: {
                        'Authorization': 'Bearer ' + data.token,
                    },
                    success: function(user, textStatus) {
                        data.user = user;
                        localStorage.setItem(localStorageName, JSON.stringify(data));
                        login(data.user.name, data.user.surname);
                    },
                    error: function(jqXHR, textStatus) {
                        logout();
                    }
                });
            }
        } catch (e) {

        }
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

            var total = 0.00;
            var items = products.map(product => {
                var itemPrice = product.price / currencyRatio;
                total += (itemPrice * product.quantity);

                return {
                    name: product.title,
                    unit_amount: {
                        currency_code: currency,
                        value: itemPrice.toFixed(2),
                    },
                    quantity: product.quantity,
                    sku: 'PRODUCT-ID-' + product.id
                }
            });

            return actions.order.create({
                intent: "CAPTURE",
                purchase_units: [{
                    description: "Products from AdvanceAcademy.bg",
                    amount: {
                        value: total.toFixed(2),
                        currency_code: currency,
                        breakdown: {
                            item_total: {
                                currency_code: currency,
                                value: total.toFixed(2),
                            }
                        }
                    },
                    items: items,
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
