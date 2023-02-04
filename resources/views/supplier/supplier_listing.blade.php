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
            <div class="row mb-4">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">Supplier</div>

                        {{--  <div class="panel-heading">Inventory Dashboard</div>  --}}
                          @if(session()->has('success'))
                            <div role="alert" class="alert alert-success">
                            {{ session()->get('success') }}
                            </div>
                          @endif

                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="font-size: 14px !important; color:black !important">
                             <thead>
                              <tr style="background-color: #3f51b5;color:white">
                                {{--  <th scope="col">&nbsp;</th>  --}}
                                <th scope="col">Username</th>
                                <th scope="col"><center>First Name</center></th>
                                <th scope="col"><center>Last Name</center></th>
                                <th scope="col"><center>Email</center></th>
                                <th scope="col"><center>Group</center></th>
                                <th scope="col"><center>Status</center></th>
                                <th scope="col"><center>Created At</center></th>
                                <th scope="col"><center>Action</center></th>
                               </tr>
                             </thead>
                             <tbody>
                                @if($staffs->count())
                                @foreach($staffs as $key=>$row)
                                    <tr class="row_{{ $row->order_id }}" style="border:1px solid lightgray !important">
                                        {{--  <td class="col-sm-1"><input type="checkbox" class="order_checkbox" value="{{ $row->id }}" /></td>  --}}
                                        <td class="col-sm-1">{{ $row->username }}</td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->firstname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->lastname }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->email }}</center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->userGroupName?->name }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{!! ( $row->status == 1 ) ? "<i style='color:green'>Active</i>" : "<i style='color:red'>Inactive</i>" !!} </center></td>
                                        <td class="column col-sm-1 td-valign"><center>{{ $row->created_at }} </center></td>
                                        <td class="column col-sm-1 td-valign"><center></center></td>
                                    </tr>
                                @endforeach
                                @endif
                             </tbody>
                            </table>
                        {{--  {{  $staffs->appends(@$old_input)->render() }}  --}}
                    </div>

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
