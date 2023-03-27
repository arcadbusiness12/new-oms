@extends('layouts.app')

@section('content')
<style>
    .rejected{
            background: #f3bfbf;
            color: #c70a0a;
            font-weight: 500;
            padding: 4px;
            min-width: 40px;
            border: 1px solid #c70a0a;
          }
          .approved{
            background:#bdd8bd;
            color:green;
            font-weight: 500;
            padding: 4px;
            min-width: 40px;
            border: 1px solid green;
          }
          .icon-green{
            color: green;
          }
          .icon-red{
            color:#e83f3f;
          }
          tbody tr td{
            border: 1px solid #d9d9d9;
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
                            <form name="filter_reports" id="filter_reports" method="get" action="{{ URL::to('/performance/stock') }}">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                {{-- <label class="form-label" for="user_id">User</label> --}}
                                                <select name="user_id" id="user_id" class="form-control custom-select show-tick" data-live-search="true">
                                                    <option>Select User</option>
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
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" name="date_from" id="date_from" class="datepicker form-control" autocomplete="off" data-dtp="dtp_igs2I" placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
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

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            Attributes
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                           <table class="table" width="100%" style="border: 1px solid #3f51b5">
                            <?php if(count($data) > 0) { 
                                //$approved = '<i class="fa fa-check" style="color: #13a813; font-size:15px"></i>';
                                $approved = '<i class="fa fa-check-circle icon-green success-fs" title="Confirmed"></i>';
                                //$rejected = '<i class="fa fa-times" style="color: #cf1919; font-size:15px"></i>';
                                $rejected = '<i class="fa fa-times-circle icon-red success-fs" title="cancel"></i>';

                              ?>
                            <thead class="blue-table-header">
                                <tr style="background-color: #3f51b5;color:white"
                                        >
                                <th style="width: 6%;">Date</th>
                                @if( session('user_group_id') == 1 || array_key_exists('employee-performance/sale', json_decode(session('access'),true)) )
                                <th>Sale Person</th>
                                @endif
                                <!-- <th><center>Chat open</center></th>
                                <th><center>Chat close</center></th> -->
                                @foreach($activities as $duty)
                                <th><center>{{$duty->name}}</center></th>
                                @endforeach
                                <th><center>Start Time</center></th>
                                <th><center>End Time</center></th>
                                <th><center>Total Time</center></th>
                                <th><center>Break Time</center></th>
                                <!-- <th><center>Broadcast Sent</center></th>
                                <th><center>Status</center></th>
                                <th><center>Catalog</center></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $key => $row) { 
                                    $start = '';
                                     $end = '';
                                     $total = '';
                                     $break = '';
                                    foreach($user_daily_workH as $hour) {
                                     
                                      if(@$row[0]['created_at'] == $hour['date'] && @$row[0]['user_id'] == $hour['user_id']) {
                                          $start = $hour['start_time'] ? date('H:i:s a',strtotime($hour['start_time'])) : ''; 
                                          $end = $hour['end_time'] ? date('H:i:s a',strtotime($hour['end_time'])) : '';
                                          if($hour['total_worked']) {
                                            $total = explode(':', $hour['total_worked']);
                                            $total = $total[0]. ' Hr. '. $total[1]. ' M. '. $total[2]. ' S.';
                                           }else {
                                            $total = '';
                                           }
                                        //   $total = $hour['total_worked']; 
                                          if($hour['break_interval']) {
                                            $break = explode(':', $hour['break_interval']);
                                            $break = $break[0]. ' Hr. '. $break[1]. ' M. '. $break[2]. ' S.';
                                          }else {
                                            $break = '';
                                          }
                                        //   $break = $hour['break_interval'];
                                        }
                                    }
                                    ?>
                                <tr style="border:1px solid lightgray">
                                <td>{{ @$row[0]['created_at'] }}</td>
                                    @if( session('user_group_id') == 1 || array_key_exists('employee-performance/sale', json_decode(session('access'),true)) )
                                    <td>{{ @$row[0]['sale_person']['firstname']." ".@$row[0]['sale_person']['lastname'] }}</td> 
                                    @endif
                                @foreach($activities as $duty)
                                    <td align="center">
                                  @foreach($row as $r)
                                    @if($duty->id == $r['duty_list_id'])
                                    @if(count($r['performance_details']) > 0)
                                      @forelse($r['performance_details'] as $key => $product)
                                        @if($product['type'] == 1)
                                         <label class="p-label {{  $product['confirm'] == 1 ? 'approved' : 'rejected' }}">{{ $product['product_group_name'] }}</label>
                                        @endif
                                      @empty
                                        No data found
                                      @endforelse
                                      @forelse($r['performance_details'] as $key => $product)
                                        @if($product['type'] == 2)
                                         <label class="p-label {{  $product['confirm'] == 1 ? 'approved' : 'rejected' }}">{{ $product['product_group_name'] }}</label>
                                        @endif
                                      @empty
                                        No data found
                                      @endforelse
                                      @else
                                      {{ $r['achieved'] }}  @php echo $r['confirm'] == 1 ? $approved : $rejected @endphp
                                    
                                      @endif
                                      @endif
                                        @endforeach
                                    </td>

                                    
                                    
                                    @endforeach
                                    
                                    <td>
                                        {{ $start }}
                                        
                                    </td> 
                                    <td>
                                        {{ $end }}
                                    </td> 
                                    <td>
                                        {{ $total }}
                                        
                                    </td> 
                                    <td>
                                        {{ $break }}
                                    </td> 
                                </tr>
                                <?php } ?>
                            </tbody>
                            <?php }else{ ?>
                            <tr>
                                <td class="text-center text-danger">No data Found!</td>
                            </tr>
                            <?php } ?>
                        </table>
                        <div class="">
                            {{ $data->appends(@$old_input)->render() }}
                            </div>
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
</script>
@endpush
