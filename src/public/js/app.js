$(function() {
    var totalAmount = 0.00;
    var $cart = $("#cart");

    $(".add-to-cart").on("click", function(e) {
        var $product = $(this).closest('.card');
        var title = $product.find('h2').text();
        totalAmount = parseFloat($(this).data('price')).toFixed(2);
        $cart.html("<p>" + title + ': <strong>€' + totalAmount + "</strong></p>");
    })

    paypal.Buttons({
        createOrder: function(data, actions) {
            if (totalAmount < 0.00)  {
                return null;
            }

            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '' + totalAmount,
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                console.log("Transaction Details", details);
                // alert('Transaction completed by ' + details.payer.name.given_name);
            });
        }
    }).render('#paypal-button-container');

});
