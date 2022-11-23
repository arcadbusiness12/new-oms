@extends('layouts.app')

@section('content')
<style>
.form-control {
    border: 1px solid #c1c6cb;
}
.badge {
    font-weight: 900;
}


</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12 text-right">
                        <div class="card-header white">

                                        <div class=" ">
                                        <a href="{{route('add.shipping.method')}}"> <button id="" type="button" class="btn btn-primary active add-method">
                                            <i class="icon-plus-circle"></i>  New
                                        </button>
                                        </a>
                                        </div>

                        </div>
                </div>
                </div>
            </div>
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            <div class="col-md-6 col-sm-6 col-grid">
                                Shipping Methods
                            </div>
                            <div class="col-md-6 col-sm-6 col-grid text-right">
                                <span class="font-weight-bold">Free Shipping On <span style="color: white;
                                    background-color: darkgreen;
                                    padding: 2px;">{{isset($freeShippingAmount) ? $freeShippingAmount->value : '--'}} <a href="javascript:;" class="" onclick="freeShippingAmount('{{@$freeShippingAmount->setting_id}}', '{{@$freeShippingAmount->value}}')"><i class="icon icon-edit" style="color: white;"></i></a></span> </span>
                                    
                            </div>
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">

                             <thead >

                              <tr
                              style="background-color: #3f51b5;color:white"
                              >
                                <th scope="col"><center>Name</center></th>
                                <th scope="col"><center>Store</center></th>
                                <th scope="col"><center>Geo Zone</center></th>
                                <th scope="col"><center>Amount</center></th>
                                {{-- <th scope="col"><center>Status</center></th> --}}
                                <th scope="col"><center>Action</center></th>

                               </tr>

                             </thead>
                             @if(count($methods) > 0)
                                @foreach($methods as $method)

                                    <tr>

                                        <td class="text-center">{{$method->name}}</td>
                                        <td class="text-center">{{@$method->store->name}}</td>
                                        <td class="text-center">{{($method->geo_zone_id == 0) ? 'All Zone' : @$method->geoZone->name}}</td>
                                        <td class="text-center">{{$method->amount}}</td>
                                        {{-- <td class="text-center">
                                            @if($method->status == 1)
                                            <span class="badge badge-success r-5">Active</span>
                                            @else
                                            <span class="badge badge-danger r-5">In-Active</span>
                                            @endif
                                        </td> --}}
                                        <td class="text-center">
                                            <a href="{{route('edit.shipping.method', $method->id)}}" class="" onclick="editMothod({{$method}})"><i class="icon icon-edit"></i></a>
                                            {{--  <a href="{{route('destroy.option',$list->id)}}"  onclick="return confirm('Are You Sure Want To Delete ?')" class=""><i class="icon-close2 text-danger-o text-danger"></i></a>  --}}

                                        </td>
                                    </tr>
                                @endforeach
                                @else 
                                <tr>
                                    <td colspan="4" class="text-danger text-center">
                                        No Payment Method Available.
                                    </td>
                                </tr>
                              @endif
                             <tbody>


                     </tbody>

                    </table>

                    </div>

            </div>


                    </div>
                </div>

        </div>
    </div>
</div>

  <!-- Modal -->
<div class="modal fade" id="freeShippingModal" tabindex="-1" data-backdrop="static" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color: aliceblue;">
          <h5 class="modal-title" id="ModalLabel">Add Free Shipping Amount</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{route('free.shipping.setting.form')}}" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
                {{-- <div class="col-1 col-grid"> --}}
                  <input type="hidden" name="freeShipping_id" id="free-shipping-id" class="form-control">
                    <label class="text-black">Free Shipping On</label>
                    <input type="number" name="free_shipping_amount" id="free_shipping_amount" class="form-control">
                {{-- </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add</button>
              </div>
          </form>
        </div>
      
      </div>
    </div>
  </div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
<script>
    $('.add-method').on('click', function () {
        $('#ModalLabel').text('Add Shipping Method');
        $('#paymentMethodModal').modal('show');
    });

    function editMothod(object) {
        if(object) {
            $('#name').val(object.name);
            $('#amount').val(object.amount);
            $('#shipping_method_id').val(object.id);
            $.ajax({
                url: "{{route('get.countries')}}",
                type: 'GET',
                success: function(resp) {
                    var html = '';
                    resp.countries.forEach(function(v,k) {
                        var selected = (object.country_id == v.id) ? 'selected' : '';
                        html += '<option value="'+v.id+'" '+selected+'>'+v.name+'</option>';
                    });
                    $('#select-country').html(html);
                }
            })
           
            $('#ModalLabel').text('Edit Shipping Method');
            $('#paymentMethodModal').modal('show');
        }
    }
    
    function freeShippingAmount(id = null, value = null) {
        // console.log(object);
        if(id) {
            $('#free-shipping-id').val(id);
            $('#free_shipping_amount').val(value);
        }
        $('#freeShippingModal').modal('show');
    }
    
    $('.btn-close, .close-btn').on('click', function() {
        $('#paymentMethodModal').find('form').trigger('reset');
        $('#shipping_method_id').val('');
    });
</script>
@endpush
