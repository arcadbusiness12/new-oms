@extends('layouts.app')

@section('content')
<style>
    .status_published .bootstrap-select .dropdown-toggle{
    display: none;
  }
  .catalog_product_add .bootstrap-select .dropdown-toggle{
    display: none;
  }
  .row{
    margin-top: 5px;
  }
  .top-lable {
    color: darkgray;
  }
  .point-target-input {
    width: 100% !important;
  }
  label {
      font-weight: 700 !important;
      color: #000;
  }
  .top-lable {
      color: white;
  }
  .p-label {
    margin-right: 13px;
    text-align: center;
  }
  .all-user-progress-button {
    font-size: 18px;
  }
</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">
          @if ($errors->any())
          <div class="alert alert-warning">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif
        @if(Session::has('query_status'))
          <div class="alert alert-success">
            {{ Session::get('query_status') }}
          </div>
        @endif

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                          <div class="panel-heading">
                            Add Sale Progress
                          </div>
                          <div class="p-4">
                           <div id="status_changed_msg" style="display: none"></div>
                           <form action="{{route('performance.operation.save.conversation')}}" method="POST">
                            {{csrf_field()}}
                            <div class="row">
                              <div class="col-sm-2">
                                
                              </div>
                            </div>
                            @forelse($sale_team as $key => $row)
                            
                              @php
                                  $user_row = $opening_conversation[$row->user_id];
                              @endphp
                              
                                      <div class="row">
                                        <div class="col-2">
                                          <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                            @if(@$user_row && @$user_row['conversation_closing'] > 0 )
                                            <span class="badge btn-success pull-right"><small>Completed</small></span>
                                          @endif
                                              <a class="nav-link all-user-progress-button" id="v-pills-sale_person_name{{ $row->user_id }}-tab" data-toggle="pill" href="#v-pills-sale_person_name{{ $row->user_id }}" role="tab" aria-controls="v-pills-sale_person_name{{ $row->user_id }}" aria-selected="false" onclick="showUserProgress({{ $row->user_id }})">{{ $row->username }}</a>
                                              <input type="hidden" name="user_id[]" class="form-control" value="{{ $row->user_id }}" autocomplete="off">
                                          </div>
                                        </div>

                                        <div class="col-10">
                                          <div class="tab-content" id="v-pills-tabContent">
                                            <div class="tab-pane fade" id="v-pills-sale_person_name{{ $row->user_id }}" role="tabpanel" aria-labelledby="v-pills-sale_person_name{{ $row->user_id }}-tab">
                                              <table class="table table-bordered table-hover all-user-progress" id="progress_details_{{ $row->user_id }}">
                                                @if($opening_conversation[$row->user_id] > 0)
                                                <input type="hidden" name="sale_perf_id[{{ $row->user_id }}]" value="{{ $opening_conversation[$row->user_id]['id'] }}">
                                                @endif
                                                <tbody>
                                                  {{--  <tr>
                                                    <td><strong>Order Taken</strong></td>
                                                    <td>{{ $opening_conversation[$row->user_id]['orders_taken'] }}</td>
                                                  </tr>
                                                  <tr>
                                                    <td><strong>Exchange Created</strong></td>
                                                    <td>{{ $opening_conversation[$row->user_id]['exchange_created'] }}</td>
                                                  </tr>  --}}
                                                  @foreach($row->activities as $activity)
                                                  <tr>
                                                    <td ><strong>{{$activity->name}}</strong></td>
                                                    <td align="left">
                                                      @php
                                                        $status_product_confirmed = 1;
                                                      @endphp
                                                        @if(isset($activity->achieved) && is_array($activity->achieved)) 
                                                        @forelse($activity->achieved as $k => $achieved_details)
                                                        <label class="p-label " >
                                                          {{$achieved_details['name']}} <br>
                                                          <input name="confirm_detail_id[{{ $row->user_id }}][]" type="hidden" value="{{$achieved_details['id']}}" >
                                                          <input name="confirm_status_pub_prd_detail[{{ $row->user_id }}][{{$achieved_details['id']}}]" id="confirm_status_pub_prd_detail_yes{{  $row->user_id }}{{ $achieved_details['id'] }}" type="radio" value="1" @if($achieved_details['confirm'] == 1) checked @endif><label for="confirm_status_pub_prd_detail_yes{{  $row->user_id }}{{ $achieved_details['id'] }}">Yes</label>
                                                          <input name="confirm_status_pub_prd_detail[{{ $row->user_id }}][{{$achieved_details['id']}}]" id="confirm_status_pub_prd_detail_no{{  $row->user_id }}{{ $achieved_details['id'] }}" type="radio"  value="0" @if($achieved_details['confirm'] == 0) checked @endif><label for="confirm_status_pub_prd_detail_no{{  $row->user_id }}{{ $achieved_details['id'] }}">No</label> 
                                                        </label>
                                                        @empty
                                                        <p class="danger">No data found.</p>
                                                        @endforelse
                                                        @else
                                                        @if($activity->activity_id == 64 || $activity->activity_id == 65)
                                                          <input name="chat_id[{{ $row->user_id }}][]" type="hidden" value="{{$activity['performance_id']}}" >
                                                          <input name="activity_id[{{ $row->user_id }}][]" type="hidden" value="{{$activity->activity_id}}" >
                                                          <input name="chat_prd[{{ $row->user_id }}][{{$activity['performance_id']}}]" id="confirm_status_pub_prd_yes{{ $row->user_id }}{{ $activity['performance_id'] }}" type="text" value="{{$activity->achieved ? $activity->achieved : 0}}">
                                                          @else
                                                         {{$activity->achieved}} <br> 
                                                          @if($activity['performance_id']) 
                                                          <input name="confirm_status_id[{{ $row->user_id }}][]" type="hidden" value="{{$activity['performance_id']}}" >
                                                          
                                                          <input name="confirm_status_pub_prd[{{ $row->user_id }}][{{$activity['performance_id']}}]" id="confirm_status_pub_prd_yes{{ $row->user_id }}{{ $activity['performance_id'] }}" type="radio" value="1" @if($activity['confirm'] == 1) checked @endif><label for="confirm_status_pub_prd_yes{{ $row->user_id }}{{ $activity['performance_id'] }}">Yes</label>
                                                          <input name="confirm_status_pub_prd[{{ $row->user_id }}][{{$activity['performance_id']}}]" id="confirm_status_pub_prd_no{{ $row->user_id }}{{ $activity['performance_id'] }}" type="radio"  value="0" @if($activity['confirm'] == 0) checked @endif><label for="confirm_status_pub_prd_no{{ $row->user_id }}{{ $activity['performance_id'] }}">No</label>
                                                          
                                                          @else
                                                          <p class="danger">No data found.</p>
                                                          @endif
                                                          
                                                          @endif
                                                        @endif
                                                        
                                                    </td>
                                                  </tr>
                                                  
                                                @endforeach
                                                </tbody>
                                              </table>
                                          </div>
                                          </div>
                                        </div>
                                      </div>
                                      <hr style="background-color:#726b6b;">
                                      
                              <br>
                              
                            @empty
                              <div class="row">
                                <p class="danger">No data found.</p>
                              </div>
                            @endforelse
                            <br>
                            <button type="submit" class="btn btn-success pull-right">Submit</button>
                          </form>
                    </div>

            </div>
           

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showUserProgress(user_id){
    $('.all-user-progress').hide();
    $('#progress_details_'+user_id).show();
    $('.all-user-progress-button').removeClass('active');
    $('#sale_person_name'+user_id).addClass('active');
  }
  {{--  function confirmDailyProgress(user_id){
    //console.log();
  }  --}}
  $(document).ready(function() {
    $("#status_published").select2({
        placeholder: "-Select Product-",
        width:'270%'
    });
    //=============
    $("#catalog_product_add").select2({
      placeholder: "-Select Product-",
      width:'270%'
  });
  });
</script>
@endpush
