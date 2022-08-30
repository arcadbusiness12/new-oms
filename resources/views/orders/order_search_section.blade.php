<div class="panel panel-default">
  <div class="panel-body">
      <form name="filter_orders" id="filter_orders" method="get" action="{{$searchFormAction}}">
          {{ csrf_field() }}
          <div class="row">
              <div class="col-sm-4">
                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="order_id">Order ID</lebel>
                          <input type="text" name="order_id" id="order_id" class="form-control" autocomplete="off" value="{{ isset($old_input['order_id'])?$old_input['order_id']:'' }}">
                      </div>
                  </div>

                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="customer">Customer</lebel>
                          <input type="text" name="customer" id="customer" class="form-control" autocomplete="off" value="{{ isset($old_input['customer'])?$old_input['customer']:'' }}">
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="email">E-mail</lebel>
                          <input type="email" name="email" id="email" class="form-control email" autocomplete="off" value="{{ isset($old_input['email'])?$old_input['email']:'' }}">
                      </div>
                  </div>
              </div>
              <div class="col-sm-4">
                  <div class="form-group form-float">
                      <div class="form-line">
                          @if(strpos($_SERVER['REQUEST_URI'],'exchange_returns'))
                          <input type="hidden" name="is_return" value="1">
                          <select name="order_status_id" id="order_status_id" class="form-control show-tick" data-live-search="true">
                              <option></option>
                              <option value="2" {{ isset($old_input['order_status_id']) ?
                              $old_input['order_status_id'] == 2 ?"selected='selected'":'' :''
                              }}>AWB</option>
                              <option value="4" {{ isset($old_input['order_status_id']) ?
                              $old_input['order_status_id'] == 4 ?"selected='selected'":'' :''
                              }}>Delivered</option>
                          </select>
                          @else
                          <lebel class="form-label" for="order_status_id">Status</lebel>
                          <select name="order_status_id" id="order_status_id" class="form-control show-tick" data-live-search="true">
                            <option value="">--Select Status--</option>
                            @foreach($orderStatus as $status)
                            <option value="{{ $status['order_status_id']}}"
                                    {{ isset($old_input['order_status_id'])?
                                    $status['order_status_id'] == $old_input['order_status_id']?"selected='selected'":'' :''
                                    }}
                                    >{{$status['name']}}</option>
                            @endforeach
                        </select>
                        @endif
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="total">Total</lebel>
                          <input type="text" name="total" id="total" class="form-control" autocomplete="off" value="{{ isset($old_input['total'])?$old_input['total']:''}}">
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="telephone">Telephone</lebel>
                          <input type="text" name="telephone" id="telephone" class="form-control" autocomplete="off" value="{{isset($old_input['telephone'])?$old_input['telephone']:''}}">
                      </div>
                  </div>
              </div>
              <div class="col-sm-4">
                  <div class="form-group">
                      <div class="form-line">
                            <lebel class="form-label" for="date_from">Date From</lebel>
                          <input type="text" name="date_from" id="date_from" class="date-time-picker form-control" autocomplete="off" data-options='{
                            "timepicker":false,
                            "format":"Y-m-d"
                            }' placeholder="Date From" value="{{isset($old_input['date_from'])?$old_input['date_from']:''}}">
                      </div>
                  </div>
                  <div class="form-group">
                      <div class="form-line">
                          <lebel class="form-label" for="date_to">Date From</lebel>
                          <input type="text" name="date_to" id="date_to" class="date-time-picker form-control" data-options='{
                            "timepicker":false,
                            "format":"Y-m-d"
                            }' autocomplete="off"  placeholder="Date To" value="{{isset($old_input['date_to'])?$old_input['date_to']:''}}">
                      </div>
                  </div>
                  <div class="form-group form-float">
                      <div class="form-line">
                          <lebel class="form-label" for="sku">SKU</lebel>
                          <input type="text" name="sku" id="sku" class="form-control" autocomplete="off" value="{{ isset($old_input['sku'])?$old_input['sku']:'' }}">
                      </div>
                  </div>
              </div>
              @if( Request::route()->getName() == 'orders' )
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="form-line">
                            {{--  <lebel class="form-label" for="sku">Category</lebel>  --}}
                            <select class="form-control" id="search_by_category" name="search_by_category">
                                <option value="">--By Category--</option>
                                <option value="ws" {{ (@$old_input['search_by_category']=="ws") ? "selected" : ""   }} >Web Site</option>
                                <option value="mapp" {{ (@$old_input['search_by_category']=="mapp") ? "selected" : ""   }} >Mobile App</option>
                                <option value="android" {{ (@$old_input['search_by_category']=="android") ? "selected" : ""   }} >Android</option>
                                <option value="ios" {{ (@$old_input['search_by_category']=="ios") ? "selected" : ""   }} >IOS</option>
                                <option value="oms" {{ (@$old_input['search_by_category']=="oms") ? "selected" : ""   }}>OMS</option>
                            </select>
                        </div>
                    </div>
                </div>
              @endif
              @if( Request::route()->getName() == 'exchange_orders' )
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="form-line">
                            {{--  <lebel class="form-label" for="sku">Category</lebel>  --}}
                            <select class="form-control" id="search_by_category" name="search_by_category">
                                <option value="">--Shipping--</option>
                                <option value="free.free" {{ (@$old_input['search_by_category']=="ws") ? "selected" : ""   }} >Free Shipping</option>
                                <option value="flat.flat" {{ (@$old_input['search_by_category']=="android") ? "selected" : ""   }} >Flat Shipping</option>
                            </select>
                        </div>
                    </div>
                </div>
              @endif
              @if( Request::route()->getName() == 'orders.picking-list-awaiting' ||
              Request::route()->getName() == 'exchange_orders.picking-list-awaiting')
              <div class="col-sm-4">
                <div class="form-group">
                    <div class="form-line">
                        {{--  <lebel class="form-label" for="sku">Category</lebel>  --}}
                        <select class="form-control" id="search_by_print" name="search_by_print">
                            <option value="">--By Print--</option>
                            <option value="1" {{ (@$old_input['search_by_print']=="1") ? "selected" : ""   }} >Printed</option>
                            <option value="0" {{ (@$old_input['search_by_print']=="0") ? "selected" : ""   }}>Non Printed</option>
                        </select>
                    </div>
                </div>
              </div>
              @endif
              <div class="col-sm-4">
                <div class="form-group">
                    <div class="form-line">
                        {{--  <lebel class="form-label" for="sku">Category</lebel>  --}}
                        <select class="form-control" id="by_store" name="by_store">
                            <option value="">--By Store--</option>
                            <option value="1" {{ (@$old_input['by_store']=="1") ? "selected" : ""   }} >BA</option>
                            <option value="2" {{ (@$old_input['by_store']=="2") ? "selected" : ""   }}>DF</option>
                        </select>
                    </div>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                    <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                </div>
              </div>
          </div>
      </form>
  </div>
</div>
