@extends('layout.theme')
@section('title', 'Home')
@section('content') 
<section class="content">
    <div class="container-fluid">
        <form action="<?php echo URL::to('/settings') ?>" method="post" name="form-setting">
            {{ csrf_field() }}
            <div class="row clearfix">
                @if(Session::has('setting-error'))
                <p class="alert alert-danger">{{ Session::get('setting-error') }}</p>
                @endif
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    {{--  <div class="panel panel-default">
                        <div class="panel-heading">
                            Public Holidays list
                        </div>
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label" for="out_of_stock">Out Of Stock</label>
                                        <input type="number" name="stock_level[out_of_stock]" value="" id="out_of_stock" class="form-control" autocomplete="off" required />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label" for="low_stock">Low Stock</label>
                                        <input type="number" name="stock_level[low_stock]" value="" id="low_stock" class="form-control" autocomplete="off" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Public Holidays list
                            <div class="pull-right">
                              <a href="javascript:void()" class="btn btn-success btn-xs" data-toggle="modal" data-target="#new_setting_popup"><strong>Add</strong></a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-borderd table-hover" style="width:50%">
                              <thead>
                                <th>Title</th>
                                <th>Model</th>
                                <th>Action</th>
                              </thead>
                              <tbody>
                                @forelse ($data as $row)
                                  <tr>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->model->firstname }}</td>
                                    <td><a href="{{ url('/employee-performance/photography/edit-settings/') }}/{{$row->id}}"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Edit" onclick="checkSettings(1, '2')" data-toggle="modal" data-target=".setting_view_modal"></i></a></td>
                                  </tr>
                                @empty
                                  <tr>No data found.</tr>  
                                @endforelse
                              </tbody>
                            </table>
                            <div class="pull-right">
                                {{--  {{ $data->render() }}  --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@include('employee_performance.photography.popup_new_setting')
{{--  @include('employee_performance.photography.popup_edit_setting')  --}}
@endsection
@push('scripts')
<script>
function edit(id){
  $.ajax({
      url: "{{url('/employee-performance/photography/edit-settings/')}}/"+id,
      type: "GET",
      cache: false,
      success: function(respo) {
        //alert(respo);
        $('.ajax_content').html(respo);
      }
    });
  }
</script>
<link href="{{URL::asset('assets/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet" />
<script defer="defer" src="{{URL::asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script>
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>  --}}
{{--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">

    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            debug: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />loadingggg',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });
    });
</script>  --}}

@endpush