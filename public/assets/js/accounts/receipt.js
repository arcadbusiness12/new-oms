var deliver_orders = 0;
$(document).ready(function() {


    /*
     * Hndle file upload
     */

    $("#deliverd_orders_file").change(function() {
        swal({ title: "<h2>Please Wait uploading file...</h3>", html: true, text: loader, showConfirmButton: false });
        var form = document.forms.namedItem("deliverOrderFileUpload");
        // console.log(form); return;
        var formdata = new FormData(form);

        $.ajax({
            url: APP_URL + '/accounts/process/courier/excel/file',
            data: formdata,
            async: true,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                //if error response will get object
                // console.log(response);
                if (typeof response == 'object') {
                    swal({ title: "<h2>Error!</h3>", html: true, text: response.error.message, type: "error" });
                } else {
                    document.getElementById("deliverOrderFileUpload").reset();
                    swal.close();
                    console.log(response);

                    // console.log(form);
                    // var f = response.split(',');
                    // console.log(f);
                    $('#excelSheetModal_content').html(response);
                    // $('#form_content').html(f[1]);
                    var nonShippedFound = false;

                    $('#excelSheetModal').modal('show');
                }

            },
        });
    });

}); // document ready

function fetchOrderDetails(orderID) {
    var orderId = $('#orderId').val();
    var shipping_provider_name = $("#shipping_provider_name").val();
    if (orderId == "") {
        return;
    }
    $.ajax({
        method: 'POST',
        url: APP_URL + '/orders/get-order-detail-deliver',
        data: { orderId: orderId, shipping_provider_name: shipping_provider_name },
        headers: {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    }).success(function(response) {
        var check_string = response.slice(0, 7);
        if (response == 0) {
            alert("Order #" + orderId + " is not in shipped status, only shipped status can be deliver.");
            return;
        }
        if (response != 0) {
            if (check_string != "<script") {
                deliver_orders += 1;
                $('#deliverOrders .order-counter').text(deliver_orders + ' Order(s)');
            }
            $('#deliverd_order_response').prepend(response);
        }
        $('.scan-response-container').removeClass('hidden');
        $('#orderId').val('');
    });
}