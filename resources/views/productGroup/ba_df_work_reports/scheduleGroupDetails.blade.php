<table class="table" width="100%">
  <caption class="caption-text"></caption>
                      <tbody >
                        @if(@$groupProducts)
                        @if(count(@$groupProducts->products) > 0)
                        <tr id="tr_{{$groupProducts->id}}" style="border-top: 1px solid gray">

                       
                          <td class="col-sm-2 text-center">
                            <table class="table table-hover" style="margin-bottom: 0px;">
                              <thead style="background-color: #eee;">
                                <th class="tab-th"><center><label>{{$groupProducts->name}}</label></center></th>
                                
                              </thead>
                             
                            </table> 
                            <img src="{{URL::asset('uploads/inventory_products/'.$groupProducts['products'][0]->image)}}" class="img-responsive img-thumbnail group-pro" height="120"/> 
                            <h5 class="type-name">{{$groupProducts->producType['name']}} </h5>
                            
                        </td>

                          <td class="column col-sm-6" style="vertical-align: top;">
                              <table class="" style="display: inline-block;">
                                <thead style="background-color: #eee;">
                                  <th><center><label>{{ ($groupProducts['products'][0]->omsOptions) ? $groupProducts['products'][0]->omsOptions->option_name : "Color"}}</label></center></th>
                                  
                                </thead>
                                @foreach($groupProducts['products'][0]->ProductsSizes as $key=>$val)
                                <tr>
                                  <td><center>{{ $val->omsOptionDetails->value }}</center></td>
                                  
                                </tr>
                                @endforeach
                                <tr  style="background-color: #eee">
                                <td><center><strong>Total</strong></center></td>
                                </tr>
                                
                              </table>
                          @foreach($groupProducts['products'] as $key=>$productt)
                            
                              <table class="" style="display: inline-block;">
                                <thead style="background-color: #eee;">
                                  <th><center><label>{{$productt->sku}}</label></center></th>
                                </thead>
                                @php 
                                  $sum=0; 
                                  @$sum_available_quantity = 0;
                                @endphp
                                
                                @foreach($productt->ProductsSizes as $key=>$val)
                                @php $bg = ''; $color = ''; @endphp
                                @if(isset($groupProducts->action) && $val->available_quantity == 0)
                                @php $bg = 'red'; $color = 'white'; @endphp
                                @endif
                                <tr>
                                  <td style="
                                  font-size: 13px;
                                  background-color: {{$bg}};
                                  color: {{$color}};
                              "><center>{{$val->available_quantity}}</center></td>
                                  
                                </tr>
                                @php 
                                @$sum_available_quantity += $val->available_quantity;
                                @endphp
                                @endforeach
                                <tr style="background-color: #eee;">
                                  <!-- <td><center><strong>Total</strong></center></td> -->
                                  <td><center><strong>{{ $sum_available_quantity }}</strong></center></td>
                                </tr>
                              </table>
                          @endforeach
                          </td>
                </tr>
                <tr>
                  @if(!isset($groupProducts->action))
                    <td colspan="3">
                      <div class="table-responsive promo-table" id="history-tbl">
                        
                      @if(count($groupProducts->histories) > 0) 
                          <table class="table table-bordered table-hover">
                        
                              <thead style="background-color: #eee;">
                                  <tr>
                                      <th class="text-center">Store</th>
                                      <input type="hidden" name="store" value="" id="store_{{$groupProducts->id}}">
                                      <th class="text-center">Date/Time</th>
                                      @foreach($socials as $social)
                                      <th class="text-center">{{$social->name}}</th>
                                      @endforeach
                                  </tr>
                              </thead>
                              <tbody class="history">
                                  @foreach($groupProducts->histories as $history)
                                    <tr>

                                        <td class="text-center td-vertical">
                                          <p>{{$history['store']['name']}}</p>
                                        </td>
                                        <td class="text-center tbl-top-td">
                                          <p>{{date('Y-F-d', strtotime($history['date']))}}</p>
                                        </td>
                                        @foreach($socials as $social)
                                        <td class="text-center td-vertical"><input type="checkbox" disabled <?php if(in_array($social->id, explode(',', $history['socials'])) ) { echo 'checked'; }else{ echo '';} ?>></td>
                                        @endforeach
                                    </tr>
                                  @endforeach
                              </tbody>
                          </table>
                          
                          @else
                          <h5><center>No History found..</center></h5>
                          @endif
                      </div> 
                      @endif
                    </td> 
                </tr>
                @endif

                @endif

                </tbody>

</table>