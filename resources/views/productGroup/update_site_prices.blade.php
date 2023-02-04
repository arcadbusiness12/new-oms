            <style>
              table tr td{
                padding: 4px;
              }
              .panel-default .panel-heading{
                width: 100%;
                background-color: gainsboro;
              }
            </style>
            {{-- for BA price============== --}}
            @foreach ($stores as $store)
                
              <div class="col-12 col-md-12 col-sm-12  price-section" style="border: 1px solid red">
                <h5>{{$store->name}} Prices</h5>
                <input type="hidden" name="store[]" value="{{$store->id}}">
                @if( $store->products->count() > 0 )
                  @foreach($store->products as $key => $bproduct)
                    <div class="col-3 col-md-3 col-sm-3 panel panel-default col-grid mb-2 mt-2" style="padding-left: 1px;padding-right: 1px;border: 2px solid gainsboro;">
                      <div class="panel-heading text-black fw-bold"> {{ $bproduct->sku }}  Quantity: {{$bproduct->ProductsSizes->sum('available_quantity')}}</div>
                      <table>
                      <tr class="extra-field text-black">
                        <td><b> DT Price </b></td> 
                        
                        <td><input type="text" name="data_price[{{$store->id}}][{{ $bproduct->product_id }}]" value="{{ $bproduct->productDescriptions->sum('price') }}.00" size="8"></td>
                      </tr>
                      @if( $bproduct->productSpecials->count() > 0 )
                        @foreach ($bproduct->productSpecials as $sp_rows)
                            @if( $sp_rows->date_end=="0000-00-00" || $sp_rows->date_end==null)
                              @php
                                  $specail_price = $sp_rows->price;
                              @endphp
                            @else
                              @php
                                $ba_offer_price = $sp_rows->price;
                                $ba_offer_end_date = $sp_rows->date_end;
                                $ba_offer_start_date = $sp_rows->date_start;
                                if( $ba_offer_end_date > date('Y-m-d') ){
                                  $ba_offer_status = true;
                                  $ba_offer_color = "lightgreen";
                                }else{
                                  $ba_offer_status = false;
                                  $ba_offer_color = "lightpink";
                                }
                              @endphp
                            @endif
                        @endforeach
                      @endif
                        <tr class="extra-field text-black">
                          <td><b>SP Price</b></td>
                          <td><input type="text" name="sp_price[{{$store->id}}][{{ $bproduct->product_id }}]" size="8" value="{{ @$specail_price }}"></td>
                        </tr>
                        <tr class="extra-field text-black" style="background:{{ @$ba_offer_color }}">
                          <td><b>Offer</b></td>
                          <td><input type="text" name="offer_price[{{$store->id}}][{{ $bproduct->product_id }}]" size="8" value="{{ @$ba_offer_price }}" readonly></td>
                        </tr>

                        {{-- <tr class="text-black" style="background-color: mediumturquoise;">
                          <td><b>Deals Price</b></td>
                          <td><input type="text" name="ba_deals_price[{{ $bproduct->product_id }}]" size="8" value="{{ @$bproduct->deals_price }}" ></td>
                        </tr>
                        <tr class="text-black">
                          <td>
                            <b>Free Shipping</b>
                          </td>
                          <td class="text-center"><input type='checkbox' {{($bproduct->deals_free_shipping > 0) ? 'checked' : ''}} name='ba_deals_free_shipping[{{ $bproduct->product_id }}]' value='1' class='btn btn-success'></td>
                        </tr> --}}
                      </table>
                    </div>
                  @endforeach
                @else
                  <b class="btn-danger"> No Product found. </b>
                @endif
              </div>
              <br>
            @endforeach
            
          