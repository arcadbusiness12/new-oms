@extends('layouts.app')

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
        {{-- <div class="block-header">
            <h2>Custom Duties Report</h2>
        </div> --}}

        <div class="row">
            <div class="col-xs-12">
                <div class="row mb-4">
                    <div class="col-md-12 col-sm-12">
    
                        <div class="card no-b form-box">
                            @if(session()->has('success'))
                                <div class="alert alert-success">
                                    {{ session()->get('success') }}
                                </div>
                            @endif
    
                            <div class="card-header white">
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

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <select name="progress" class="form-control move-select">
                                                    <option value="">Select Stage</option>
                                                    <option value="0" <?php  if(isset($old_input['progress']) && $old_input['progress'] == 0) { echo 'selected';} ?>>To Do</option>
                                                    <option value="1" <?php  if(isset($old_input['progress']) && $old_input['progress'] == 1) { echo 'selected';} ?>>Doing</option>
                                                    <option value="2" <?php  if(isset($old_input['progress']) && $old_input['progress'] == 2) { echo 'selected';} ?>>Testing</option>
                                                    <option value="5" <?php  if(isset($old_input['progress']) && $old_input['progress'] == 5) { echo 'selected';}
                                                     ?>>Completed</option>
                                                </select>
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
                                        <?php if( count($reports) > 0) { 
                                            //$approved = '<i class="fa fa-check" style="color: #13a813; font-size:15px"></i>';
                                            $approved = '<i class="fa fa-check-circle green success-fs" title="Confirmed"></i>';
                                            //$rejected = '<i class="fa fa-times" style="color: #cf1919; font-size:15px"></i>';
                                            $rejected = '<i class="fa fa-times-circle red success-fs" title="cancel"></i>';

                                          ?>
                                        <thead class="blue-table-header" style="background-color: #3f51b5;color:white">
                                            <th><center>User</center></th>
                                            <th><center>Duty</center></th>
                                            <th><center>Start Date</center></th>
                                            <th><center>End Date</center></th>
                                            <th><center>Event Date</center></th>
                                            <th><center>Progress</center></th>
                                            <th><center>Moved</center></th>
                                            <!--<th><center>Salary</center></th> -->
                                        </thead>
                                        <tbody>
                                            @foreach($reports as $da)
                                            <tr style="border:1px solid lightgray">
                                                <td align="center">{{$da->user->firstname}} {{$da->user->lastname}}</td>
                                                <td align="center">{{$da->title}}</td>
                                                <td align="center">{{ date('d M, Y', strtotime($da->start_date)) }}</td>
                                                <td align="center">{{ date('d M, Y', strtotime($da->end_date)) }}</td>
                                                <td align="center">{{ $da->event_date ? date('d M, Y', strtotime($da->event_date)) : '' }}</td>
                                                <td align="center">
                                                    @if($da['progress'] == 0)
                                                    <div class="badge badge-warning"><strong> In To Do list </strong></div>
                                                    @if($da->end_date < date('Y-m-d')) 
                                                        <div class="badge badge-danger"><strong> Expired </strong></div>
                                                        @endif
                                                    @elseif($da['progress'] == 1)
                                                    <div class="badge badge-primary"><strong>In Doing list </strong>
                                                    </div>
                                                    @if($da->end_date < date('Y-m-d')) 
                                                        <div class="badge badge-danger"><strong> Expired </strong></div>
                                                        @endif
                                                    @elseif($da['progress'] == 2)
                                                    <div class="badge badge-info"><strong>In Testing list </strong></div>
                                                    @else
                                                    <div class="badge badge-success"><strong>In Completed list </strong></div>
                                                    @endif
                                                </td>
                                                <td align="center">{{@$da->statusHistories[0]['user']['firstname']}} {{@$da->statusHistories[0]['user']['lastname']}}</td>
                                                <!-- <td align="center">1000000</td> -->
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <?php }else{ ?>
                                        <tr>
                                            <td class="text-center text-danger">No data Found!</td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                    <div id="group-paginate">
                                        {{$reports->links()}}
                                    </div>
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