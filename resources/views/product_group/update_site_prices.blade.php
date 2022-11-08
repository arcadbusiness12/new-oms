            <style>
              table tr td{
                padding: 4px;
              }
              .panel-default .panel-heading{
                width: 112%;
              }
            </style>
            {{-- for BA price============== --}}
            <div class="col-sm-12" style="border: 1px solid red">
              <h5>BusinessArcade Prices</h5>
              @if( $ba_products->count() > 0 )
                @foreach($ba_products as $key => $bproduct)
                  <div class="col-sm-2 panel panel-default">
                    <div class="panel-heading">{{ $bproduct->sku }}</div>
                    <table>
                    <tr>
                      <td>DT Price</td> 
                      <td><input type="text" name="ba_data_price[{{ $bproduct->product_id }}]" value="{{ $bproduct->price }}" size="8"></td>
                    </tr>
                    @if( $bproduct->product_special->count() > 0 )
                      @foreach ($bproduct->product_special as $sp_rows)
                          @if( $sp_rows->date_end=="0000-00-00" )
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
                      <tr>
                        <td>SP Price</td>
                        <td><input type="text" name="ba_sp_price[{{ $bproduct->product_id }}]" size="8" value="{{ @$specail_price }}"></td>
                      </tr>
                      <tr  style="background:{{ @$ba_offer_color }}">
                        <td>Offer</td>
                        <td><input type="text" name="ba_offer_price[{{ $bproduct->product_id }}]" size="8" value="{{ @$ba_offer_price }}" readonly></td>
                      </tr>
                    </table>
                  </div>
                @endforeach
              @else
                <b class="btn-danger"> No Product found. </b>
              @endif
            </div>
            <br>
            {{--  for DF price==============  --}}
            <div class="col-sm-12" style="border: 1px solid black">
              <h5>DressFair Prices</h5>
              @if( $df_products->count() > 0 )
                @foreach($df_products as $key => $dproduct)
                  <div class="col-sm-2 panel panel-default">
                    <div class="panel-heading">{{ $dproduct->sku }}</div>
                    <table>
                      <tr>
                        <td>DT Price</td> 
                        <td><input type="text" name="df_data_price[{{ $dproduct->product_id }}]" value="{{ $dproduct->price }}" size="8"></td>
                      </tr>
                    @if( $dproduct->product_special->count() > 0 )
                      @foreach ($dproduct->product_special as $sp_rows)
                          @if( $sp_rows->date_end=="0000-00-00" )
                            @php
                                $dspecail_price = $sp_rows->price;
                            @endphp
                          @else
                              @php
                                $df_offer_price = $sp_rows->price;
                                $df_offer_end_date = $sp_rows->date_end;
                                $df_offer_start_date = $sp_rows->date_start;
                                if( $df_offer_end_date > date('Y-m-d') ){
                                  $df_offer_status = true;
                                  $df_offer_color = "lightgreen";
                                }else{
                                  $df_offer_status = false;
                                  $df_offer_color = "lightpink";
                                }
                              @endphp
                          @endif
                      @endforeach
                    @endif
                    <tr>
                      <td>SP Price</td>
                      <td><input type="text" name="df_sp_price[{{ $dproduct->product_id }}]" size="8" value="{{ @$dspecail_price }}"></td>
                    </tr>
                    <tr  style="background:{{ @$df_offer_color }}">
                      <td>Offer</td>
                      <td><input type="text" name="df_offer_price[{{ $bproduct->product_id }}]" size="8" value="{{ @$df_offer_price }}" readonly></td>
                    </tr>
                    </table>
                  </div>
                @endforeach
              @else
                <b class="btn-danger"> No Product found. </b>
              @endif
            </div>