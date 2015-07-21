require(['jquery'], function($) {
    var $deliveryAddress = $('#delivery-address');
    $('#ekyna_order_order_sameAddress').on('change', function() {
        if ($(this).prop('checked')) {
            $deliveryAddress.hide();
        } else {
            $deliveryAddress.show();
        }
    }).trigger('change');
});