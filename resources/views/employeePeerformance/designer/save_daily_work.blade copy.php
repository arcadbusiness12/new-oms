@extends('layout.theme')
@section('title', 'Home')
@section('content')
<style>
  .success-fs{
    font-size: 15px;
  }
  .green{
    color: green;
    cursor: pointer;
  }
  .red{
    color:red;
    cursor: pointer;
  }
  .bootstrap-select{
    width: 100px !important;
    height: 25px !important;
  }
  .group-name{
    width: 116%;
    margin-top: -11px;
    padding: 11px;
    margin-bottom: 4px;
  }
  .light-red{
    background: #ff00002b;
  }
  .light-orange{
    background:#ffa5003d;
  }
  .light-green{
    background:#00800021;
  }
  }
  .tab-links a.active {
    color:green;
    border:1px solid green;
    -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
  }
</style>
<section class="content">
    <div class="container-fluid">
      
        <div class="error-messages"></div>
        <div class="block-header">
            <div class="col-sm-2">
            <h2>Add Daily Work</h2>
            </div>
            
            <div class="clearfix"></div>
        </div>
        <div class="row">
          <div class="col-sm-12 tab-links">
            <!-- <a href="{{ route('df.orders') }}" target="_blank"><div class="tab-box">DressFair</div></a> -->
            @include('employee_performance.designer.tab_links')
        </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="card">
                    @if ($errors->any())
                      <div class="alert alert-warning">
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                    @endif
                    @php
                    @endphp
                    @if(Session::has('query_status'))
                      <div class="alert alert-success">
                        {{ Session::get('query_status') }}
                      </div>
                    @endif
                    @if(Session::has('promo_status'))
                      <div class="alert alert-warning">
                        {{ Session::get('promo_status') }}
                      </div>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form action="{{Request::url()}}" method="POST">
                              {{csrf_field()}}
                              <div class="table-responsive">
                                <table class="table"  style="border: 1px solid #2196f3; width:68%;">
                                  <input type="hidden" name="main_id" value="{{$id}}">
                                      <thead >
                                        <tr id="head_row" style="background-color:lightgray">
                                
                                          <th scope="col" class="sticky-col first-col" style="width:3%"><center>Post</center></th>
                                          <th scope="col" class="sticky-col second-col" style="width:4%"><center>Type </center></th>
                                          <th scope="col" class="sticky-col third-col" style="width:4%"><center>Category</center></th>
                                          @if(@$days)
                                          @foreach($days as $k => $day)
                                          <th scope="col" class="calendar-col" style="width:7% !important"><center>{{$day['display_date']}}</center></th>
                                          @endforeach
                                          @endif
                                        </tr>
                                
                                      </thead>
                                
                                      <tbody>
                                      @if(count($product_pro_posts) > 0)
                                          @php
                                            $desining_permission = false;
                                            $posting_permission = false;
                                            $login_user = session('user_id');
                                            if( $login_user == $templates->designing_person OR session('user_group_id') == 1 ){
                                              $desining_permission = true;
                                            }
                                            if( $login_user == $templates->posting_person OR session('user_group_id') == 1 ){
                                              $posting_permission = true;
                                            }
                                          @endphp

                                          @foreach(@$product_pro_posts as $key=>$product_pro_post)
                                          
                                            @php $group = ''; @endphp
                                          <tr id="" style="border-top: 1px solid gray !important;">
                                                  <td class="argn-popup-td" style="vertical-align: middle; width:3%"><center><label>{{ date('h:i a', strtotime($product_pro_post->schedule_time)) }}</label></center></td>
                                                  <td class="argn-popup-td" style="vertical-align: middle; width:4%"><center><label>{{$product_pro_post->type->name}}</label></center></td>
                                                  <td class="argn-popup-td" style="vertical-align: middle; width:4%"><center><label>{{$product_pro_post->category}}</label></center></td>
                                                 
                                                  @foreach(@$days as $k => $day)
                                                  @php
                                                  $exist = false;
                                                  @endphp
                                                  <td class="argn-popup-td posting-status" style="vertical-align: middle; width:7% !important">
                                                    <center>
                                                  @php
                                                    $scheduled_id = 0;
                                                  @endphp
                                                  @foreach(@$pro_posts as $post)
                                                    @if($product_pro_post->schedule_time == $post->time && $day['hiddn_date'] == date('Y-m-d', strtotime($post->date)) && $product_pro_post->id == $post->setting_id && $id == $post->main_setting_id)
                                                      @php
                                                       $exist = true;
                                                       $group_color_class = "light-red";
                                                       $designed = 0;
                                                       $posted = 0;
                                                       if( $post->designed ==1 && $post->posted != 1 ){
                                                        $group_color_class = "light-orange";
                                                        $designed = 1;
                                                        $posted = 0;
                                                       }elseif( $post->designed ==1 && $post->posted == 1 ){
                                                        $group_color_class = "light-green";
                                                        $designed = 1;
                                                        $posted = 1;
                                                       }
                                                       $diabale_action = "";
                                                       if( date('Y-m-d') >  $post->date ){
                                                        $diabale_action = 'disabled="disabled"';
                                                       }
                                                       $scheduled_id = $post->id;
                                                       $group_name = $post->group->name;
                                                       
                                                      @endphp
                                                    @endif
                                                  @endforeach
                                                  @if($exist)
                                                  <div class="row group-name {{ $group_color_class }} post-title{{$post->id}}">
                                                    <strong>{{$group_name}}</strong>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col-sm-6">
                                                      @if( $designed != 1 )
                                                        @if( $diabale_action == "" )
                                                          @if( $desining_permission )
                                                            <a href="{{ route('employee-performance.designer.changePostStatus',[$scheduled_id,'designed']) }}" class="btn btn-xs btn-info btn-design{{$post->id}}">Design</a>
                                                          @endif
                                                        @else
                                                          <button class="btn btn-xs btn-secondary " {{ $diabale_action  }}>Design</button>
                                                        @endif
                                                      @else
                                                        <i class="fa fa-check-circle green success-fs btn-design-icon{{$post->id}}" title="Designed"></i>
                                                      @endif
                                                    </div>
                                                    <div class="col-sm-6">
                                                      @if( $posted != 1 )
                                                       @if( $diabale_action == "" )
                                                        @if( $posting_permission )
                                                          <a href="{{ route('employee-performance.designer.changePostStatus',[$scheduled_id,'posted']) }}" class="btn btn-xs btn-success btn-post{{$post->id}}">Post</a>
                                                        @endif
                                                      @else
                                                        <button class="btn btn-xs btn-secondary" {{ $diabale_action  }}>Post</button>
                                                      @endif
                                                      @else
                                                        <i class="fa fa-check-circle green success-fs btn-post-icon{{$post->id}}" title="Posted"></i>
                                                      @endif
                                                    </div>
                                                  </div>
                                                  @else
                                                    -
                                                  @endif
                                                    </center>
                                                      
                                                  </td>
                                                  @endforeach
                                
                                          </tr>
                                          @endforeach
                                          
                                          @else
                                          <tr id="tr_" style="border-top: 1px solid gray">
                                
                                          <td class="column text-center" colspan="{{count($days )+2}}">
                                              <center><label>No post found.</label></center>
                                          </td>
                                          </tr>
                                          @endif
                                    </tbody>
                                 </table>
                              </div>
                              {{-- <button type="submit" class="btn btn-success pull-right">Submit</button> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('scripts')

{{-- <link rel="stylesheet" href="{{URL::asset('assets/plugins/select2/select2.min.css') }}">
<script src="{{URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script> --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  {{-- $(document).on('mouseover','.posting-status',function(){
    console.log("fired");
    $(this).find('span').show();
  })
  $(document).on('mouseout','.posting-status',function(){
    console.log("fired");
    $(this).find('span').hide();
  }) --}}
  function showOption(uid){
    //$('#option'+uid).show();
    console.log(uid);
  }
  function hideOption(uid){
    console.log(uid);
    //$('#option'+uid).hide();
  }
  function showUserProgress(user_id){
    console.log();
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