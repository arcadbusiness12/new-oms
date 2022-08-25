
@foreach($inventory_product as $prod)

<form method="post" action="{{route('edit.inventory.product', $prod->product_id)}}" enctype="multipart/form-data">

  @endforeach

  {{ csrf_field() }}

  <div id="alert-response"></div>

  <?php $unique = uniqid(); ?>

  <div class="product_list_row">

   <div class="product_row">

    @foreach($inventory_product as $products)

    <div class="row">

     <div class="col-xs-4 col-sm-2" style="padding: 0">

      <input type="file" name="image" class="input-image" data-id="" value="{{$products->image}}" style="position: absolute;height: 100%;width: 100%;opacity: 0;cursor: pointer;" />

      <img id="uploadable" src="{{URL::asset('uploads/inventory_products/'.$products->image)}}" width="150px" style="float: right;" />

    </div>

    <div class="col-xs-10 col-sm-10">

      <div class="row">

       <div class="col-xs-8">

        <input type="text" name="sku" class="form-control" value=" {{$products->sku}}" placeholder="Enter Product SKU" required/>

      </div>

      @endforeach

    </div>

    <div class="form-group">

     <div class="row">

      <div class="col-lg-4">

       <label for="">Colors</label>

       <?php $var = DB::table('oms_inventory_product')->select('option_name','option_value')->where('product_id', $products->product_id)->get();

       ?>

       @foreach($var as $va)

       @endforeach



       <select name="option_color" id="option_color" class="form-control" autocomplete="off" readonly>



        {{--  @foreach($option_value as $color)


         @endforeach --}}
         <option>{{ $prod->option_name }}</option>

       </select>

     </div>

     <div class="col-lg-4">

       <div class="input-group mb-3">

        <label for="">Select Size</label>

        <select name="option_name" id="taken_id" class="form-control option_name" onchange="getMessage()" readonly>

          @foreach($option_detail as $options)
          @if($options->id == $va->option_value)
          <option value="{{$options->id}}" {{ ( $options->id == $va->option_value ) ? 'selected' : '' }}>{{$options->option_name}}</option>
          @endif

          @endforeach

        </select>

      </div>

      <div id="newRow" style="width: 210%!important;"></div>

    </div>

    <div class="col-md-4" style="margin-top:27px;">

     {{-- <button type="button" class="btn btn-info" onclick="getMessage(1)">More Options</button> --}}

   </div>

 </div>

</div>

</div>

</div>

<div id="htmlpart"></div>

<div class="manually_option_row"></div>

<div class="row">

 <div class="col-xs-9 text-right">

  <input type="submit" name="submit" class="btn btn-success">

</div>
</form>
{{-- </div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

</section>

@endsection --}}

@push('scripts')

<!-- Sweet alert css -->

<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />

<!-- SweetAlert Plugin Js -->

<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<link rel="stylesheet" href="{{URL::asset('assets/css/purchase.css') }}">

<script>

  // getMessage(1);


</script>

<script>

 // function removeAttri(id){
 //  $('#at_remove_'+id).hide();

   // alert('successfully Removed');

//    $.ajax({



//     method: "GET",



//     url:"{{url('inventory_manage/remove_inventory')}}/"+ id,



//     headers: {

//      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

//    },



//    success:function(response){



//     $('#htmlpart').html(response.vw);

//   }

// });





// }

</script>

@endpush
<script type="text/javascript">
  $(document).delegate('.input-image', 'change', function(){
    var input = $(this)[0];
    //console.log(input);
  var data_id = $(this).attr('data-id');
 if (input.files && input.files[0]) {
  var reader = new FileReader();
  reader.onload = function (e) {
    //console.log(e.target.result);
    //$('#' + data_id).attr('src', e.target.result);
    $('#uploadable').attr('src', e.target.result);
  }
  //console.log(input.files[0]);
  reader.readAsDataURL(input.files[0]);
 }
 });
  getMessage(1);
  function getMessage(flag=0) {
    console.log("Ok");
    var taken_id = $('#taken_id').val();
    var option_color = $('#option_color').val();
    var product_id = 0;
    if(flag){
      product_id = {{ $prod->product_id }};
    }
    
    $.ajax({
      method: "POST",
      url:"{{route('edit.inventory.product.option.details')}}",
      data:"product_id="+product_id+"&option_color="+option_color+"&taken_id="+taken_id,
      cache: false,
      headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     success:function(response){
       console.log(response);
      $('#htmlpart').html(response);
    }

  });

  }
  function removeAttri(id,product_id,product_option_id){
    var product_id        = parseInt(product_id) || 0;
    var product_option_id = parseInt(product_option_id) || 0;
    if( product_id < 1 && product_option_id < 1 ){
      $('#at_remove_'+id).hide(); 
      return false;
    }
    if( !confirm("Are you sure you want to completly delete this option.") ){
      return false;
    }
    $.ajax({
      method: "POST",
      url:"{{url('inventory_manage/removeProductOption')}}",
      data:{ product_id : product_id, product_option_id : product_option_id },
      cache: false,
      headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     success:function(response){
       if(response){
        $('#at_remove_'+id).remove(); 
       }
     }
    });
  }
  function showRemainingOption(){
    $('.show_more_optons').show();
  }
</script>