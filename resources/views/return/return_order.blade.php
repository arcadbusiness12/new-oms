@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12 col-sm-12 col-grid" id="return_order">
            <div class="card p-3 text-black">
                <div class="row">
                    <div class="col-sm-8">
                        <form name="form_return_order" id="form_return_order" action="{{ route('return.get') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="order_id" class="form-control text-center" style="background: none" autofocus="" autocomplete="off" placeholder="Scan By Order ID">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="return_barcode_scan" class="form-control text-center" style="background: none; border:none !important" autofocus="on" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{--card end--}}
            @if(Session::has('message'))
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
            <div class="card no-b mt-3">
                <div class="row" id="return_order_row">

                </div>
            </div> {{--card end--}}
        </div>
    </div>
</div>
{{--  @include("orders.popup_generate_awb")  --}}
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).delegate('.product-return-bacode-checkbox', 'change', function(e) {
            //alert("first");
            if($('.product-return-bacode-checkbox:not(:checked)').length == 0){
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
            //show hide barcode and button
            var total_checkbox = $('.product-return-bacode-checkbox');
            var checked = $('.product-return-bacode-checkbox:checked');
            //alert(total_checkbox.length);
            if (checked.length == total_checkbox.length) {
                //$('#return_order .row_update_returned').slideDown(250);
                $('#return_order .row_update_returned').removeClass("d-none");
            } else {
                //$('#return_order .row_update_returned').slideUp(250);
                $('#return_order .row_update_returned').addClass("d-none");
            }
        });

    });
    //----------------------
    $(document).delegate('#form_return_order', 'submit', function(e) {
        e.preventDefault();
        $.ajax({
            method: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
        }).done(function(html) {
            $('#return_order_row').html(html);
            JsBarcode(".barcode").init();
            $('[name="return_barcode_scan"]').focus();
        });
    });
</script>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
@endpush
