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
                            <form action="" method="post" name="form-setting">
                                {{ csrf_field() }}
                                     <div class="form-group">

                                            <div class="col-lg-8">
                                                <div class="row">
                                                    <div class="inserted-row mt-4">
                                                        <div class="col-lg-4">
                                                            <input type="text" name="name" class="form-control m-input" placeholder="Search By Name" autocomplete="off">
                                                        </div>

                                                    </div>
                                                </div>
                                             <div class="row" id="newRow" style="width: 210%!important;margin-left:1px;">
                                            </div>
                                            </div>
                                            <div class="col-md-4" style="margin-top:27px;">

                                            </div>
                                            </div>
                                    <div class="row" >
                                        <div class="col-md-2" style="margin-left: 28px;">
                                            <input type="submit" name="submit" value="Filter" class="btn btn-primary btn-lg">
                                        </div>

                                        <div class=" col-md-2">
                                        <a href="{{route('add.attribute')}}"> <button id="" type="button" class="btn btn-primary active col-md-6">
                                            <i class="icon-plus-circle"></i>  New
                                        </button>
                                        </a>
                                        </div>

                                </div>

                                   </form>
                        </div>
                </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Attributes
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">

                             <thead >

                              <tr
                              style="background-color: #3f51b5;color:white"
                              >
                                <th scope="col"><center>Name</center></th>
                                <th scope="col"><center>Arabic Name</center></th>
                                <th ><center>Category</center></th>
                                <th ><center>Presets</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Action</center></th>

                               </tr>

                             </thead>
                                @foreach($attributes as $list)

                                <tr>

                                    <td class="text-center">{{$list->name}}</td>
                                    <td class="text-center">{{$list->name_ar}}</td>
                                    <td class="text-center">
                                        @forelse ($list->attributeCategories as $value )
                                          {{ $value->name }},
                                        @empty
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @forelse ($list->presets as $value )
                                          {{ $value->name }},
                                        @empty
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @if($list->status == 1)
                                        <span class="badge badge-success r-5">Active</span>
                                        @else
                                        <span class="badge badge-danger r-5">In-Active</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                    <a href="{{route('edit.attribute',$list->id)}}"  class=""><i class="icon-edit"></i></a>
                                    {{--  <a href="{{route('destroy.option',$list->id)}}"  onclick="return confirm('Are You Sure Want To Delete ?')" class=""><i class="icon-close2 text-danger-o text-danger"></i></a>  --}}

                            </td>
                                </tr>
                                @endforeach
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
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
<script>

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
