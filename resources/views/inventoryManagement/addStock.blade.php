@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="container-fluid relative animatedParent animateOnce my-3">
  <div class="row row-eq-height my-3 mt-3">
      <div class="col-md-12">
          <div class="row">
              <div class="col-md-12 col-sm-12">
                  <div class="card no-b">
                      <div class="error-messages"></div>
                      <div class="panel-heading">
                          Add Stock
                      </div>
                      <div class="box">
                          <div class="panel panel-default">
                              <div class="panel-body">
                                  <div class="row">
                                          @if(Session::has('message'))
                                            <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                                <?php echo Session::get('message') ?>
                                            </div>
                                          @endif
                                          <form action="{{route('inventory.add.stock')}}">
                                            {{ csrf_field() }}
                                            <div class="row  mt-2 ml-1">
                                              <div class="col-2">
                                                <div class="form-group">
                                                    <input type="text" id="product_sku" list="product_skus" value="{{ @$old_input['a'] }}" name="a" class="form-control" placeholder="Enter SKU" autocomplete="off">
                                                    <datalist id="product_skus"></datalist>
                                                </div>
                                              </div>
                                              <div class="col-2">
                                                <input type="submit" class="btn btn-sm btn-primary" value="Search">
                                              </div>
                                            </div>
                                          </form>
                                  </div>
                              </div>
                          </div>
                      </div>
                      @if ($stocks->count() > 0)
                      <div class="box">
                          <div class="panel panel-default">
                              <div class="panel-body">
                                  <form method="post" action="{{route('inventory.add.stock',$stocks[0]->product_id)}}">
                                    {{ csrf_field() }}
                                      <div class="col-12">
                                          <table class="table table-hover table-borderd" >
                                              <thead style="background-color: #eee">
                                                  <th class="text-center"><b>{{$stocks[0]->sku}}</b></th>
                                                  <th class="text-center">{{ $stocks[0]->option_name }}</th>
                                                  <th class="text-center">Available Quantity</th>
                                                  <th class="text-center">Onhold Quantity</th>
                                                  <th class="text-center">Quantity +/-</th>


                                              </thead>
                                              <tbody>
                                                  <tr>
                                                      <td class="col-4" rowspan="{{ (count($stocks)+1) }}"><img src="{{URL::asset('uploads/inventory_products/'.$stocks[0]->image)}}" class="img-responsive img-thumbnail" /></td>
                                                  </tr>
                                                  @foreach($stocks as $stock)

                                                  <tr>
                                                      {{-- <td class="text-center">{{$stock->sku}}</td> --}}
                                                      <td class="text-center">{{$stock->value}}</td>
                                                      <td class="text-center">{{$stock->available_quantity}}</td>
                                                      <td class="text-center">{{$stock->onhold_quantity}}</td>

                                                      <input type="hidden" name="option_id[]" value="{{ $stock->option_id }}">
                                                      <input type="hidden" name="option_value_id[]" value="{{ $stock->option_value_id }}">
                                                      <input type="hidden" name="option_value[]" value="{{ $stock->value }}">
                                                      <td class="text-center"><input type="text" name="option_quantity[]" class="form-controller" size="4"></td>
                                                      {{csrf_field()}}
                                                  </tr>
                                                  @endforeach
                                                  <tr>
                                                      <td class="text-center" colspan="4"><textarea name="option_reason" placeholder="Enter reason" cols="130" rows="2" required></textarea></td>
                                                      <td class="text-right"> 
                                                        <input type="submit" name="submit" value="Update" class="btn btn-success btn-md">

                                                      </td>
                                                  </tr>
                                              </tbody>
                                          </table>
                                      {{-- </div> 
                                        <div class="col-sm-1"> --}}
                                          @php
                                          $url = url('/inventory_manage/print_label/'.$stocks[0]->product_id);
                                          @endphp
                                          <!-- <div class="row">
                                              <a class="btn btn-warning btn-md" onclick="window.open('<?php echo $url ?>?type=big', '_blank')">Big Label</a>
                                          </div>
                                          <div class="row">
                                              <a class="btn btn-primary btn-md" onclick="window.open('<?php echo $url ?>?type=small', '_blank')">Small Label</a>
                                          </div> -->
                                          {{-- <div class="row" style="margin-top: 38px">
                                              @php
                                              if($stocks[0]->print_label=="small"){
                                                  $small_selected = "checked";
                                                  $big_selected = "";
                                              }elseif($stocks[0]->print_label=="big"){
                                                  $small_selected = "";
                                                  $big_selected = "checked";
                                              }
                                              @endphp

                                              <input type="radio" name="print_label" id="big" value="big" oninvalid="$('.print_msg').text('Please Select any one label');" {{ @$big_selected }}>
                                              <label for="big">Big Label</label>
                                          </div>
                                          <div class="row">
                                              <input type="radio" name="print_label" id="small" value="small" oninvalid="$('.print_msg').text('Please Select any one label');" {{ @$small_selected }}>
                                              <label for="small">Small Label</label>
                                          </div> --}}
                                          <div class="row">
                                              <input type="hidden" name="sku" value="{{ $stocks[0]->sku }}">
                                              <input type="hidden" name="color" value="{{ $stocks[0]->option_name }}">
                                              {{-- <input type="submit" name="submit" value="Update" class="btn btn-success btn-md pull-right"> --}}
                                          </div>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                      @endif
                      @if($user_update->count() > 0)
                      <div class="box">
                        <div class="panel panel-default">
                          <div class="panel-body">
                        <table class="table table-borderd pull-right"  style="width:50%">
                            <thead>
                                <th>History</th>
                                <th>Reason</th>
                                <th>Update time</th>
                            </thead>
                            <tbody>
                                @foreach($user_update as $usup)
                                <tr>
                                    <td>{{ $usup->comment }}</td>
                                    <td>{{ $usup->reason }}</td>
                                    <td>{{ $usup->updated_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                          </div>
                        </div>
                      </div>
                      @endif
                    </div> {{--  end class card no-b  --}}
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection
@push('scripts')
<script>
     var xhr = {};
    $(document).delegate('#product_sku','keyup',function(){
        // _this = $(this);
        if(typeof xhr['get_product_sku_keyup'] != 'undefined' && xhr['get_product_sku_keyup'].readyState != 4){
            xhr['get_product_sku_keyup'].abort();
        }
        xhr['get_product_sku_keyup'] = $.ajax({
            method: "POST",
            url: "{{route('inventory.get.product.sku')}}",
            data: {
                product_sku : $(this).val()
            },
            headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
        }).done(function (data){
            html = '';
            if(data.skus){
                $.each(data.skus, function(k,v){
                    html +='<option value="'+v+'">';
                });
                $('#product_skus').html(html);
            }
        });
    });
</script>

@endpush