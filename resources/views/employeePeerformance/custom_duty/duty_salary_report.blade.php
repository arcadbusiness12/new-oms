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
                                    <div class="col-sm-3">
                                      <div class="form-group form-float">
                                          <div class="form-line">
                                              <label class="form-label" for="user_group_id">User Group</label>
                                              <select name="user_group_id" id="user_group_id" class="form-control show-tick" data-live-search="true">
                                                  <option value="">--Select--</option>
                                                 @foreach($user_groups as $group)
                                                 <option value="{{ $group->id }}"
                                                          {{ isset($old_input['user_group_id'])?
                                                          $group->id  == @$old_input['user_group_id']?"selected='selected'":'' :''
                                                          }}
                                                          >{{$group['name']}}</option>
                                                  @endforeach
                                              </select>
                                          </div>
                                      </div>
                                    </div>  
                                    <div class="col-sm-3">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label class="form-label" for="user_id">User</label>
                                                <select name="user_id" id="user_id" class="form-control show-tick" data-live-search="true">
                                                    <option value="">--Select--</option>
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
                                    <div class="col-sm-3 date-field">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 date-field">
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
          .bg-light-red{
            background: #f9000029;
          }
          .bg-light-grn{
            background: #bbfdad47 !important;
          }
          .bg-light-gray{
            background: lightgray !important;
          }
          tbody {
            display:block;
            max-height:700px;
            overflow-x: hidden;
            overflow-y: auto;
            }
            thead, tbody tr {
                display:table;
                width:100%;
                table-layout:fixed;
            }
            .right_border {
              border-right: 1px solid white !important;
            }
            .v-align-middle {
              vertical-align: middle !important;
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
                                        <thead style="background-color: #3379b7; color:white">
                                            <tr>
                                              <th class="right_border v-align-middle" rowspan="2"><center>User</center></th>
                                              <th class="right_border v-align-middle" rowspan="2"><center>Duty</center></th>
                                              <th colspan="3" class="right_border"><center>Activity</center></th>
                                              <th colspan="2" class="right_border"><center>Points</center></th>
                                              <th class="right_border v-align-middle"  rowspan="2"><center>Progress</center></th>
                                            </tr>
                                            <tr>
                                              <th class="right_border"><center>Ponts Per Activity</center></th>
                                              <th class="right_border"><center>Monthly Target</center></th>
                                              <th class="right_border"><center>Achieved</center></th>
                                              <th class="right_border"><center>Monthly Target</center></th>
                                              <th class="right_border"><center>Achieved</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($done_histories as $k => $user)
                                            @php 
                                              $t_salary = 0;
                                              $total_deduction = 0;
                                            @endphp
                                            @foreach($user['assigned_custome_duties'] as $key => $duty)
                                            @php
                                            $tr_class = "";
                                            $acheived_class = "";
                                            $working_days = $daysWithoutHoliday;
                                            $t_custom = [];
                                                foreach ($user['custom_duties'] as $key => $cDuty) {
                                                    if($cDuty['duty_list_id'] == $duty['custom_duty']['id']) {
                                                        array_push($t_custom, $cDuty['achieved']);
                                                    }
                                                }

                                                $total_points = ($duty['duration'] > 0) ? $working_days/$duty['duration']*$duty['point']*$duty['quantity'] : $duty['quantity'];
                                                if( $duty['activity_id'] == 4 || $duty['activity_id'] == 1 || $duty['activity_id'] == 3 ){
                                                  $working_days  = 30;
                                                  $total_points = $duty['monthly_tasks'];
                                                }
                                                
                                                $mtasks = ($duty['duration'] > 0) ? $working_days/$duty['duration']*$duty['quantity'] : $duty['quantity'];
                                                foreach ($user['performance_sales'] as $key => $pDuty) {
                                                    if($pDuty['duty_list_id'] == $duty['custom_duty']['id']) {
                                                        //echo "<h1>First</h1>"; die("abc");
                                                        array_push($t_custom, $pDuty['achieved']);
                                                    }
                                                    if( $pDuty['duty_list_id'] == 2 &&  $duty['activity_id'] == 2 ){ //2 for orders
                                                      //echo "<h1>TEST3</h1>"; die("abc");
                                                      $mtasks = $pDuty['target'] * $duty['quantity'];
                                                    }
                                                }
                                                foreach ($user['done_duty_histories'] as $key => $postDuty) {
                                                    if($postDuty['duty_id'] == $duty['custom_duty']['id']) {
                                                        array_push($t_custom, $postDuty['achieved']);
                                                    }
                                                }
                                                $acheived_activities = array_sum($t_custom);
                                                $acheived_points     = $acheived_activities * $duty['point'];
                                                $extra_points = $acheived_points - $total_points;
                                                /*if( $user['user_group_id'] == 12 && $extra_points > 0 ){ // if sale team & got extra points
                                                  $salary = $user['per_p_salary'] * $total_points; //then calculate salary with target point
                                                }else{
                                                  $salary = $user['per_p_salary'] * $acheived_points;
                                                }*/
                                                $salary = $user['per_p_salary'] * $acheived_points;
                                                $t_salary           += $salary;
                                                $acheived_percentage = $mtasks==0 ? 0 : ($acheived_activities/$mtasks) * 100;
                                                $acheived_percentage = number_format($acheived_percentage,2);
                                                
                                                if( $user['user_group_id'] == 12 && $duty['daily_compulsory'] == 1 ){
                                                  if($acheived_percentage < 75){
                                                    $acheived_class = "red";
                                                    $tr_class = "bg-light-red";
                                                    $acheived_percentage = -25;
                                                    $total_deduction += $acheived_percentage;
                                                  }else{
                                                    $acheived_class = "green";
                                                    $tr_class = "bg-light-grn";
                                                    $acheived_percentage = "Done";
                                                  }
                                                }
                                                $payable_salary = $user['salary'] + $total_deduction;
                                            @endphp
                                            <tr style="border-bottom:1px solid lightgray" class="{{ $tr_class }}">
                                                <td align="center">{{$user['firstname']}} {{$user['lastname']}}</td>
                                                <td align="center">{{$duty['custom_duty']['name']}} </td>
                                                <td align="center">{{$duty['point']}}</td>
                                                <td align="center">{{$mtasks}}</td> <!--monthly activity-->
                                                <td align="center">{{ $acheived_activities }}</td>
                                                <td align="center">{{$total_points}}</td>
                                                <td align="center">{{ $acheived_points }}</td>
                                                {{--  <td align="center">{{ $salary }}</td>  --}}
                                                <td align="center" class="{{ @$acheived_class }}" >{{ $acheived_percentage }}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="border-bottom:1px solid lightgray">
                                              <td colspan="6" align="right">Total Salary</td>
                                              <td  align="center">{{$user['salary']}}</td>
                                            </tr>
                                            <tr>
                                              <td colspan="6" align="right">Total Deduction</td>
                                              <td  align="center">{{  $total_deduction }}</td>
                                            </tr>
                                            <tr class="bg-light-gray" style="border-bottom: 1px solid #3379b7">
                                                <td  colspan="6" align="right" ><strong>Payable Salary:</strong></td>
                                                <td align="center"><strong> {{ $payable_salary }} </strong></td>
                                            </tr>
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