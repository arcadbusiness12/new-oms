@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Option Connection
                          </div>

                          <form action="" method="post" name="form-setting" id="myForm">
                            {{ csrf_field() }}
                            <table class="table thead-dark" style="border: 1px solid #3f51b5">
                                <thead>
                                    <tr class="text-center" style="background-color: #3f51b5;color:white">
                                        <th><strong> OMS </strong></th>
                                        <th><strong> Business Arcade </strong></th>
                                        <th><strong> Dressfair </strong></th>
                                    </tr>
                                   
                                </thead>
                                <tbody>
                                    <tr>
                                        {{-- oms start --}}
                                        <td class="col-md-2">
                                            <center>
                                                <ul class="list-unstyled">
                                                    @forelse($oms_options as $key=>$omsoption)
                                                    <li><input type="text" readonly="readonly" value="{{ $omsoption->option_name }}" class="form-control">
                                                        <input type="hidden" value="{{ $omsoption->id }}" id="oms_optionid_{{ $key }}" class="form-control">
                                                        <ul class="list-unstyled" style="margin-left: 3px;">
                                                            @forelse($omsoption->omsOptionsDetails as $key1=>$omsOptionsDetail)
                                                            <li class="col-sm-12" style="margin-left: 32px;"><input type="text" readonly="readonly" value="{{ $omsOptionsDetail->value }}" class="form-control"></li>
                                                            @empty
                                                            @endforelse
                                                        </ul>
                                                    </li>
                                                    @empty
                                                    @endforelse
                                                </ul>
                                            </center>
                                        </td>
                                        {{-- oms end --}}
                                        <td class="col-md-4">
                                            <ul class="list-unstyled">
                                                @php
                                                $ba_option_ids = [];
                                                @endphp
                                                @foreach($oms_options as $oms_op_key=>$oms_op_val)
                                                <li >
                                                    <select class="form-control" name="baoptions[]" onchange="loadBAoptionDetails(this,{{ $oms_op_key }},'ba')">
                                                        <option value="0">OFF</option>
                                                        @forelse($baOption as $keys=>$vals)
                                                        @php
                                                        $OmsInventoryOptiondata = App\Models\Oms\InventoryManagement\OmsInventoryOptionModel::where(['ba_option_id'=>$vals->option_id])->first();
                                                        if( !empty($OmsInventoryOptiondata) && $vals->option_id==$OmsInventoryOptiondata->ba_option_id && $oms_op_val->id==$OmsInventoryOptiondata->oms_options_id ){
                                                            $option_selected = "selected";
                                                            $ba_option_ids[]=$vals->option_id;
                                                        }else{
                                                            $option_selected = "";
                                                        }
                                                        @endphp
                                                        <option value="{{ $vals->option_id }}" {{ $option_selected }}>{{ $vals->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </li>
                                                <li id="badetails_container_{{ $oms_op_key }}" style="margin-left:32px;">
                                                    {{-- option details start --}}
                                                    @php
                                                    if(array_key_exists($oms_op_key, $ba_option_ids)){ 
                                                        $OptionValueDescriptionModeldata = App\Models\OpenCart\Products\OptionValueDescriptionModel::where(['option_id'=>$ba_option_ids[$oms_op_key]])->where("language_id",1)->orderBy('name')->get();
                                                    }else{
                                                        $OptionValueDescriptionModeldata = "";
                                                    }
                                                    @endphp
                                                    <ul>
                                                        @foreach($oms_op_val->omsOptionsDetails as $omdk=>$omdv)
                                                        <select class="form-control" name="baoptionsdetails[{{ array_key_exists($oms_op_key, $ba_option_ids) ? $ba_option_ids[$oms_op_key] : ''}}][]">
                                                            <option value="0">OFF</option>
                                                            @if(!empty($OptionValueDescriptionModeldata))
                                                            @foreach($OptionValueDescriptionModeldata as $ovdk=>$ovdv)
                                                            @php
                                                            $where_arr = ['ba_option_id'=>$ba_option_ids[$oms_op_key],"ba_option_value_id"=>$ovdv->option_value_id,"oms_option_details_id"=>$omdv->id];
                                                            $OmsInventoryOptionValueModeldata = App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel::where($where_arr)->first();
                                                            if(!empty($OmsInventoryOptionValueModeldata) && $ovdv->option_value_id==$OmsInventoryOptionValueModeldata->ba_option_value_id){
                                                                $value_selected = "selected";
                                                            }else{
                                                                $value_selected = "";
                                                            }
                                                            @endphp
                                                            <option value="{{ $ovdv->option_value_id }}" {{ $value_selected }}>{{ $ovdv->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        @endforeach
                                                    </ul>
                                                    {{-- option details end--}}
                                                </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        {{-- BA end --}}
                                        
                                        {{-- DF Start --}}
                                        <td class="col-md-4">
                                            <ul class="list-unstyled">
                                                @php
                                                $df_option_ids = [];
                                                @endphp
                                                @foreach($oms_options as $oms_op_key=>$oms_op_val)
                                                <li>
                                                    <select class="form-control" name="dfoptions[]" onchange="loadBAoptionDetails(this,{{ $oms_op_key }},'df')">
                                                        <option value="0">OFF</option>
                                                        @forelse($dfOption as $keys=>$vals)
                                                        @php
                                                        $OmsInventoryOptiondata = App\Models\Oms\InventoryManagement\OmsInventoryOptionModel::where(['df_option_id'=>$vals->option_id])->first();
                                                        if( !empty($OmsInventoryOptiondata) && $vals->option_id==$OmsInventoryOptiondata->df_option_id && $oms_op_val->id==$OmsInventoryOptiondata->oms_options_id ){
                                                            $option_selected = "selected";
                                                            $df_option_ids[]=$vals->option_id;
                                                        }else{
                                                            $option_selected = "";
                                                        }
                                                        @endphp
                                                        <option value="{{ $vals->option_id }}" {{ $option_selected }}>{{ $vals->name }}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </li>
                                                <li id="dfdetails_container_{{ $oms_op_key }}" style="margin-left:32px;">
                                                    {{-- option details start --}}
                                                    @php
                                                    if(array_key_exists($oms_op_key, $df_option_ids)){
                                                        $OptionValueDescriptionModeldata = App\Models\DressFairOpenCart\Products\OptionValueDescriptionModel::where(['option_id'=>$df_option_ids[$oms_op_key]])->where("language_id",1)->orderBy('name')->get();
                                                    }else{
                                                        $OptionValueDescriptionModeldata = "";
                                                    }
                    
                                                    @endphp
                                                    <ul>
                                                        @foreach($oms_op_val->omsOptionsDetails as $omdk=>$omdv)
                                                        <select class="form-control" name="dfoptionsdetails[{{ array_key_exists($oms_op_key, $df_option_ids) ? $df_option_ids[$oms_op_key] : ''}}][]">
                                                            <option value="0">OFF</option>
                                                            @if(!empty($OptionValueDescriptionModeldata))
                                                            @foreach($OptionValueDescriptionModeldata as $ovdk=>$ovdv)
                                                            @php
                                                            $where_arr = ['df_option_id'=>$df_option_ids[$oms_op_key],"df_option_value_id"=>$ovdv->option_value_id,"oms_option_details_id"=>$omdv->id];
                                                            $OmsInventoryOptionValueModeldata = App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel::where($where_arr)->first();
                                                            if(!empty($OmsInventoryOptionValueModeldata) && $ovdv->option_value_id==$OmsInventoryOptionValueModeldata->df_option_value_id){
                                                                $value_selected = "selected";
                                                            }else{
                                                                $value_selected = "";
                                                            }
                                                            @endphp
                                                            <option value="{{ $ovdv->option_value_id }}" {{ $value_selected }}>{{ $ovdv->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        @endforeach
                                                    </ul>
                                                    {{-- option details end--}}
                                                </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        {{-- DF end --}}
                                    </tr>
                                    <tr>
                                        
                                        <td colspan="4" class="text-right"><input type="submit" name="btn-connect-options" value="Connect" class="btn btn-success btn-lg pull-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
            
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
             console.log("Ok");
            var html = '';
            html += '<tr>';
            html += '<td><input type="text" name="title[]" id="myInput" class="form-control m-input" placeholder="" autocomplete="off"></td><td><button id="removeNewRow" type="button" class="btn btn-danger "><i class="icon-close"></i></button></td>';
            
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