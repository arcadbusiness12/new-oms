@extends('layout.theme')
@section('title', 'Home')
@section('content')
<style>
  .p-label{
    padding: 2px;
    border: 1px solid gray;
    background: lightgray;
  }
  .date-field {
      padding-top: 20px;
  }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="error-messages"></div>
        <div class="block-header">
            <h2>Custom Duties Report</h2>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form name="filter_reports" id="filter_reports" method="get" action="{{ Request::url() }}">
                                {{csrf_field()}}
                                <div class="row">
                                    @if( session('user_group_id') == 1 )
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label" for="user_id">User</label><br>
                                                <select name="user_id" id="user_id" class="form-control show-tick" data-live-search="true">
                                                    <option></option>
                                                   @foreach($users as $user)
                                                   <option value="{{ $user['user_id']}}"
                                                            {{ isset($old_input['user_id'])?
                                                            $user['user_id'] == @$old_input['user_id']?"selected='selected'":'' :''
                                                            }}
                                                            >{{$user['username']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>  
                                    @endif
                                    <div class="col-sm-4 date-field">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 date-field">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date To" value="{{isset($old_input['date_to'])?$old_input['date_to']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
          .rejected{
            background: #f3bfbf;
            color: #c70a0a;
          }
          .approved{
            background:#bdd8bd;
            color:green;
          }
          .green{
            color: green;
          }
          .red{
            color:#e83f3f;
          }
        </style>
        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="alert-response"></div>
                            <div class="row">
                                <div class="col-sm-12 table-responsive">
                                    <table class="table table-hover table-borderd" style="width: 100%">
                                        <?php if( count($done_histories) > 0) { 
                                            //$approved = '<i class="fa fa-check" style="color: #13a813; font-size:15px"></i>';
                                            $approved = '<i class="fa fa-check-circle green success-fs" title="Confirmed"></i>';
                                            //$rejected = '<i class="fa fa-times" style="color: #cf1919; font-size:15px"></i>';
                                            $rejected = '<i class="fa fa-times-circle red success-fs" title="cancel"></i>';

                                          ?>
                                        <thead class="blue-table-header">
                                            <th><center>User</center></th>
                                            <th><center>Duty</center></th>
                                            <th><center>Ponts Of Activity</center></th>
                                            <th><center>Activity Quantities</center></th>
                                            <th><center>Per Activity Quantities</center></th>
                                            <th><center>Cost Of Total Activity</center></th>
                                            <th><center>Achieved Activitis</center></th>
                                            <th><center>Salary Cost Per Activity</center></th>
                                            <!--<th><center>Salary</center></th> -->
                                        </thead>
                                        <tbody>
                                            @foreach($done_histories as $k => $user)
                                            @foreach($user['done_duties'] as $key => $duty)
                                            <tr style="border:1px solid lightgray">
                                                <td align="center">{{$user['firstname']}} {{$user['lastname']}}</td>
                                                <td align="center">{{$duty['duty']}} </td>
                                                <td align="center">{{$duty['point_of_job']}}</td>
                                                <td align="center">{{$duty['monthly_tasks']}}</td>
                                                <td align="center">{{$duty['per_activity_point']}}</td>
                                                <td align="center">{{$duty['cost_total_activties']}}</td>
                                                <td align="center">{{$duty['achieved_tasks']}}</td>
                                                <td align="center">{{$duty['task_payment']}}</td>
                                                
                                                {{-- <td align="center">{{@$da->statusHistories[0]['user']['firstname']}} {{@$da->statusHistories[0]['user']['lastname']}}</td> --}}
                                                <!-- <td align="center">1000000</td> -->
                                                
                                            </tr>
                                            @endforeach
                                            @if(count($user['done_duties']) > 0)
                                            <tr style="border:1px solid lightgray;background-color: lavender;">
                                                <td align="right" colspan="7"><strong>Total Salary:</strong></td>
                                                <td align="center"><strong>{{$user['achieved_salary']}}</strong></td>
                                                
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                        <?php }else{ ?>
                                        <tr>
                                            <td class="text-center text-danger">No data Found!</td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                    {{-- <div id="group-paginate">
                                        {{$done_histories->links()}}
                                    </div> --}}
                                </div>
                            </div>
                            <div class="pull-right">
                              
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