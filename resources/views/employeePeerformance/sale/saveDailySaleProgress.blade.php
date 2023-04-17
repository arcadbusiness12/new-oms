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
          @if(Session::has('query_success') || Session::has('query_error') )
            @if( Session::has('query_success') )
              @php
                  $alert_class = "alert-success";
                  $msg = Session::get('query_success');
              @endphp
            @else
              @php
                $alert_class = "alert-danger";
                $msg = Session::get('query_error');
              @endphp
            @endif
            <div class="alert {{ $alert_class }}">
              {{ $msg }}
            </div>
          @endif

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Add Sale Progress
                          </div>
                          <div class="">
                           <div id="status_changed_msg" style="display: none"></div>
                           <form action="{{URL::to('/performance/sale/save/daily/progress')}}" method="POST" class="form-inline">
                                {{csrf_field()}}
                                @if( session('user_id') == 28 )
                                <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-line">
                                    <div class="col-sm-2">
                                    <label>Comments Replied:</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="comments_replied">
                                            <option value="0" {{ $today_converation->comments_replied == 0 ? 'selected' : '' }} >No</option>
                                            <option value="1" {{ $today_converation->comments_replied == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                </div>
                                @endif

                                <div class="row" style="min-width: 101%;background-color: #3f51b5;color:white;padding: 7px 0px 7px 0px;margin-left: 0px;">
                                    <div class="form-line">
                                    <div class="col-2 col-grid">
                                    <label class="top-lable">Duties</label>
                                    </div>
                                    <div class="col-2 col-grid">
                                        <label class="top-lable">Points</label>
                                    </div>
                                    <div class="col-2 col-grid" >
                                        <label class="top-lable">Achieved</label>
                                    </div>
                                    <div class="col-2 col-grid" >
                                        <label class="top-lable">Target</label>
                                    </div>

                                    <div class="col-4 col-grid" >
                                        <label class="top-lable">Achieved Target</label>
                                    </div>
                                    </div>
                                </div>

                                @foreach($user_duties->activities as $k => $duty)
                                <div class="row mb-4" style="min-width: 100%;">
                                    <div class="form-line status_published">
                                    <div class="col-2 col-grid">
                                        <label>{{$duty->name}}:</label>
                                        <input type="hidden" name="activity_id[]" value="{{$duty->activity_id}}">
                                        <input type="hidden" name="id[]" value="{{$duty->performance_id}}">
                                    </div>
                                    <input type="hidden" class="form-control" name="target[]" id="target" value="{{$duty->quantity}}" autocomplete="off">
                                    <input type="hidden" class="form-control" id="per_quantity_point_{{$k}}" value="{{$duty->per_quantity_point}}" autocomplete="off">
                                    <div class="col-2 col-grid">
                                    <input type="text" class="form-control point-target-input" readonly id="complete_quantity" value="{{$duty->point}}" autocomplete="off">
                                    </div>
                                    <input type="hidden" class="form-control achieved-points-{{$k}}" name="achieved_points[]" id="" value="{{(isset($duty->achieved_point)) ? $duty->achieved_point : 0}}" autocomplete="off">
                                    
                                    <div class="col-2 col-grid">
                                    <input type="text" class="form-control point-target-input" readonly id="achieved-points-{{$k}}" value="{{(isset($duty->achieved_point)) ? $duty->achieved_point : 0}}" autocomplete="off">
                                    </div>
                                    <div class="col-2 col-grid">
                                    <input type="text" class="form-control point-target-input complete_quantity_{{$k}}" readonly id="complete_quantity" value="{{$duty->quantity}}" autocomplete="off">
                                    </div>

                                    @if($duty->activity_id == 9)
                                    <div class="col-4 col-grid">
                                    <input type="hidden" class="form-control complete_quantity_{{$k}}" name="complete_quantity[]" placeholder="Achieved" id="complete_quantity" value="0" autocomplete="off">
                                        <select name="status_published[{{ $duty->activity_id }}][]" id="status_published" class="status_published_{{$k}}" multiple="multiple" onchange="calculateAchievedPoints(this.value, '{{$k}}', '{{$duty->activity_id}}', '{{$duty->name}}', '{{$duty->quantity}}', 'status_published')">
                                        @foreach($product_groups as $pgroups)
                                        @if(isset($duty->achieved) && is_array($duty->achieved))
                                            @if(in_array($pgroups->id,(isset($duty->achieved)) ? $duty->achieved : []) )
                                            @php
                                                $selected = "selected";
                                            @endphp
                                            @else
                                            @php
                                                $selected = "";
                                            @endphp
                                            @endif
                                            @endif
                                            <option value="{{ $pgroups->id }}" {{ @$selected }}>{{ $pgroups->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    @elseif($duty->activity_id == 15)
                                    <div class="col-4 col-grid">
                                    <input type="hidden" class="form-control" name="complete_quantity[]" placeholder="Achieved" id="complete_quantity" value="0" autocomplete="off">
                                    <select name="catalog_product_add[{{ $duty->activity_id }}][]" id="catalog_product_add" class="catalog_product_{{$k}}" multiple="multiple" onchange="calculateAchievedPoints(this.value, '{{$k}}', '{{$duty->activity_id}}', '{{$duty->name}}', '{{$duty->quantity}}', 'catalog_product')">
                                        @foreach($product_groups as $pgroups)
                                        @if(isset($duty->achieved) && is_array($duty->achieved))
                                            @if(in_array($pgroups->id,(isset($duty->achieved)) ? $duty->achieved : []) )
                                            @php
                                                $selected = "selected";
                                            @endphp
                                            @else
                                            @php
                                                $selected = "";
                                            @endphp
                                            @endif
                                            @endif
                                            <option value="{{ $pgroups->id }}" {{ @$selected }}>{{ $pgroups->name }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    </div>
                                    @else 
                                    @if($duty->activity_id == 2 || $duty->activity_id == 64 || $duty->activity_id == 65)
                                        @php 
                                        $orders = ($duty->activity_id == 2) ? $totalOrders : $duty->achieved;
                                        $readonly = 'readonly';
                                        @endphp
                                    @else
                                    @php 
                                        $orders = $duty->achieved;
                                        $readonly = '';
                                        @endphp
                                    @endif
                                    <div class="col-4 col-grid">
                                    <input type="text" class="form-control complete_quantity complete_quantity_{{$k}}" {{$readonly}} name="complete_quantity[]" onchange="calculateAchievedPoints(this.value, '{{$k}}', '{{$duty->activity_id}}', '{{$duty->name}}', '{{$duty->quantity}}')" placeholder="Achieved" id="complete_quantity" value="{{($orders) ? $orders : 0}}" autocomplete="off">
                                    </div>
                                    @endif
                                    </div>
                                </div>
                                <br>
                                @endforeach
                                <br>
                                <div class="row mb-5 mr-5">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                    
                                </div>
                                
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
  setTimeout(() => {
    $('.alert').css('display', 'none');
  }, 5000);
  });

  function calculateAchievedPoints(value, index, activity, activity_name, target, input = null) {
    var flag = true;
    if(input) {
      if(input == 'status_published') {
        var achieved = $('.status_published_'+index+ ' :selected').length;
        if(parseInt(achieved) > parseInt(target)) {
          flag = checkAchievedQuantities(achieved, activity, activity_name, target);
          if(!flag) {
            $("#status_published").select2({
                placeholder: "-Select Product-",
                width:'270%',
                maximumSelectionLength: target
            });
          }
        }
        
      }else {
        var achieved = $('.catalog_product_'+index+ ' :selected').length;
        if(parseInt(achieved) > parseInt(target)) {
          flag = checkAchievedQuantities(achieved, activity, activity_name, target);
          if(!flag) {
            $("#catalog_product_add").select2({
                placeholder: "-Select Product-",
                width:'270%',
                maximumSelectionLength: target
            });
          }
        }
      }
    }else {
      var achieved = value;
      console.log('achieved = '+parseInt(achieved)+' target = '+target);
      if(parseInt(achieved) > parseInt(target)) {
        flag = checkAchievedQuantities(achieved, activity, activity_name, target);
      }
    }
    var per_qu_point = $('#per_quantity_point_'+ index).val();
    if(flag) {
      var get_points = achieved*per_qu_point;
      $('#achieved-points-'+index).val(get_points.toFixed(0));
      $('.achieved-points-'+index).val(get_points.toFixed(0));
      console.log('get_points ='+get_points);
    }else {
      var get_points = target*per_qu_point;
      $('#achieved-points-'+index).val(get_points.toFixed(0));
      $('.achieved-points-'+index).val(get_points.toFixed(0));
      $('.complete_quantity_'+index).val(target);
      console.log('else get_points ='+get_points);
    }
    
  }

  function checkAchievedQuantities(achieved, activity, activity_name, target) {
    console.log("ok"+activity);
    if(activity != 2 && activity != 10) {
      console.log("false "+activity);
     return flag = false;
    }else{
      console.log("true "+activity);
     return flag = true;
    }
  }
</script>
@endpush
