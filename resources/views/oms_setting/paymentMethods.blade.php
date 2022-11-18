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
                                        <a href="javascript:;"> <button id="" type="button" class="btn btn-primary active add-method">
                                            <i class="icon-plus-circle"></i>  New
                                        </button>
                                        </a>
                                        </div>

                        </div>
                </div>
                </div>
            </div>
            <div class="card no-b form-box">
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Payment Methods
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">

                             <thead >

                              <tr
                              style="background-color: #3f51b5;color:white"
                              >
                                <th scope="col"><center>Name</center></th>
                                <th scope="col"><center>Code</center></th>
                                <th scope="col"><center>Fee</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Action</center></th>

                               </tr>

                             </thead>
                             @if(count($methods) > 0)
                                @foreach($methods as $method)

                                    <tr>

                                        <td class="text-center">{{$method->name}}</td>
                                        <td class="text-center">{{$method->code}}</td>
                                        <td class="text-center">{{$method->fee}}</td>
                                        <td class="text-center">
                                            @if($method->status == 1)
                                            <span class="badge badge-success r-5">Active</span>
                                            @else
                                            <span class="badge badge-danger r-5">In-Active</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:;" class="" onclick="editMothod({{$method}})"><i class="icon icon-edit"></i></a>
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
</div>

<!-- Modal -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1" data-backdrop="static" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color: aliceblue;">
          <h5 class="modal-title" id="ModalLabel">Add Payment Method</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{route('add.payment.method')}}" method="POST">
            {{ csrf_field() }}
              <div class="form-group">
                  <label class="text-black">Name<span class="text-danger">*</span></label>
                  <input type="text" name="name" id="name" class="form-control">
                  <input type="hidden" name="payment_method_id" id="payment_method_id" class="form-control">
              </div>
              <div class="form-group">
                <label class="text-black">Code<span class="text-danger">*</span></label>
                <input type="text" name="code" id="code" class="form-control">
            </div>
            <div class="form-group">
                <div class="col-6 col-grid">
                    <label class="text-black">Fee</label>
                    <input type="number" name="fee" id="fee" class="form-control">
                </div>
                <div class="col-6 col-grid">
                    <label class="text-black">Status</label>
                    <select class="form-control custom-select" name="status" id="select-status">
                        <option value="1">Active</option>
                        <option value="0">In-Active</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Method</button>
              </div>
          </form>
        </div>
      
      </div>
    </div>
  </div>

  
  {{-- Modal End  --}}
<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
<script>
    $('.add-method').on('click', function () {
        $('#ModalLabel').text('Add Payment Method');
        $('#paymentMethodModal').modal('show');
    });

    function editMothod(object) {
        if(object) {
            $('#name').val(object.name);
            $('#code').val(object.code);
            $('#fee').val(object.fee);
            $('#payment_method_id').val(object.id);
            var html = '';
            var active = (object.status == 1) ? 'selected' : '';
            var inActive = (object.status == 0) ? 'selected' : '';
            html += '<option value="1" '+active+'>Active</option>';
            html += '<option value="0" '+inActive+'>In-Active</option>';
            $('#select-status').html(html);
            $('#ModalLabel').text('Edit Payment Method');
            $('#paymentMethodModal').modal('show');
        }
    }
    
    $('.btn-close, .close-btn').on('click', function() {
        $('#paymentMethodModal').find('form').trigger('reset');
    })
</script>
@endpush
