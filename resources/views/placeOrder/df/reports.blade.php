@extends('layout.theme')
@section('title', 'Home')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="error-messages"></div>
        <div class="block-header">
            <h2>Order Report</h2>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form name="filter_reports" id="filter_reports" method="get">
                            {{csrf_field()}}
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <lebel class="form-label" for="order_id">Order ID</lebel>
                                                <input type="text" name="order_id" id="order_id" class="form-control" autocomplete="off" value="{{ isset($old_input['order_id'])?$old_input['order_id']:'' }}">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <lebel class="form-label" for="total">Min Amount</lebel>
                                                <input type="text" name="min_amount" id="min_amount" class="form-control" autocomplete="off" value="{{ isset($old_input['min_amount'])?$old_input['min_amount']:''}}">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <lebel class="form-label" for="total">Max Amount</lebel>
                                                <input type="text" name="max_amount" id="max_amount" class="form-control" autocomplete="off" value="{{ isset($old_input['max_amount'])?$old_input['max_amount']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <!-- 4 for main admin -->
                                        <?php if($role == 'ADMIN' || session('user_group_id')==4 || session('user_group_id')==8 ) { ?>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label" for="user_id">User</label>
                                                <select name="user_id" id="user_id" class="form-control show-tick" data-live-search="true">
                                                    <option></option>
                                                    @foreach($staffs as $staff)
                                                    <option value="{{ $staff['user_id']}}"
                                                            {{ isset($old_input['user_id'])?
                                                            $staff['user_id'] == $old_input['user_id']?"selected='selected'":'' :''
                                                            }}
                                                            >{{$staff['username']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date To" value="{{isset($old_input['date_to'])?$old_input['date_to']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label order_status" for="order_status_id">Order Status</label>
                                                <select name="order_status_id" id="order_status_id" class="form-control show-tick" data-live-search="true">
                                                    <option></option>
                                                    @foreach($orderStatus as $status)
                                                    <option value="{{ $status['order_status_id']}}"
                                                            {{ isset($old_input['order_status_id'])?
                                                            $status['order_status_id'] == $old_input['order_status_id']?"selected='selected'":'' :''
                                                            }}
                                                            >{{$status['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <label for="generate_csv"> <input type="checkbox"  name="generate_csv" id="generate_csv">&nbsp;&nbsp; Export Excel</label>
                                        <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="panel panel-default">
                        <div class="panel-heading text-right">
                            Showing {{($orders_data->currentpage()-1) * $orders_data->perpage()+1}} to {{(($orders_data->currentpage()-1)*$orders_data->perpage())+$orders_data->count()}} of <strong>{{$orders_data->total()}}</strong>
                        </div>
                        <div class="panel-body">
                            <div id="alert-response"></div>
                            <div class="row">
                                <div class="col-sm-12 table-responsive">
                                    <table class="table">
                                        <?php if($orders) { ?>
                                        <thead>
                                            <th>Order ID</th>
                                            <?php if($role == 'ADMIN' || $role == 'STAFF') { ?>
                                            <th>User</th>
                                            <?php } ?>
                                            <th>Amount</th>
                                            <th>Courier Company</th>
                                            <th>Airway Bill</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $key => $order) { ?>
                                            <tr>
                                                <td><?php echo $order['order_id'] ?></td>
                                                <?php if($role == 'ADMIN' || $role == 'STAFF') { ?>
                                                <td><?php echo $order['user'] ?></td>
                                                <?php } ?>
                                                <td><?php echo $order['amount'] ?></td>
                                                <td>@php echo $order['shipping_company'] @endphp</td>
                                                <td>{{ $order['airwaybill_no'] }}</td>
                                                <td><?php echo $order['status'] ?></td>
                                                <td><?php echo $order['date'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                        <?php }else{ ?>
                                        <tr>
                                            <td class="text-center text-danger">No Orders Found!</td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                            <div class="pull-right">
                                {{$pagination}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<link rel="stylesheet" href="{{URL::asset('assets/plugins/select2/select2.min.css') }}">
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
@endpush