@extends('layouts.app')

@section('content')

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

                                <?php if($data->count() > 0) { ?>
                                    <thead >

                                        <tr
                                        style="background-color: #3f51b5;color:white"
                                        >
                                          <th scope="col">Activity</th>
                                          <th scope="col">Arif</th>
                                          <th >Sohail</th>
                                         </tr>
          
                                       </thead>
                                    <tbody>
                                        <?php foreach ($data as $key => $row) { ?>
                                        <tr>
                                            <td>{{ $row->activity }}</td>
                                            <td>{{ $row->user_one }}</td>
                                            <td>{{ $row->user_two }}</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    <?php }else{ ?>
                                    <tr>
                                        <td class="text-center text-danger">No data Found!</td>
                                    </tr>
                                    <?php } ?>

                    </table>

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
