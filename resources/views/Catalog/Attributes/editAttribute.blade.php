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
                                                <label>Category</label>
                                                <select name="category" class="custom-select form-control">
                                                    <option value="">Select Category {{$attribute->category_id}}</option>
                                                    @foreach($categories as $cate)
                                                    <option value="{{$cate->id}}" 
                                                        @selected($cate->id == $attribute->category_id) >{{$cate->name}} </option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                             <label>Name</label>
                                                <input type="text" name="name" class="form-control m-input" value="{{ $attribute->name }}" placeholder="Enter Name" autocomplete="off">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row" id="newRow">
                                            @foreach($attribute->presets as $preset)
                                            <div class="inserted-row-{{$preset->id}} mt-4">
                                                <div class="row">
                                                    <div class="col-lg-6"> <input type="text" name="prests_old[{{$preset->id}}]" class="form-control m-input" value="{{$preset->name}}" placeholder="Enter Prest Value" autocomplete="off"></div>
                                                    <div class="col-md-2"><button onclick="removeOldPreset({{$preset->id}})" id="removeOldRow" type="button" class="btn btn-danger col-md-6"><i class="icon-close"></i></button></div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                
                                                <input type="submit" name="submit" value="S A V E" class="btn btn-success btn-lg float-right">
                                                <button type="button" class="btn btn-primary float-right mr-2" id="add-prest-row"><i class="icon icon-plus-circle"></i>Add Prest</button>
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
        $("#add-prest-row").click(function () {
            var html = '';
            html += '<div class="inserted-row mt-4">';
            html += '<div class="row">';
            html += '<div class="col-lg-6"> <input type="text" name="prests[]" class="form-control m-input" placeholder="Enter Prest Value" autocomplete="off"></div>';
            html += '<div class="col-md-2"><button id="removeRow" type="button" class="btn btn-danger col-md-6"><i class="icon-close"></i></button></div>';
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
    </script>
@endpush