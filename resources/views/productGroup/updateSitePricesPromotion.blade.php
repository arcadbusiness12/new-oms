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
            @php 
            $date_start = "";
               $date_end   = "";
               $ba_offer_price = "";
               $spId = 0;
               $kk = 0;
           @endphp
       @if( $bproduct->productSpecials->count() > 0 )
         @foreach ($bproduct->productSpecials as $k => $sprow)
           @if( $sprow->date_start != "0000-00-00" && $sprow->date_end != "0000-00-00" )
           @php
                $kk = $k;
                $spId = $sprow->id;
               if( $date_start=="" ){
                   $date_start = $sprow->date_start;
               }
               if( $date_end=="" ){
                   $date_end   = $sprow->date_end;
               }
               if( $ba_offer_price=="" ){
                   $ba_offer_price = $sprow->price;
               }
           @endphp
           @else
           @php
               $ba_specail_price = $sprow->price;
           @endphp
           @endif
         @endforeach
       @endif
            <div class="panel-heading text-black fw-bold"> {{ $bproduct->sku }}  <input type="checkbox" name="update_flag[{{$store->id}}][{{$spId}}]" class="pull-right float-end"></div>
            <table style="width:100%;">
              
            <tr class="extra-field text-black" style="background:{{ @$ba_offer_color }}">
              <td style="min-width: 74px;"><b>Offer {{$spId}}</b></td>
              <input type="hidden" name="product_id[{{$spId}}]" value="{{$bproduct->product_id}}">
              <td><input type="text" name="offer_price[{{$spId}}]" size="8" value="{{ @$ba_offer_price }}"  style="max-width: 102px;"></td>
            </tr>
            <tr class="extra-field text-black">
                <td style="min-width: 74px;"><b> DT Price </b></td> 
                
                <td><input type="text" name="data_price[{{$store->id}}][{{ $bproduct->product_id }}]" value="{{ $bproduct->productDescriptions->sum('price') }}.00" size="8" readonly style="max-width: 102px;"></td>
              </tr>
              <tr class="extra-field text-black">
                <td><b>SP Price</b></td>
                <td style="min-width: 74px;"><input type="text" name="sp_price[{{$store->id}}][{{ $bproduct->product_id }}]" size="8" value="{{ @$ba_specail_price }}" readonly style="max-width: 102px;"></td>
              </tr>
              <tr class="extra-field text-black">
                <td style="min-width: 74px;"><b> Start Date </b></td> 
                
                <td><input type="date" class="date-time-picker" name="offer_start_date[{{$spId}}]" value="{{ $date_start }}" style="max-width: 102px;"></td>
              </tr>
              <tr class="extra-field text-black">
                <td style="min-width: 74px;"><b>End Date</b></td>
                <td><input type="date" class="date-time-picker" name="offer_end_date[{{$spId}}]" size="8" value="{{ @$date_end }}" data-options='{"timepicker":false, "format":"Y-m-d"}' style=" max-width: 102px;"></td>
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
  
