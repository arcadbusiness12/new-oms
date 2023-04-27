@extends('layouts.app')

@section('content')
<style>
  td {
    vertical-align: middle !important;
  }
  .right_border{
    border-right: 1px solid white !important;
  }
  .right_border_bk{
    border-right: 1px solid black !important;
    border-bottom: 1px solid black !important;
  }
  .more_details{
    font-weight: bolder;
    font-size: 17px;
    cursor: pointer;
  }
  .font-red{
    color: red;
  }
  .btn-success {
    background-color: green !important;
  }
  .btn-danger {
    background-color: #ed5564 !important;
  }
  .btn-warning {
    background-color: #fcce54 !important;
  }
</style>
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
                            <div class="panel-body">
                                <form name="filter_reports" id="filter_reports" method="get" action="{{ route('chat.sale.order.report') }}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <div class="form-line">
                                                <select name="by_duration" id="by_duration" class="form-control show-tick" data-live-search="true" onchange="showDates()">
                                                  <option value="today"     {{ ("today"==@$old_input['by_duration']) ? "selected" : "" }}>Today</option>
                                                  <option value="yesterday" {{ ("yesterday"==@$old_input['by_duration']) ? "selected" : "" }}>Yesterday</option>
                                                  <option value="thisweek"  {{ ("thisweek"==@$old_input['by_duration']) ? "selected" : "" }}>This week</option>
                                                  <option value="lastweek"  {{ ("lastweek"==@$old_input['by_duration']) ? "selected" : "" }}>Last week</option>
                                                  <option value="thismonth" {{ ("thismonth"==@$old_input['by_duration']) ? "selected" : "" }}>This Month</option>
                                                  <option value="lastmonth" {{ ("lastmonth"==@$old_input['by_duration']) ? "selected" : "" }}>Last Month</option>
                                                  <option value="custom"    {{ ("custom"==@$old_input['by_duration']) ? "selected" : "" }}>custom</option>
                                                </select>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <select name="user">
                                                <option value="">-User-</option>
                                                 @foreach( $users as $user)
                                                  <option value="{{ @$user->user_id }}" {{ ($user->user_id==@$old_input['user']) ? "selected" : "" }}>{{ $user->username }}</option>
                                                 @endforeach
                                              </select>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                              <select name="campaign">
                                                <option value="">-campaign-</option>
                                                 @foreach( $campaigns as $campaignn)
                                                  <option value="{{$campaignn->id}}" {{ ($campaignn->id==@$old_input['campaign']) ? "selected" : "" }}>{{ $campaignn->campaign_name }}</option>
                                                  {{-- <option value="{{ $campaignn->id }} {{($campaignn->id == @$old_input['by_duration']) ? 'selected' : ''}}">{{ $campaignn->campaign_name }} {{$campaign->id}}={{$campaignn->id}}</option> --}}
                                                 @endforeach
                                              </select>
                                          </div>
                                        </div>
                                    </div>
                                    <div class="row custom_duration" <?php if(@$old_input['by_duration'] != 'custom'){ ?> style="display:none" <?php } ?> >
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">From</label>
                                                  <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_from'] != "" ? $old_input['date_from'] : '' }}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-4">
                                          <div class="form-group form-float">
                                            <div class="form-line">
                                            <label class="form-label">To</label>
                                                  <input type="text" name="date_to" id="date_to" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" value="{{ @$old_input['date_to'] != "" ? $old_input['date_to'] : ''  }}">
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

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Ads Chat Report
                          </div>
                          @foreach ($campaignns as $k => $campaign)
                          <div id="tab{{$campaign->id}}" class="tab-pane {{($k == 0) ? 'fade in active' : ''}}">
                          
                            <span><label>Budget Type: </label> {{@$campaign->mainSetting->budgetType['name']}}</span>,
                            <span class="range-text"><label>Range: </label> {{ @$campaign->mainSetting->range }}</span>,
                            <span class="range-text"><label>Ad Type: </label> {{ @$campaign->mainSetting->adsType->name }}</span>,
                            <span class="range-text"><label>Users: </label>
                              @foreach ($campaign->users as $user)
                              {{$user->username}}, 
                              @endforeach
                              </span>
                            
                            <span class="range-text"><label>{{(@$campaign->start_date) ? 'Campaign Start Date:' : ''}} </label> {{@$campaign->start_date}}</span>
                          
                            <table class="table table-hover">
                              <thead style="background-color: #3379b7; color:white">
                                <tr>
                                  {{-- <th class="right_border"><center>S.No</center></th> --}}
                                  <th class="right_border"><center>Group</center></th>
                                  <th class="right_border"><center>Budget</center></th>
                                  <th class="right_border"><center>Chats</center></th>
                                  <th class="right_border"><center>Sold Out</center></th>
                                </tr>
                              </thead>
                              <tbody>
                                  @if($campaign)
                                  @if(count($campaign->paidAds) > 0)
                                  @foreach ($campaign->paidAds as $kn => $row)
                                  @php 
                                  if($row->totalSoldQuantities > 0) {
                                    $perchatOrder = $row->chatTotals/$row->totalSoldQuantities; 
                                  
                                      $chatOrders = round($perchatOrder);

                                      if($chatOrders <= 6) {
                                        $bgcolor = 'green ';
                                      }
                                      if($chatOrders > 6 && $chatOrders <= 12) {
                                        $bgcolor = 'yellow';
                                      }
                                      if($chatOrders > 12) {
                                        $bgcolor = 'orange';
                                      }
                                  }else {
                                    $bgcolor = 'red';
                                  }
                                  
                                  @endphp
                                      <tr style="border-bottom:1px solid black">
                                        {{-- <td align="center" style="font-weight:700;">{{ $kn+1 }}</td> --}}
                                        <td align="center" style="font-weight:700; font-size:15px;">{{ $row->group_code }}</td>
                                          <td align="center" style="font-weight:700; font-size:15px;">{{ $row->budget }}</td>
                                          <td align="center" style="font-weight:700; font-size:15px;">{{ $row->chatTotals }}</td>
                                          <td align="center" style="font-weight:700; font-size:16px;background-color:{{$bgcolor}}; color:{{($bgcolor) ? 'white' : 'black'}}">
                                            <strong> {{ $row->totalSoldQuantities }}</strong>
                                          </td>
                                      </tr>
                                      
                                  @endforeach
                                  @else 
                                  <tr>
                                    <td colspan="5" class="text-center">No Ads Available..</td>
                                </tr>
                                @endif
                                  @else
                                      <tr>
                                          <td colspan="5" class="text-center">No data found!</td>
                                      </tr>
                                  @endif
                              </tbody>
                          </table>

                          </div>
                        @endforeach

            </div>
           

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script>
  $(document).on('click','.more_details',function(){
    var element_html = $(this).html();
    //$('.user_det').hide();
    if( element_html === "+" ){
     $(this).html('-');
     $(this).parent().parent().next('tr').show();
    }else if( element_html === "-" ){
     $(this).html('+');
     $(this).parent().parent().next('tr').hide();
    }
  });
  function showDates(){
    var duration = $('#by_duration').val();
    //alert(duration);
    if( duration == "custom" ){
      $('.custom_duration').show();
    }else{
      $('.custom_duration').hide();
    }
  }
</script>
@endpush
