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
                        <div class="panel-heading">Add Attributes</div>
                            <form action="{{route('save.attribute')}}" method="post" name="form-setting">
                                {{ csrf_field() }}
                                    <div class="form-group p-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label>Name</label>
                                                   <input type="text" name="name" class="form-control m-input" value="{{ old('name') }}" placeholder="Enter Name" autocomplete="off">
                                                   @error('name')
                                                       <span class="invalid-feedback" role="alert">
                                                           <strong>{{ $message }}</strong>
                                                       </span>
                                                   @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Arabic Name</label>
                                                   <input type="text" name="name_ar" class="form-control m-input" value="{{ old('name_ar') }}" placeholder="Enter Arabic Name" dir="rtl" autocomplete="off">
                                                   @error('name_ar')
                                                       <span class="invalid-feedback" role="alert">
                                                           <strong>{{ $message }}</strong>
                                                       </span>
                                                   @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Category</label>
                                                <select name="category[]" id="category" class="custom-select form-control" onchange="loadPresetCategory()" multiple>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $cate)
                                                     <option value="{{$cate->id}}">{{$cate->name}}</option>
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
                                                        <option value="1">Active</option>
                                                        <option value="0">In-Active</option>
                                                    </select>
                                                    @error('status')
                                                       <span class="invalid-feedback" role="alert">
                                                           <strong>{{ $message }}</strong>
                                                       </span>
                                                   @enderror
                                               </div>
                                        </div>
                                        <div class="row" id="newRow">

                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">

                                                <input type="submit" name="submit" value="S A V E" class="btn btn-success btn-lg float-right">
                                                <button type="button" class="btn btn-primary float-right mr-2" id="add-prest-row" onclick="loadPresetCategory()"><i class="icon icon-plus-circle"></i>Add Prest</button>
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

        }); //end document.ready
        var preset_row_counter = 0;
        $("#add-prest-row").click(function () {
            var html = '';
            html += '<div class="inserted-row mt-4">';
            html += '<div class="row">';
            html += '<div class="col-lg-4"> <input type="text" name="prests[]" class="form-control m-input" placeholder="Enter Prest Value" autocomplete="off"></div>';
            html += '<div class="col-lg-4"> <input type="text" name="prests_ar[]" dir="rtl" class="form-control m-input" placeholder="Enter Prest Value" autocomplete="off"></div>';
            html += '<div class="col-lg-3"><select name="preset_category['+preset_row_counter+'][]" class="preset_category'+preset_row_counter+' preset_category_all" multiple></select></div>';
            html += '<div class="col-md-1"><button id="removeRow" type="button" class="btn btn-danger col-md-6"><i class="icon-close"></i></button></div>';
            // html += '<div class="input-group-append">';
            // html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
            html += '</div>';
            html += '</div>';
            $('#newRow').append(html);
            $('.preset_category'+preset_row_counter).select2();
            preset_row_counter++;
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).parent().parent().parent().remove();
            preset_row_counter--;
        });
        //
        function loadPresetCategory(){
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
                $('.preset_category_all').html(html);
            });
        }
    </script>
@endpush
