<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AWB </title>

</head>
<body>
  @if($orders->count())
  @php
    $logo_width = 45;
  @endphp
  @foreach($orders as $order)
  <div class="container">
    <table style="width:100%;">
      <tr>
        @php
        $company_logo = '';
        $company_name = '';
        if($order_tracking->count() > 0)
        {
          foreach($order_tracking as $data):
            if($order->order_id == $data['order_id'])
            {
              foreach($shipping_providers as $provider)
              {
                if($data['shipping_provider_id'] == $provider['shipping_provider_id'])
                  $company_logo =  $provider['company_logo'];
                $company_name = $provider['name'];
                if( $provider['logo_width'] > 0  ){
                  $logo_width = $provider['logo_width'];
                }
              }
            }
          endforeach;
        }
        @endphp
        <td>
          @if($company_logo)
            <img class='logo' src="{{ URL::to($company_logo) }}" style="width:{{ $logo_width }}px" />
          @else
            <h2>{{ $company_name }}</h2>
          @endif
        </td>
        <td>
          @php
          $awb = '' ;
          $sortingCode = '';
          if($order_tracking->count() > 0)
          {
            foreach($order_tracking as $data):
              if($order->order_id == $data['order_id'])
              {
                $awb =  $data['airway_bill_number'];
                $sortingCode =  $data['sortingCode'];
              }
            endforeach;
          }
          @endphp
          <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="{{$awb}}"
          jsbarcode-textmargin="0" jsbarcode-height="50"  ></svg>
        </td>
      </tr>
      @if( $data['shipping_provider_id'] == 18 )
        <tr>
          <td colspan="2" v-align="middle" style="text-align: center; font-size: 30px; padding:5px">{{ $sortingCode }}</td>
        </tr>
      @endif
    </table>
    <table style="width:100%;" class="pure-table pure-table-bordered">
      <tr>
        <td>
          <strong> From (Sender) </strong>
          BusinessArcade.com<br />
          DUBAI, UAE<br />
          0565634477  <br />
          <a href="mailto:info@businessarcade.com">info@businessarcade.com</a>
        </td>
        <td style="padding:0px">
          @if($company_name != 'GetGive' && $company_name != 'ShafiExpress' && $company_name != 'NiazExpress')
          <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-height="20" jsbarcode-textmargin="0"
          jsbarcode-value="{{ $order->order_id }}"></svg>
          @else
          {{ $order->order_id }}-2
          @endif
        </td>
      </tr>

    </table>

    <table style="width:100%;" class="pure-table pure-table-bordered">
      <tr>
        <td>
          <strong> To (Receiver) </strong> <br />
          {{ $order->shipping_firstname }} {{ $order->shipping_lastname }}<br />
          {{ $order->shipping_city_area }}, {{$order->shipping_address_1 }} {{  ($order->shipping_address_2) ?  ",".$order->shipping_address_2 : ""}} <br />
          {{ $order->shipping_city }} <br />
          <strong>Mobile:</strong> {{$order->mobile}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {!! ($order->alternate_number != "") ? '<strong>Alternate:</strong> '.$order->alternate_number : "" !!}
        </td>
      </tr>
    </table>

    <table class="pure-table" style="width:100%;">
      <thead>
        <tr>
          <th>Amount to be collected</th>
          <th>Instructions</th>
          <th>Payment Mode</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong> <span class="total_amount">
            @if( $order->payment_method_id == 1 )
                {{(number_format($order->total_amount, 2))}}
            @else
            0
            @endif
          </span> AED</strong></td>
          <td></td>
          <td>
            @if( $order->payment_method_id == 1 OR $order->payment_method_id == "" )
                Cash On Delivery
            @else
                Prepaid
            @endif
          </td>
        </tr>
      </tbody>
    </table>

    <table style="width:100%;" class="pure-table pure-table-bordered">
      <tr>
        <td>
          @php
          $totalProducts =0;
          @endphp
          @foreach ($order->returnProducts as $product )
            @php
            $totalProducts += $product->quantity;
            @endphp
          @endforeach
          <strong> Details </strong> Total Products : <span class="count">{{$totalProducts}} </span><br />
          @php
          $totalProducts = 0;
          @endphp
          [
          @foreach ( $order->returnProducts as $product )
          {{ $product->sku }}
          (QTY:{{$product->quantity}})
            @if(  $product->product?->option_value > 0  )
                    <strong>{{ $product->option_name }}</strong> : {{ $product->option_value }}
            @endif
            <strong>Color : </strong>{{ $product->product?->option_name }},
          @endforeach
          ]
        </td>
      </tr>
    </table>
  </div>
  <div class="page-break"></div>
  @endforeach
  @else
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="alert alert-danger">
          <strong>Ooopss!</strong> Something went wrong.
        </h1>
      </div>
    </div>
  </div>
  @endif
</body>
</html>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript">
  JsBarcode(".barcode").init();
</script>
<style type="text/css">

  html, body, div, span, applet, object, iframe,
  h1, h2, h3, h4, h5, h6, p, blockquote, pre,
  a, abbr, acronym, address, big, cite, code,
  del, dfn, em, img, ins, kbd, q, s, samp,
  small, strike, strong, sub, sup, tt, var,
  b, u, i, center,
  dl, dt, dd, ol, ul, li,
  fieldset, form, label, legend,
  table, caption, tbody, tfoot, thead, tr, th, td,
  article, aside, canvas, details, embed,
  figure, figcaption, footer, header, hgroup,
  menu, nav, output, ruby, section, summary,
  time, mark, audio, video {
/*        margin: 0;
padding: 0;*/
border: 0;
}


body { font-family: DejaVu Sans;font-size: 10px; color:#000 !important }
table{
  margin: 5px;
}
.pure-table{
  border: 1px solid #c1c1c1;
  border-spacing:0;
  border-collapse: collapse;
}

.pure-table td{
  border: 1px solid #c1c1c1;
  padding: 5px;
}
.pure-table th {
  background: #c1c1c1;
  color: #000;
}
div.item {
  /* To correctly align image, regardless of content height: */
  vertical-align: top;
  display: inline-block;
  /* To horizontally center images and caption */
  text-align: center;
  /* The width of the container also implies margin around the images. */

}

.caption {
  /* Make the caption a block so it occupies its own line. */
  display: block;

}

.logo{    width: 35mm;}
.container, body
{
  width: 95mm !important;
  margin: auto;
  display: inherit;
}
table{margin:0px}
table td , th {  font-family: sans-serif;font-size: 12px; color:#000 !important}
.total_amount{ font-size: 30px}
.count{
  display: inline-block;
  height: 20px;
  width: 20px;
  line-height: 21px;
  -moz-border-radius: 30px;
  border-radius: 23px;
  background-color: #000 !important;
  color: white !important;
  text-align: center;
  font-size: 14px;
}
@media print {
  .page-break  { display: block; page-break-before: always; }
}
</style>
