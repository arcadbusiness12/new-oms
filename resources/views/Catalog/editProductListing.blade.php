@extends('layouts.app')

@section('content')

<div class="container-fluid relative animatedParent animateOnce my-3">
    <div class="row row-eq-height my-3 mt-3">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card no-b">
                        <div class="card-header white">
                            <div class="d-flex justify-content-between">
                                {{-- <div class="align-self-center">
                                    <strong>Awesome Title</strong>
                                </div> --}}
                                <div class="align-self-end float-right">
                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active show" id="w5--tab1" data-toggle="tab" href="#w5-general" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">General</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-tab2" role="tab" aria-controls="tab2" aria-selected="false">Tab 2</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="w5--tab3" data-toggle="tab" href="#w5-tab3" role="tab" aria-controls="tab3" aria-selected="false">Tab 3</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body no-p">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="w5-general" role="tabpanel" aria-labelledby="w5-general">
                                    <div class="card no-b">
                                        <div class="card-header white">
                                            <div class="d-flex justify-content-between">
                                                {{-- <div class="align-self-center">
                                                    <strong>Awesome Title</strong>
                                                </div> --}}
                                                <div class="align-self-end float-right">
                                                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                                        @foreach($stores as $k => $store)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{($k == 0) ? 'active' : ''}} show" id="w5--tab1" data-toggle="tab" href="#w5-{{$store->name}}" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">
                                                                {{$store->name}}
                                                            </a>
                                                        </li>
                                                        @endforeach
                                                        {{-- <li class="nav-item">
                                                            <a class="nav-link" id="w5--tab2" data-toggle="tab" href="#w5-store1" role="tab" aria-controls="tab2" aria-selected="false">Store 2</a>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body no-p">
                                            <div class="tab-content">
                                                @foreach($stores as $k => $store)
                                                <div class="tab-pane fade {{($k == 0) ? 'active' : ''}} show" id="w5-{{$store->name}}" role="tabpanel" aria-labelledby="w5-{{$store->name}}">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover earning-box">
                    
                                                            <tbody>
                                                            <tr class="no-b">
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u1.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon {{$store->name}}</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u2.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u3.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u4.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u5.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="w-10"><span class="round">
                                                            <img src="assets/img/dummy/u6.png" alt="user"></span>
                                                                </td>
                                                                <td>
                                                                    <h6>Sara Kamzoon</h6>
                                                                    <small class="text-muted">Marketing Manager</small>
                                                                </td>
                                                                <td>25</td>
                                                                <td>$250</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                             @endforeach
                                            </div>
                    
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade text-center p-5" id="w5-tab2" role="tabpanel" aria-labelledby="w5-tab2">
                                    <h4 class="card-title">Tab 2</h4>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                                <div class="tab-pane fade text-center p-5" id="w5-tab3" role="tabpanel" aria-labelledby="w5-tab3">
                                    <h4 class="card-title">Tab 3</h4>
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
    
                        </div>
                    </div>

            
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>

<div class="toast-action" data-title="Hey, Bro!" data-message="Paper Panel has toast as well." data-type="success" data-position-class="toast-top-right"></div>

 <!-- product location modal start -->
 <div class="modal fade porduct_location_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Add & Edit options</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="porduct_location_content">
          <div class="text-center" id="loader">
            
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
  <!-- product location modal end -->
@endsection

@push('scripts')
<script>

</script>
@endpush