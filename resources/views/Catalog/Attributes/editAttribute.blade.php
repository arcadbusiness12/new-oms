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
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Edit Attributes <button type="button" value="Back" onclick="history.back()" class="btn btn-secondary r-20 float-right"><i class="icon icon-backward"> Back</i> </button> </div>
                            <form action="{{route('update.attribute')}}" method="post" name="form-setting">
                                {{ csrf_field() }}
                                    <div class="form-group p-4">
                                        <div class="row">
                                            <input type="hidden" name="attribute_id" value="{{$attribute->id}}">

                                            <div class="col-lg-6">
                                                <label>Name</label>
                                                    <input type="text" name="name" class="form-control m-input" value="{{ $attribute->name }}" placeholder="Enter Name" autocomplete="off">
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                            </div>
                                            <div class="col-lg-6">
                                            <label>Arabic Name</label>
                                                <input type="text" name="name_ar" class="form-control m-input" value="{{ $attribute->name_ar }}" placeholder="Enter Arabic Name" autocomplete="off">
                                                @error('name_ar')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Category</label>
                                                <select name="category[]" id="category" onchange="loadPresetCategory()" class="custom-select form-control" multiple>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $cate)
                                                        @forelse ($attribute->attributeCategories as $assined_cat )
                                                            @php
                                                                $selected = 0;
                                                                if( $cate->id == $assined_cat->id ){
                                                                    $selected = 1;
                                                                    break;
                                                                }
                                                            @endphp
                                                        @empty
                                                            @php
                                                              $selected = 0;
                                                            @endphp
                                                        @endforelse
                                                    <option value="{{$cate->id}}"
                                                        @selected( $selected == 1 ) >{{$cate->name}} </option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Status</label>
                                                    <select name="status" class="custom-select form-control" >
                                                        <option value="">Select Status</option>
                                                        <option value="1" @selected($attribute->status == 1)>Active</option>
                                                        <option value="0" @selected($attribute->status == 0)>In-Active</option>
                                                    </select>
                                                    @error('status')
                                                       <span class="invalid-feedback" role="alert">
                                                           <strong>{{ $message }}</strong>
                                                       </span>
                                                   @enderror
                                               </div>
                                        </div>
                                        <div class="row" id="newRow">
                                            @php
                                             $preset_key = -1;
                                            @endphp
                                            @foreach($attribute->presets as $preset_key => $preset)
                                            <div class="inserted-row-{{$preset->id}} mt-4">
                                                <div class="row">
                                                    <input type="hidden" name="prestsId[]" value="{{ $preset->id }}" />
                                                    <div class="col-lg-4"> <input type="text" name="prests[]" class="form-control m-input" value="{{$preset->name}}" placeholder="Enter Prest Value" autocomplete="off"></div>
                                                    <div class="col-lg-4"> <input type="text" name="prests_ar[]" class="form-control m-input" value="{{$preset->name_ar}}" placeholder="Enter Arabic Preset Value" autocomplete="off"></div>
                                                    <div class="col-lg-3">
                                                        <select name="preset_category[{{ $preset_key }}][]" class="preset_category preset_category_all" multiple>
                                                            @forelse ($attribute->attributeCategories as $assined_cat )
                                                                @forelse ($preset->categories as $assign_presetCat )
                                                                @php
                                                                    $selected = 0;
                                                                    if( $assined_cat->id == $assign_presetCat->id ){
                                                                        $selected = 1;
                                                                        break;
                                                                    }
                                                                @endphp
                                                                @empty
                                                                @endforelse
                                                                <option value="{{ $assined_cat->id }}" @selected( $selected == 1)>{{ $assined_cat->name }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1"><button onclick="removeOldPreset({{$preset->id}})" id="removeOldRow" type="button" class="btn btn-danger col-md-6"><i class="icon-close"></i></button></div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">

                                                <input type="submit" name="submit" value="S A V E" class="btn btn-success btn-lg float-right">
                                                <button type="button" class="btn btn-primary float-right mr-2" id="add-prest-row" onclick=""><i class="icon icon-plus-circle"></i>Add Prest</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#category').select2();
            $('.preset_category').select2();
        });
        var preset_row_counter = {{ $preset_key+1 }};
        $("#add-prest-row").click(function () {
            var html = '';
            html += '<div class="inserted-row mt-4">';
            html += '<div class="row">';
            html += '<div class="col-lg-4"> <input type="text" name="prests[]" class="form-control m-input" placeholder="Enter Prest Value" autocomplete="off"></div>';
            html += '<div class="col-lg-4"> <input type="text" name="prests_ar[]" dir="rtl" class="form-control m-input" placeholder="Enter Prest Arabic Value" autocomplete="off"></div>';
            html += '<div class="col-lg-3"><select name="preset_category['+preset_row_counter+'][]" class="preset_category'+preset_row_counter+' preset_category_all" multiple></select></div>';
            html += '<div class="col-md-1"><button id="removeRow" type="button" class="btn btn-danger col-md-6" onclick="loadPresetCategory"><i class="icon-close"></i></button></div>';
            // html += '<div class="input-group-append">';
            // html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
            html += '</div>';
            html += '</div>';

            $('#newRow').append(html);
            $('.preset_category'+preset_row_counter).select2();
            loadPresetCategory(preset_row_counter);
            preset_row_counter++;
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).parent().parent().parent().remove();
        });

        function removeOldPreset(preset) {
            console.log(preset);
            if(preset) {
                swal({
                    title: "Are you sure?",
                    text: "You want to delete preset value?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes, I am sure!',
                    cancelButtonText: "No, I don't want!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                    $.ajax({
                        url: "{{route('destory.preset')}}",
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {preset:preset},
                        success: function(resp) {
                            swal.close();
                            if(resp.status) {
                                $('.inserted-row-'+preset).remove();
                                $('.toast-action').data('title', 'Action Done!');
                                $('.toast-action').data('type', 'success');
                                $('.toast-action').data('message', 'Preset deleted successfully.');
                                $('.toast-action').trigger('click');
                            }else {
                                $(".toast-action").data('title', 'Went Wrong!');
                                $(".toast-action").data('type', 'error');
                                $(".toast-action").data('message', 'Something went wrong.');
                                $(".toast-action").trigger('click');
                            }
                        }
                    })
                } else {
                        swal.close();
                        //swal("Cancelled", "Your process is canceled", "error");
                    }
                });
            }
        }
        //
        function loadPresetCategory(counter = ''){
            $.ajax({
                method: "POST",
                url: APP_URL+"/productgroup/get/preset/category",
                data: { category_ids: $('#category').val() },
                headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
            }).done(function(resp) {
                var html = "";
                if( resp.status ){
                    resp.data.forEach(element => {
                        html += "<option value="+element.id+">"+element.name+"</option>"
                    });
                }
                if( counter > -1 ){
                    $('.preset_category'+counter).html(html);
                }else{
                    $('.preset_category_all').html(html);
                }
            });
        }
    </script>
@endpush
