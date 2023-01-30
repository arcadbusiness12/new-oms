<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Ship Print</title>
	</head>
    <body>
        <?php if($orders) { ?>
        <div class="book">
            <?php $i=1; foreach ($orders as $key => $order_detail) { ?>
            <div class="page">
                <div class="subpage">
                    <div class="container">
                        <table style="width:100%;margin-bottom: 15px;">
                            <tr>
                                <td>Date : <b><?php echo $ship_date ?></b></td>
                                <td>Shipper : <b><?php echo $shipper->name ?></b></td>
                                <td>Total Shipment : <b><?php echo $total_orders ?></b></td>
                            </tr>
                        </table>
                        <?php 
                      
                        ?>
                          <table style="width:100%;font-size: 13px" class="table-bordered" cellpadding="0" cellspacing="0">
                              <thead>
                                  <th>SNO</th>
                                  <th>Order ID</th>
                                  <th>AWB No</th>
                                  <th></th>
                                  <th>SNO</th>
                                  <th>Order ID</th>
                                  <th>AWB No</th>
                                  {{--  <th>Customer Name</th>
                                  <th>Telephone</th>
                                  <th>Address</th
                                  <th>Items QTY</th>
                                  <th>Amount</th>  --}}
                              </thead>
                              
                              <tr>
                                <?php foreach($order_detail as $sub_key => $order) { ?>
                                  <td><?php echo $i; ?></td>
                                  <td><center><?php echo $order['order_id']; ?></center></td>
                                  <td><center><?php echo $order['awb']; ?></center></td>
                                  <!--  <td><?php echo $order['name']; ?></td>                               
                                  <td><?php echo $order['mobile']; ?></td>
                                  <td style="display: -webkit-box;overflow: hidden;-webkit-line-clamp: 4;-webkit-box-orient: vertical;"><?php echo $order['address']; ?></td>
                                  <td><?php echo $order['qty']; ?></td>
                                  <td><?php echo $order['amount']; ?></td>  -->
                                  @php
                                  if( $sub_key%2 ==1  ){
                                    echo "</tr><tr>";
                                  }else{
                                    echo "<td></td>";
                                  }
                              @endphp
                              <?php $i++; } ?>
                            </tr>
                          </table>
                        <?php if( ( $key+1 ) == count($orders) ){?>
                          <table style="width:100%;margin-top: 10px;position: absolute;bottom: 50px">
                              <tr>
                                  <td style="width: 33.3%">Name:</td>
                                  <td style="width: 33.3%">Sign:</td>
                                  <td style="width: 33.3%">Courier:</td>
                              </tr>
                          </table>
                        <?php } ?>
                    </div>
                </div>    
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </body>
</html>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript">JsBarcode(".barcode").init();</script>
<style type="text/css">
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 12pt "Tahoma";
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        position: relative;
        width: 210mm;
        min-height: 297mm;
        padding: 10mm 5mm;
        margin: 10mm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage {
        height: 257mm;
    }
    
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm;        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
    @media print {
        .page-break  { display: block; page-break-before: always; }
    }
    .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd;
        padding: 5px;
    }
</style>