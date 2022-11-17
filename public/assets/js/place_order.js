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
        $(".customer_search_table").html(data);
    });
});

function loadAreas() {
    var city_id = $('#city_id').val();
    alert(city_id);
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/load/areas",
        data: { city_id: city_id },
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        // $(".customer_search_table").html(data);
        // var data = JSON.parse(data);
        var area_html = "";
        // alert(data);
        if (data) {
            for (const row of data) {
                area_html += "<option value='" + row.id + "'>" + row.name + "</option>";
            }
            $('#area').html(area_html);
        }
    });
}
$(document).on('submit', '#customer_save', function(e) {
    e.preventDefault();
    const request_data = $(this).serialize();
    $.ajax({
        method: "POST",
        url: APP_URL + "/place/order/save/customer",
        cache: false,
        data: request_data + '&store_id=' + store,
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        shippingPayment();
    });
});

function shippingPayment() {
    $.ajax({
        method: "GET",
        url: APP_URL + "/place/order/shipping/payment",
        cache: false,
        data: 'store_id=' + store,
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(data) {
        console.log(data);
        $('#step-5').html(data);
    });
}