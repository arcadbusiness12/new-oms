@extends('layouts.app')

@section('content')
<style>
  .tab-links a.active {
    color:green;
    border:2px solid green;
    -webkit-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    -moz-box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
    box-shadow: 2px 2px 8px 2px rgba(165,165,165,1);
  }
  .tab-links a {
    text-decoration: none;
    border: 1px solid gray;
    padding: 5px 10px;
    text-transform: uppercase;
    text-align: center;
    font-size: 13px;
    float: left;
    margin-right: 5px;
}
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
    background: #ff00002b !important;
  }
  .light-orange{
    background:#ffa5003d !important;
  }
  .light-green{
    background:#00800021 !important;
  }
  .page-name {
    font-size: 12px;
  }
  .tab-links .active {
    box-shadow: 0px 0px 4px 2px;
    font-size: 13px !important;
  }

</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12">
                  
                  <div class="card no-b">
                    <div class="card no-b form-box">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        <div class="card-header white">
                            <div class="panel-body">
                              <div class="col-sm-12 tab-links">
                                @include('employeePeerformance.designer.tab_links')
                              </div>
                            </div>
                        </div>
                </div>
                  </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                      <form action="{{Request::url()}}" method="POST">
                        {{csrf_field()}}
                        <div class="table-responsive">
                          <table class="table"  style="border: 1px solid #2196f3; width:100%;">
                            <input type="hidden" name="main_id" value="{{$id}}">
                                <thead >
                                  <tr id="head_row" style="background-color: #3f51b5;color:white">
                          
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
                                      $posting_permission = true;
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
                                            <td class="argn-popup-td" style="vertical-align: top; width:3%;    border-right: 1px solid darkgray;"><center><label style="font-weight: 700;">{{ date('h:i a', strtotime($product_pro_post->schedule_time)) }}</label></center></td>
                                            <td class="argn-popup-td" style="vertical-align: top; width:4%;    border-right: 1px solid darkgray;"><center><label style="font-weight: 700;">{{$product_pro_post->type->name}}</label></center></td>
                                            <td class="argn-popup-td" style="vertical-align: top; width:4%;    border-right: 1px solid darkgray;"><center><label style="font-weight: 700;">{{$product_pro_post->category}}</label></center></td>
                                           
                                            @foreach(@$days as $k => $day)
                                            @php
                                            $exist = false;
                                            @endphp
                                            <td class="argn-popup-td posting-status" style="vertical-align: top; width:7% !important;    border-right: 1px solid darkgray;">
                                              <center>
                                            @php
                                              $scheduled_id = 0;
                                            @endphp
                                            @foreach(@$pro_posts as $post)
                                              @if($product_pro_post->schedule_time == $post->time && $day['hiddn_date'] == date('Y-m-d', strtotime($post->date)) && $product_pro_post->id == $post->setting_id && $id == $post->main_setting_id)
                                                @php
                                                 $exist = true;
                                                   $multi_post = [];
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
                                                 $product_type_id = $post->product_type_id;
                                                 $diabale_action = "";
                                                //  if( date('Y-m-d') >  $post->date ){
                                                //   $diabale_action = 'disabled="disabled"';
                                                //  }
                                                $scheduled_id = $post->id;
                                                if(count($post->promo_cate_posts) > 0) {
                                                  foreach ($post->promo_cate_posts as $key => $value) {
                                                    $m_post = array_push($multi_post, $value->group_code);
                                                    //  echo $value;
                                                  }
                                                  //  print_r($multi_post); die;
                                                  $group_name = implode(',',$multi_post);
                                                }else {
                                                  $group_name = (isset($post->group->name) && $post->group->name) ? $post->group->name : '';
                                                }
                                                 $posted_pages = $post->total_pages ? explode(",", $post->total_pages) : [];
                                                @endphp
                                              @endif
                                            @endforeach
                                            @if($exist)
                                            <div class="row group-name {{ $group_color_class }} post-title{{$scheduled_id}}">
                                              <strong>{{$group_name}}</strong>
                                            </div>
                                            <div class="row">
                                              <div class="col-sm-12 btn-designed-icon{{$scheduled_id}}-{{$key}}">
                                                @if( $designed != 1 && $product_type_id != 10 )
                                                  @if( $diabale_action == "" )
                                                    @if( $desining_permission )
                                                      <a href="javascript:;" onclick="postOrDesign('{{$scheduled_id}}', 'designed', '{{$key}}')" class="btn btn-xs btn-info btn-designed-{{$key}} btn-designed{{$scheduled_id}}-{{$key}}">Design</a>
                                                    @endif
                                                  @else
                                                    <button class="btn btn-xs btn-secondary " {{ $diabale_action  }}>Design</button>
                                                  @endif
                                                @else
                                                  <i class="fa fa-check-circle green success-fs " title="Designed"></i>
                                                @endif
                                              </div>
                                            </div>
                                            <div class="row">
                                              <div class="col-sm-12">
                                                @if( $posted != 1)
                                                 @if( $diabale_action == "" )
                                                  @if( $posting_permission )
                                                  @if($designed != 1 && $product_type_id != 10)
                                                  @php $display = 'none'; @endphp
                                                  @else
                                                  @php $display = 'inline-block'; @endphp
                                                  @endif
                                                  @if(count($templates->pages) > 0)
                                                  {{-- @foreach($templates->pages as $k => $page) --}}
                                                  @foreach($templates->postPages as $k => $page)
                                                  <div class="col-sm-12 btn-posted-icon">
                                                    @if($page->posting_person == session('user_id'))
                                                    <span class="page-name"><strong>{{$page->page_name}}</strong> </span>
                                                    @endif
                                                    <div class="btn-posted-icon{{$scheduled_id}}-{{$k}}">
                                                      @if($page->posting_person == session('user_id'))
                                                        @if(in_array($page->page_name, $posted_pages))
                                                        <i class="fa fa-check-circle green success-fs " title="posted"></i>
                                                        @else 
                                                        <a href="javascript:;" style="display: {{$display}} ;" onclick="postOrDesign('{{$scheduled_id}}', 'posted', '{{$k}}', '{{$page->page_name}}')" class="btn btn-xs btn-success btn-posted-{{$k}} btn-posted{{$scheduled_id}}-{{$k}} btn-post-icon{{$scheduled_id}} ">Post</a> 
                                                        @endif
                                                      @endif
                                                  </div>
                                                  </div>
                                                  @endforeach
                                                  {{-- For old single post --}}
                                                  @else 
                                                  <div class="col-sm-12 btn-posted-icon{{$scheduled_id}}-{{$key}}">
                                                    <a href="javascript:;" style="display: {{$display}} ;" onclick="postOrDesign('{{$scheduled_id}}', 'posted', '{{$key}}')" class="btn btn-xs btn-success btn-posted-{{$key}} btn-posted{{$scheduled_id}}-{{$key}} btn-post-icon{{$scheduled_id}}">Post</a> 
                                                  
                                                  </div>
                                                  @endif
                                                  @endif
                                                @else
                                                  <button class="btn btn-xs btn-secondary" {{ $diabale_action  }}>Post </button>
                                                @endif
                                                @else
                                                  {{-- posted post --}}
                                                  @if(count($templates->pages) > 0)
                                                    @foreach($templates->pages as $k => $page)
                                                    <div class="col-sm-12 btn-posted-icon">
                                                      <span class="page-name"><strong>{{$page}}</strong> </span>
                                                      <div class="btn-posted-icon{{$scheduled_id}}-{{$k}}">
                                                        <i class="fa fa-check-circle green success-fs " title="posted"></i>
                                                    </div>
                                                    </div>
                                                    @endforeach
                                                    {{-- For old single post --}}
                                                    @else 
                                                    <div class="col-sm-12 btn-posted-icon{{$scheduled_id}}-{{$key}}">
                                                      <i class="fa fa-check-circle green success-fs btn-posted-icon{{$scheduled_id}}" title="Posted">P</i>
                                                    
                                                    </div>
                                                    @endif

                                                  
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
</div>

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

 function postOrDesign(id, action, index, page = null) {
    var middelText = '';
    var btn = '';
    $('.btn-'+action+id+'-'+index).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
    $('.btn-'+action+id+'-'+index).prop('disabled', true);
    $('.btn-'+action+'-'+index).prop('disabled', true);
    
    if(page) {
      var url = "{{url('/performance/employee-performance/designer/change-post-status')}}/"+ id +"/"+ action + "/" + page;
    }else {
      var url = "{{url('/performance/employee-performance/designer/change-post-status')}}/"+ id +"/"+ action;
    }
    if(id && action) {
      $.ajax({
        url: url,
        type: "GET",
        cache: false,
        success: function(resp) {
          if(resp.status) {
            if(action == 'designed') {
              bgColor = 'light-orange';
            }else {
              if(resp.remain_pages == 1) {
                bgColor = 'light-green';
              }else {
                bgColor = 'light-orange';
              }
              
            }
            $('.alert-success').css('display', 'block');
            $('.alert-success').text(resp.mesg);
            $('.post-title'+id).addClass(bgColor);
            // $('.btn-posted'+id+index).css('display', 'inline-block');
            $('.btn-post-icon'+id).css('display', 'inline-block');
            $('.btn-'+action+id+'-'+index).remove();
            $('.btn-'+action+'-icon'+id+'-'+index).html('<i class="fa fa-check-circle green success-fs " title="'+action+'"></i>');
            $('.btn-'+action+'-'+index).prop('disabled', false);

            setTimeout(() => {
              $('.alert-success').css('display', 'none');
              $('.alert-success').text('');
            }, 4000)
          }else {
            $('.alert-warning').css('display', 'block');
            $('.alert-warning').text('Somethings went wrong please try again.');
            setTimeout(() => {
              $('.alert-warning').css('display', 'none');
              $('.alert-warning').text('');
            }, 4000)
          }
          
        }
      });
    }
  }
</script>
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
@endpush
