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
                        @if(count($errors) > 0)
                        <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            
                                <li>{{ str_replace('.2', ' ', $error) }}</li>
                            
                        @endforeach
                    </div>
                    @endif
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Assign Attributes
                           <a href="{{route('product.listing')}}"> <button type="button" value="Back" class="btn btn-secondary r-20 float-right"><i class="icon icon-backward"> Back</i> </button></a> 
                        </div>
                        <div class="col-lg-12 text-center" style="background-color: #3f51b5;color:white;font-weight:bold;
                            padding: 5px;
                        ">{{$group->name}}
                        </div>
                            <form action="{{route('save.assign.attributes')}}" method="post" name="form-setting">
                                {{ csrf_field() }}
                                <input type="hidden" name="group" value="{{$group->id}}">
                                @foreach ($group->attributes as $k => $attribte)
                                <div class="form-group p-4" style="border-bottom: 2px solid beige;">
                                    <div class="row">
                                        <input type="hidden" name="old_id[]" value="{{$attribte->pivot->id}}">
                                        <div class="col-lg-6" style="border-right:5px solid beige;">
                                            <label class="text-black"><strong> Attributes </strong></label>
                                            <select name="attributes[]" class="custom-select form-control" onchange="fetchPresets(this.value, {{$attribte->id}})">
                                                <option value="">Select Attribute</option>
                                                @foreach($attributes as $attribute)
                                                <option value="{{$attribute->id}}" @selected($attribute->id == $attribte->id)>{{$attribute->name}}</option>
                                                @endforeach
                                            </select>

                                            <label class="text-black"><strong> Presets </strong></label>
                                            <select name="presets[]" class="custom-select form-control preset-action" data-index="{{$attribte->id}}{{$k}}" id="preset-value-{{$attribte->id}}">
                                                <option value="">Select Preset</option>
                                                @foreach($attributes as $attribute)
                                                 @foreach($attribute->presets as $preset)
                                                    <option value="{{$preset->id}}" @selected($preset->id == $attribte->pivot->attribute_preset_id)>{{$preset->name}}</option>
                                                @endforeach
                                                @endforeach
                                            </select>
                                            @error('presets')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-5" style="border-right:5px solid beige;">
                                         <label class="text-black"><strong> Text </strong></label>
                                            {{-- <input type="text" name="name" class="form-control m-input" value="{{ $attribte->pivot->text }}" placeholder="Enter Name" autocomplete="off"> --}}
                                            <textarea class="form-control" name="preset_text[]" rows="4" id="preset-text-{{$attribte->id}}{{$k}}">{{ $attribte->pivot->text }}</textarea>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-1"><button id="" type="button" class="btn btn-danger btn-remove-old-preset" data-id="{{$attribte->pivot->id}}"><i class="icon-close"></i></button></div>
                                    </div>
                                   
                                </div>
                                @endforeach
                                    <div class="row" id="newRow">
                                        
                                    </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        
                                        <input type="submit" name="submit" value="S A V E" class="btn btn-success btn-lg float-right">
                                        <button type="button" class="btn btn-primary float-right mr-2" id="add-prest-row"><i class="icon icon-plus-circle"></i>Add Attributes</button>
                                    </div>
                                </div>
                            </form>
                        </div>

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
     $(document).ready(function () {
         var x = 0;
        $("#add-prest-row").click(function () {
            var html = '';
            html += '<div class="form-group p-4" style="border-bottom: 2px solid beige;">';
            html += '<div class="row"><input type="hidden" name="old_id[]" value="">';
            html += '<div class="col-lg-6" style="border-right:5px solid beige;"> <label class="text-black"><strong> Attributes </strong></label>';
            html += '<select name="attributes[]" class="custom-select form-control" onchange="fetchPresets(this.value, '+x+')">';
            html +=  '<option value="">Select Attribute</option>';
            html +=   '@foreach($attributes as $attribute)';
            html +=    '<option value="{{$attribute->id}}">{{$attribute->name}}</option>';
            html +=    '@endforeach';
            html +=    '</select>';
            html +=  '<lable class="text-black"><strong> Preset </strong></label>';
            html += '<select name="presets[]" class="custom-select form-control preset-action" onchange="selectPreset(this.value, '+x+')" data-index="'+x+'" id="preset-value-'+x+'">';
            html +=  '<option value="">Select Preset</option>';
            html +=    '</select>';
            html += '</div>';
            html += '<div class="col-lg-5" style="border-right:5px solid beige;">';
            html += ' <label class="text-black"><strong> Text </strong></label>';
            html += '<textarea class="form-control" name="preset_text[]" rows="4" id="preset-text-'+x+'"></textarea>';
            html += '</div>';
            html += '<div class="col-md-1"><button id="removeRow" type="button" class="btn btn-danger "><i class="icon-close"></i></button></div>';
            // html += '<div class="input-group-append">';
            // html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
            html += '</div>';
            html += '</div>';

            $('#newRow').append(html);
            x++;
        });
     });
        

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).parent().parent().parent().remove();
        });

        function fetchPresets(value, index) {
            console.log(value);
            var url = "{{route('fetch.preset.values', ":id")}}";
            url = url.replace(":id", value);
            if(value) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    catche: false,
                    success: function(resp) {
                        var html = '';
                        html += '<option value="">Select Preset</option>';
                        resp.presets.forEach(function(v,k) {
                            console.log(v);
                            html += '<option value="'+v.id+'">'+v.name+'</option>';
                        });
                        $('#preset-value-'+index).html(html);  
                    }
                })
            } 
        }

        $('.preset-action').on('change', function(e) {
            console.log("Yesssssssss");
            let rowIndex = $(this).data('index');
            var text = $(this).find('option:selected').text();
            console.log('preset-text-'+rowIndex);
            $('#preset-text-'+rowIndex).text(text);
        });

        function selectPreset(value, row) {
            console.log(value);
            var text = $('#preset-value-'+row+ ' option:selected').text();
            // var text = $(this).find('option:selected').text();
            console.log('preset-text-'+row);
            $('#preset-text-'+row).text(text);
        }

        $('.btn-remove-old-preset').on('click', function() {
            var id = $(this).data('id');
            if(id) {
                swal({
                    title: "Are you sure?",
                    text: "You want to delete attribute value?",
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
                            url: "{{route('attribute.destory')}}",
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {id:id},
                            success: function(resp) {
                                console.log(resp);
                                swal.close();
                                if(resp.status) {
                                    $(this).parent().parent().parent().remove();
                                    $('.toast-action').data('title', 'Action Done!');
                                    $('.toast-action').data('type', 'success');
                                    $('.toast-action').data('message', 'Attribute deleted successfully.');
                                    $('.toast-action').trigger('click');
                                }else {
                                    $(".toast-action").data('title', 'Went Wrong!');
                                    $(".toast-action").data('type', 'error');
                                    $(".toast-action").data('message', 'Something went wrong.');
                                    $(".toast-action").trigger('click');
                                }
                            }
                      }); 
                    }else {
                        swal.close();
                        //swal("Cancelled", "Your process is canceled", "error");
                    }
                }
                )
            }
        });
    </script>
@endpush