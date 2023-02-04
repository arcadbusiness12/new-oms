@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Add Inventory Product
                          </div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success font-weight-bold">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          
                          @if($errors->has('sku'))
                            <div role="alert" class="error alert alert-danger font-weight-bold">{{ $errors->first('sku') }}</div>
                        @endif
                        <div class="card-header white">
                            <form name="add_inventory_product" id="add_inventory_product" method="post" action="{{route('add.inventory.product')}}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div id="alert-response"></div>
                                <?php $unique = uniqid(); ?>
                                    <div class="product_list_row">
                                        <div class="product_row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-2" style="padding: 0">
                                                    <input type="file" name="image" class="input-image" data-id="" style="position: absolute;height: 100%;width: 100%;opacity: 0;cursor: pointer;" />
                                                    <img id="uploadable" src="<?php echo $placeholder ?>" width="150px" style="float: right;" />
                                                </div>
                                                <div class="col-xs-10 col-sm-10">
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-xs-4 col-sm-3">
                                                                <label>Category</label>
                                                                <select name="category" id="category" class="form-control select-category" >
                                                                    <option value="">Select Category</option>
                                                                    @foreach($categories as $cate)
                                                                        
                                                                        <option value="{{$cate->id}}" data-code="{{$cate->code}}">
                                                                            {{$cate->name}}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                             <div class="col-xs-4 col-sm-3">
                                                                <label>Sub Category</label>
                                                                <select name="subCategory" id="sub-category" class="form-control sub-category">
                                                                    <option value="">Select Sub-category</option>
                                                                    {{-- @foreach($subcategories as $cate)
                                                                        <option value="{{$cate->id}}" data-code="{{$cate->code}}">{{$cate->name}}</option>
                                                                    @endforeach --}}
                                                                </select>
                                                            </div>
                                                            <div class="col-xs-4 col-sm-3">
                                                                <label>Sku Code</label>
                                                                <div class="input-group mb-3">
                                                                <input type="text" id="newgroup-code" class="form-control"style="float: left;
                                                                border-right: none;position: relative;
                                                                max-width: 32%;" readonly/>
                                                                <input type="text" name="group_code" id="search-code" list="code_lists" class="form-control search-group-code" data-uniq="<?= $unique ?>" placeholder="Enter code" style="max-width: 65%;
                                                                border-left: none;
                                                                left: -6px;
                                                                position: relative;
                                                                border-radius: unset;
                                                            }"/>
                                                            <datalist id="code_lists"></datalist>
                                                            </div>
                                                            </div>

                                                        <div class="col-xs-4 col-sm-3">
                                                            <label>Sku</label>
                                                            <input type="hidden" class="new-code" >
                                                            <input type="hidden" name="newSku" class="new-sku" >
                                                            <input type="hidden" class="newCode" >
                                                            <input type="hidden" class="final-selected-code" >
                                                            <input type="text" name="sku" id="sku" class="form-control" placeholder="Enter Product SKU" required readonly/>
                                                        </div>
                                                        
                                                    </div>
                                                  </div>
                                                <div class="form-group">
                                                <div class="row mt-4">
                                                    <div class="col-lg-4">
                                                    <label for="">Colors</label>
                                                    <select name="options" id="option_color" class="form-control" autocomplete="off" required>
                                                    <option value="">Select Color</option>
                                                    @foreach($option_value as $option)
                                                    <option value="{{$option->value}}" data-id="{{$option->code}}">{{$option->value}}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="">Select Size</label>
                                                <div class="input-group mb-3">
                                                    <select name="title" id="taken_id" class="form-control option_name" onchange="getMessage()" required>
                                                        <option value="">Select Size</option>
                                                        <option value="0">-None-</option>
                                                        @foreach($option_detail as $options)
                                                         <option value="{{$options->id}}">{{$options->option_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div id="newRow" style="width: 210%!important;"></div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <label for="">Sku Name</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" name="sku_name" placeholder="Enter Sku Name" required>
                                                </div>
                                                <div id="newRow" style="width: 210%!important;"></div>
                                                </div>
                                                {{-- <div class="col-md-4" style="margin-top:27px;">
                                                <button id="option_name" type="button" class="btn btn-info" onclick="getMessage()">More Options</button>
                                            </div> --}}
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div id="htmlpart"></div>
                                    <div class="manually_option_row"></div>
                                    <div class="row">
                                    <div class="col-xs-9 text-right">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                          </form>
                        </div>
                    </div> {{--  end class card no-b  --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).delegate('.select-category', 'change', function() {
        var code = $(this).find(':selected').data("code");
        var url = '{{ route("cheking.for.group.code", ":category") }}';
        url = url.replace(':category', $(this).val());
        $('#option_color').prop('selectedIndex',0);
        $.ajax({
            url: url,
            type: "GET",
            caches: false,
            success: function(respo) {
                console.log('code='+code);
                console.log(respo);
                var nCode = code;
                $('.newCode').val(respo.code);
                $('#sku').val(nCode);
                $('.new-code').val(nCode);
                $('.new-sku').val(respo.newSku);
                $('#newgroup-code').val(code);
                $('.final-selected-code').val(respo.newSku);

                var html = '';
                var op = '<option value="">Select Sub-category</option>';
                respo.subCategories.forEach(element => {
                    html += '<option value="'+element.id+'" data-code="'+element.code+'">'+element.name+'</option>';
                });
                var options = op+html;
                $('#sub-category').html(options);
            }
        });
    });

    // $(document).delegate('#sub-category', 'change', function() {
    //     var code = $(this).find(':selected').data("code");
    //     code = code ? code : '';
    //     var nCode = $('.new-code').val() +''+ code +''+ $('.newCode').val();
    //     $('#sku').val(nCode);
    //     // $('.new-code').val(nCode);
    //     $('#option_color').prop('selectedIndex',0);
    // });

    $(document).delegate('.sub-category', 'change', function() {
        var code = $(this).find(':selected').data("code");
        code = code ? code : '';
        // var nCode = $('.new-code'+row).val() +''+ code +'-'+ $('.newCode'+row).val();
         $('#newgroup-code').val($('.new-code').val() +''+ code +'-');
        var nCode = $('.new-code').val() +''+ code +'-';
        $('#search-code').focus();
        $('#sku').val(nCode);
        $('.manually_option_color').prop('selectedIndex',0);
    });

    // $(document).delegate('#option_color', 'change', function() {
    //     var iCode = $(this).find(':selected').data('id');
    //     var cateCode = $('.new-code').val();
    //         cateCode = cateCode ? cateCode : '';
    //     var subCatedCode = $('#sub-category').find(':selected').data('code');
    //         subCatedCode =  subCatedCode ? subCatedCode : '';
    //     var nCode = $('.newCode').val();
    //     var code = cateCode +''+ subCatedCode +''+ nCode +''+ iCode;
    //     $('#sku').val(code);
    // })
    $(document).delegate('#option_color', 'change', function() {
        var iCode = $(this).find(':selected').data('id');
        var cateCode = $('.new-code').val();
            cateCode = cateCode ? cateCode : '';
        var subCatedCode = $('#sub-category').find(':selected').data('code');
            subCatedCode =  subCatedCode ? subCatedCode : '';
        // var nCode = $('.newCode'+row).val();
        var nCode = $('.final-selected-code').val();
        var code = cateCode +''+ subCatedCode +'-'+ nCode +'-'+ iCode;
        $('#sku').val(code);
    });

    $(document).delegate('.search-group-code', 'keyup', function() {
        console.log($(this).val());
        console.log($(this).data('uniq'));
        var uniq = $(this).data('uniq');
        var newgroupCode = $('#newgroup-code').val();
        if($(this).val() != "") {
            var sreachCode = newgroupCode+$(this).val();
            $.ajax({
                method: "POST",
                url: "{{route('search.group.code')}}",
                data: {code: sreachCode},
                headers: { 'X-CSRF-Token': $('input[name="_token"]').val() },
                dataType: "html",
                success: function(resp) {
                    html = '';
                    console.log(resp);
                    var groups = JSON.parse(resp);
                    console.log(groups);
                    if (groups.status) {
                        console.log(groups.codes);
                        html += '<option value="' + $('.newCode').val() + '">(Suggestion new Code '+$('.newCode').val()+')</option>';
                        $.each(groups.codes, function(k, v) {
                            console.log(v);
                            html += '<option value="' + v.code + '">('+v.group_code+')</option>';
                        });
                        
                        $('#code_lists').html(html);
                    }
                }
            })
        }
        
    });
    
    $(document).delegate('.search-group-code', 'change', function() {
        console.log($(this).val());
        var uniq = $(this).data('uniq');
        // $('.newCode'+uniq).val($(this).val());
        var cateCode = $('.new-code').val();
            cateCode = cateCode ? cateCode : '';
        var subCatedCode = $('#sub-category').find(':selected').data('code');
            subCatedCode =  subCatedCode ? subCatedCode : '';
        var nCode = $('.newCode').val();
        var color = $('.manually_option_color').find(":selected").data('id');
            color = color ? color : '';
            console.log(color);
        var code = cateCode +''+ subCatedCode +'-'+ $(this).val() +'-' + color;
        $('.new-sku').val(cateCode +''+ subCatedCode +'-'+ $(this).val());
        $('.final-selected-code').val($(this).val());
        console.log(code)
        $('#sku').val(code);
    })

 function getMessage() {
    var taken_id = $('#taken_id').val();
    var url = '{{ route("inventory_manage.add_inventory_prod", ":id") }}';
    url = url.replace(':id', taken_id);
    $.ajax({
    method: "POST",
    url: url,
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success:function(response){
    $('#htmlpart').html(response);
    }
    });
}
function removeAttri(id){
    console.log($('#at_remove_'+id));
  $('#at_remove_'+id).remove();
}
</script>
@endpush