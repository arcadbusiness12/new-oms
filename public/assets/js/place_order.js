var store = $('#txtbox_store_id').val();
$(document).on('keyup', '#product_model', function() {
    _this = $(this);
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/get/product/sku",
        data: {
            product_sku: _this.val()
        },
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data.skus);
        html = '';
        if (data.skus) {
            $.each(data.skus, function(k, v) {
                html += '<option value="' + v + '">';
            });
            $('#product_models').html(html);
        }
    });
});
$(document).on('submit', '#filter_products', function(e) {
    e.preventDefault();
    var product_sku = $('#product_model').val();
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/ajax/search",
        data: {
            product_sku: product_sku,
            store: store
        },
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data);
        $(".product_search_table").html(data);
    });
});
$(document).on('submit', '#frm_add_to_cart', function(e) {
    e.preventDefault();
    var form_data = $(this).serialize();
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/add/to/cart",
        data: form_data + '&store=' + store,
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        if (data.status) {} else if (data.status == 0) {
            $('#alert_error_cart').removeClass('d-none');
            $('#alert_error_cart span').html(data.msg);
        }
        getCart();
    });
});

function getCart() {
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/get/cart",
        data: { store: store },
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data);
        $('#step-2-cart').html(data);
        $('#step-2').addClass("show");
    });
}
$(document).on('click', '.btn-cart-remove', function() {
    var cart_id = $(this).attr('cart-id');
    var $this = $(this);
    swal({
            icon: 'error',
            title: "Are you sure?",
            text: "You want to remove this item from cart.",
            showCancelButton: true,
            closeOnConfirm: true,
            animation: "slide-from-top"
        },
        function(inputValue) {
            if (inputValue) {
                $.ajax({
                    method: "POST",
                    url: APP_URL + "/place/order/remove/cart",
                    data: { cart_id: cart_id },
                    headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
                }).done(function(data) {
                    console.log(data);
                    if (data.status) {
                        $this.closest("tr").hide(1000);
                        getCart();
                    }
                });
            }
        }
    );
});
$(document).on('click', '.btn-cart-update', function() {
    var cart_id = $(this).attr('cart-id');
    var quantity = $('#product_quantity' + cart_id).val();
    // $('#step-2-cart').html("<center><h4>Updating please wait...</center>");
    $('#alert_error_cart').addClass('d-none');
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/update/cart",
        data: { cart_id: cart_id, quantity: quantity },
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data);
        if (data.status) {} else if (data.status == 0) {
            $('#alert_error_cart').removeClass('d-none');
            $('#alert_error_cart span').html(data.msg);
        }
        getCart();
    });
});
$(document).on('submit', '#filter_customers', function(e) {
    e.preventDefault();
    var name = $('#customer_name').val();
    var email = $('#customer_email').val();
    var mobile = $('#customer_mobile').val();
    const request_data = { name: name, email: email, mobile: mobile, store: store };
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/search/customer",
        data: request_data,
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data);
        $(".product_search_table").html(data);
    });
});