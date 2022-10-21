@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                    
                    <div class="card no-b form-box">
                        @if(session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif

                        <div class="card-header white">
                            
                    </div>
                </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Product Listing
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">
                    
                             <thead >
                    
                              <tr 
                              style="background-color: #3f51b5;color:white"
                              >
                                <th scope="col"><center>image</center></th>
                                <th scope="col"><center>Product Name</center></th>
                                <th scope="col"><center>Group</center></th>
                                <th scope="col"><center>Sku</center></th>
                                <th scope="col"><center>Price</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Action</center></th>
                    
                               </tr>
                    
                             </thead>
                             @if(count($productLists) > 0)
                                @foreach($productLists as $list)
    
                                <tr>
                                
                                    <td class="text-center">
                                        <img src="{{URL::asset('uploads/inventory_products/'.$list->image)}}" class="img-responsive img-thumbnail" />    
                                    </td>
                                        
                                        <td class="text-center">
                                            {{(count($list->productDescriptions) > 0) ? $list->productDescriptions[0]->name : ''}}
                                        </td>
                                    <td class="text-center">
                                       {{$list->productGroups->name}}
                                    </td>
                                    <td class="text-center">
                                       {{$list->sku}}
                                    </td>
                                    <td class="text-center">
                                        {{(count($list->productDescriptions) > 0) ? $list->productDescriptions[0]->price : ''}}
                                     </td>
                                     <td class="text-center">
                                        @if($list->status == 1) 
                                            <span class="badge badge-success font-weight-bold">Active</span>
                                        @else 
                                            <span class="badge badge-danger font-weight-bold">In-Active</span>
                                        @endif
                                     </td>
                                     
                                     <td class="text-center">
                                       <a href="{{route('edit.product.listing', $list->product_id)}}"><i class="icon icon-edit"></i> </a>
                                     </td>
                                </tr>
                                @endforeach
                                @else 
                                <tr>
                                    <td>
                                        No Product Available
                                    </td>
                                </tr>
                                @endif
                             <tbody>
                     
                    </table>
                    
                    </div>

            </div>

            
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>

 <!-- product location modal start -->
 <div class="modal fade porduct_location_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Add & Edit options</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="porduct_location_content">
          <div class="text-center" id="loader">
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- product location modal end -->
@endsection

@push('scripts')
<script>

    $("#addRow").click(function () {
            var html = '';
            html += '<div class="inserted-row mt-4">';
            html += '<div class="row">';
            html += '<div class="col-lg-4"> <input type="text" name="value[]" class="form-control m-input" placeholder="Enter Option Details" autocomplete="off"></div>';
            html += '<div class="col-md-1"><button id="removeRow" type="button" class="btn btn-danger col-md-6"><i class="icon-close"></i></button></div>';
            // html += '<div class="input-group-append">';
            // html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
            html += '</div>';
            html += '</div>';

            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).parent().parent().parent().remove();
        });

        function viewAndEdit(id) {
            console.log(id);
            if(id) {
                var url = "{{route('edit.option.details', ':id')}}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    beforeSend: function() {
                        $('#porduct_location_content').html('<div class="text-center"><div class="preloader-wrapper small active"><div class="spinner-layer spinner-green-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div></div>');
                    },
                    complete: function() {
                        // $('#porduct_location_content').html('');
                    }
                }).done(function(response) {
                    $('#porduct_location_content').html(response);
                });
            }
        }

        $(document).on('click',"#addNewRow",function () {
            var option = $('#option_name_id').val();
            var attr = '';
            if(option != 19) {
                attr = 'disabled';
            }
             console.log(option);
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" name="title[]" id="myInput" class="form-control m-input" placeholder="" autocomplete="off"></td><td><input type="text" name="code[]" value="" autocomplete="off" class="form-control" size="6px" '+attr+'></td><td><button id="removeNewRow" type="button" class="btn btn-danger "><i class="icon-close"></i></button></td>';
            
            html += '</tr>';
            $('.rowNew').append(html);
            });
        // remove row
        $(document).on('click', '#removeNewRow', function () {
            $(this).parent().parent().remove();
        });

       function checkName(v) {
           console.log(v);
           if(v) {
               $('#btn-update').attr('disabled', false);
           }
           else {
            $('#btn-update').attr('disabled', true);
           }
       }

       function destroyOptionValue(id) {
           console.log(id);
           if(id && confirm('Are You Sure Want To Delete ?')) {
               var url = "{{route('destroy.option.value', ':id')}}";
               url = url.replace(':id', id);
               $.ajax({
                   url: url,
                   type: 'GET',
                   success: function(response) {
                        if(response.status) {
                            $('#value-row'+id).remove();
                            $(".toast-action").data('title', 'Action Done!');
                            $(".toast-action").data('type', 'success');
                            $(".toast-action").data('message', 'Value deleted successfully.');
                            $(".toast-action").trigger('click');
                        } else {
                            $(".toast-action").data('title', 'Went Wrong!');
                            $(".toast-action").data('type', 'error');
                            $(".toast-action").data('message', 'Something went wrong.');
                            $(".toast-action").trigger('click');
                        }
                   }
               });
           }
       }
</script>
@endpush