@extends('layouts.app')

@section('content')
<style>
.form-control {
    border: 1px solid #c1c6cb;
}
.badge {
    font-weight: 900;
}


</style>
<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">


            <div class="row mb-4">
                <div class="col-md-12 col-sm-12 text-right">
                        <div class="card-header white">

                                        <div class=" ">
                                        <a href="{{route('add.geo.zone')}}"> <button id="" type="button" class="btn btn-primary active add-method">
                                            <i class="icon-plus-circle"></i>  New
                                        </button>
                                        </a>
                                        </div>

                        </div>
                </div>
                </div>
            </div>
            
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="panel-heading">
                            <div class="col-md-6 col-sm-6 col-grid">
                                Geo Zones
                            </div>
                          </div>
                          <div class="table-responsive">
                           <div id="status_changed_msg" style="display: none"></div>
                            <table class="table" width="100%" style="border: 1px solid #3f51b5">

                             <thead >

                              <tr
                              style="background-color: #3f51b5;color:white"
                              >
                                <th scope="col"><center>Geo Zone Name</center></th>
                                <th scope="col"><center>Description</center></th>
                                <th scope="col"><center>Action</center></th>

                               </tr>

                             </thead>
                             @if(count($geoZones) > 0)
                                @foreach($geoZones as $gzone)

                                    <tr>

                                        <td class="text-center">{{$gzone->name}}</td>
                                        <td class="text-center">{{@$gzone->description}}</td>
                                        {{-- <td class="text-center">
                                            @if($method->status == 1)
                                            <span class="badge badge-success r-5">Active</span>
                                            @else
                                            <span class="badge badge-danger r-5">In-Active</span>
                                            @endif
                                        </td> --}}
                                        <td class="text-center">
                                            <a href="{{route('edit.geo.zone', $gzone->id)}}" class=""><i class="icon icon-edit"></i></a>
                                             {{-- <a href="{{route('destroy.option',$list->id)}}"  onclick="return confirm('Are You Sure Want To Delete ?')" class=""><i class="icon-close2 text-danger-o text-danger"></i></a>  --}}

                                        </td>
                                    </tr>
                                @endforeach
                                @else 
                                <tr>
                                    <td colspan="4" class="text-danger text-center">
                                        No Geo Zone Available.
                                    </td>
                                </tr>
                              @endif
                             <tbody>


                     </tbody>

                    </table>

                    </div>

            </div>


                    </div>
                </div>

        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>
@endsection

@push('scripts')
<script>
    
</script>
@endpush
