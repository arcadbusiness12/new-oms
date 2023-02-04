@extends('layouts.app')

@section('content')
<style>
    .sw-theme-circles>ul.step-anchor:before {
        //top: 36%!important;
        width: 68%!important;
        margin-left: 39px!important;
    }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Add User</div>
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif
                          <form action="{{ route('setting.users.add') }}" method="post" name="form-setting">
                            <div class="block-header">
                                {{-- <div class="pull-left">
                                    <h2>Add Staff</h2>
                                </div> --}}
                                <div class="float-right">
                                    <button type="submit" class="btn btn-success active">SAVE</button>
                                    <a href="{{ route('setting.users') }}"><button type="button" class="btn btn-info active">Back</button></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            {{ csrf_field() }}
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <?php if(Session::has('message')) { ?>
                                    <div class="alert <?php echo Session::get('alert-class', 'alert-info') ?> alert-dismissible">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        <?php echo Session::get('message') ?>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="row" style="padding: 31px; color: black;">
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" value="{{ old('username') }}" class="form-control" autocomplete="off" />
                                                @error('username')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Firstname</label>
                                                <input type="text" name="firstname" value="{{ old('firstname') }}" class="form-control" autocomplete="off" />
                                                @error('firstname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Lastname</label>
                                                <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" autocomplete="off" />
                                                @error('lastname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" value="{{ old('email') }}"  class="form-control" autocomplete="off"  />
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Password</label>
                                                <input type="text" name="password" class="form-control" value="{{ old('password') }}" autocomplete="off" />
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="text" name="password_confirmation" class="form-control" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group form-float">
                                            <label class="form-label">User Group</label>
                                            <div class="form-line">
                                                <select name="user_group_id" id="user_group_id" class="form-control show-tick"  >
                                                    <option value="">--Select User Group--</option>
                                                    <?php foreach ($userGroups as $key => $userGroup) { ?>
                                                    <option value="{{  $userGroup->id }}">{{ $userGroup->name }}</option>
                                                    <?php } ?>
                                                </select>
                                                @error('user_group_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="commission_on_delivered_amount">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Commission on delivered orders Amount</label>
                                                <input type="number" name="commission_on_delivered_amount" id="commission_on_delivered_amount" min="0" class="form-control" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Basic Salary</label>
                                                <input type="text" name="basic_salary"  value="0" class="form-control" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label">Target Salary</label>
                                                <input type="text" name="salary" value="" value="0" class="form-control" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-float">
                                            <label class="form-label">Status</label>
                                            <div class="form-line">
                                                <select name="status" class="form-control show-tick">
                                                    <option value="1">Enabled</option>
                                                    <option value="0">Disabled</option>
                                                </select>
                                            </div>
                                        </div>
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
@include('accounts.popup_receipt_details')

<style>
.table td {
    border-top: none !important;
}
thead, tbody, tfoot, tr, td, th {
    border: none !important;
}
</style>
@endsection

@push('scripts')
<script>
    $(document).on('click','#order_history',function(event){
        //console.log(event);
        console.log($(this).attr('data-id'));
        var order_id = $(this).attr('data-id');
        $.ajax({
            method: "POST",
            url: APP_URL + "/accounts/get/receipt/popup",
            data: {id:order_id},
            //dataType: 'json',
            cache: false,
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            },
        }).done(function (data)
        {
            //alert(data);
            $('#receiptModal_content').html(data);
        }); // End of Ajax
    });
    function printContent(el){
        var restorepage = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent).css('padding-top','0px');
        window.print();
        $('body').html(restorepage);
        window.location.href=window.location.href;
    }
</script>
@endpush
