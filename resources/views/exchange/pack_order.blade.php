@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 col-grid">
            <div class="card text-black" id="pack_order">
                <div class="row container-fluid">
                    <?php if(Session::has('message')) { ?>
                    <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo Session::get('message') ?>
                    </div>
                    <?php } ?>
                    <div class="col-sm-8 col-grid">
                        <form name="form_pack_order" id="form_pack_order" action="{{ URL::to('/exchange/get/pack') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="order_id" class="form-control text-center mt-3" style="background: none" autofocus="" autocomplete="off" placeholder="Scan By Order ID">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4 col-grid">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="pick_barcode_scan" class="form-control text-center no-b" style="background: none" autofocus="on" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-heading">Pack Order</div>
                <div class="row" id="pack_order_row">

                </div>
            </div> {{--card end--}}
        </div>
    </div>
</div>
@endsection
@push('scripts')
@if( session('packed_order_id') != "" )
<input type="hidden" value="{{ URL::to('exchange/print/awb?submit=awb&order_id[]='.session('packed_order_id')) }}" id="popup_url">
<script>
var popup_url = $('#popup_url').val();
//alert(popup_url);
var popup_window = window.open(popup_url, 'Print AWB', 'width=500,height=600');
popup_window.focus();
popup_window.print();
popup_window.focus();
</script>
@endif
<script type="text/javascript">
$(document).delegate('#form_pack_order', 'submit', function(e) {
    e.preventDefault();
    $.ajax({
        method: "POST",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
    }).done(function(html) {
        $('#pack_order_row').html(html);
        $('#pack_order .row_update_picked').hide();
        JsBarcode(".barcode").init();
        $('[name="pick_barcode_scan"]').focus();
    });
});
$(document).delegate('.product-pack-bacode-checkbox', 'change', function() {
    var total_checkbox = $('.product-pack-bacode-checkbox');
    var checked = $('.product-pack-bacode-checkbox:checked');
    if (checked.length == total_checkbox.length) {
        $('#pack_order .row_update_picked').slideDown(250);
    } else {
        $('#pack_order .row_update_picked').slideUp(250);
    }
});
$(document).ready(function(){
    $(document).delegate('.product-pack-bacode-checkbox', 'change', function(e) {
        if($('.product-pack-bacode-checkbox:not(:checked)').length == 0){
            setTimeout(function(){
                $('html,body').animate({
                    scrollTop: $("#submit").offset().top - $(window).height()/2
                }, 500);
            }, 100);
        }else{
            $('html,body').animate({
                scrollTop: $(this).offset().top - $(window).height()/2
            }, 500);
        }
    });
});
</script>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
@endpush
