<div class="table-responsive">
    <?php $va = [] ?>
        <table class="table table-bordered table-hover">
          <input type="hidden" name="groupid" value="{{$groupid}}">
          <input type="hidden" name="groupname" value="{{$groupname}}">
          {{-- <caption style="font-size: 22px;">{{$userbDetails->firstname}}</caption> --}}
            <thead style="background:#3f51b5;color:white;">
                <tr>
                  <th class="text-center" style="width:12%;font-weight:bold;">Size Tag</th>
                  <th class="text-center" style="width:12%;font-weight:bold;"></th>
                  @foreach($topOptions as $toption)
                  <th class="text-center" style="width:19%;font-weight:bold;">{{$toption->name}}</th>
                  @endforeach
                </tr>
            </thead>
            <tbody id="shipment_data">
              {{-- @if($userbDetails)
              @foreach($userbDetails->bankDetails as $detail) --}}
              @foreach($sizeOptions->omsOptionsDetails as $option)
              @php 
              $headRows = count($topOptions);
              $textAlign = '';
              @endphp
             <?php if($headRows > 4) {
               $textAlign = 'right';
             } else {
               $textAlign = 'center';
             } ?>

                  <tr class="text-center">
                    <td style="vertical-align: middle;font-weight:bold;background:#3f51b5; color:white">{{($option->options == 19) ? 'Colors' : $option->value}}</td>
                    
                    <td colspan="{{count($topOptions)+1}}" style="border:none !important;">
                      
                       <table class="table table-bordered table-hover">
                        <tr class="text-center" style="border-bottom: ">
                          <td class="text-center" style="width:12%;border: none;">CM</td>
                          @foreach($topOptions as $toptn)
                          {{-- <input type="hidden" name="topOptions[]" value="{{$toptn->id}}"> --}}
                          <td class="text-{{$textAlign}} tdcm-{{$option->id}}-{{$toptn->id}}" style="width:22%;border: none;">
                            @php $va = ''; $inch_va = ''; @endphp
                            @if(count($sizeChartValues) > 0)
                            @foreach($sizeChartValues as $key => $value)
                             @if($value['option_id'] == $option->id && $value['size_chart_option_id'] == $toptn->id && $value['cm_inch'] == 'cm')
                               @php $va = $value['value'];
                                $co = $value['option_id'] .'=='. $option->id .'&&'. $value['size_chart_option_id'] .'=='. $toptn->id .'value= '.$va;
                               @endphp
                               
                             @endif
                             @if($value['option_id'] == $option->id && $value['size_chart_option_id'] == $toptn->id && $value['cm_inch'] == 'inch')
                               @php $inch_va = $value['value'];
                                $co = $value['option_id'] .'=='. $option->id .'&&'. $value['size_chart_option_id'] .'=='. $toptn->id .'value= '.$va;
                               @endphp
                               
                             @endif
                            @endforeach
                            @endif
                            <input type="text" name="cm[{{$option->id}}][{{$toptn->id}}][]" value="{{$va}}" onkeyup="calculateInch(this.value, {{$option->id}}, {{$toptn->id}})" style="width: 100px;">
                            <input type="hidden" name="inch[{{$option->id}}][{{$toptn->id}}][]" class="input-inch{{$option->id}}-{{$toptn->id}}" value="{{$inch_va}}" style="width: 100px;">
                          </td>

                          @endforeach
                        </tr>
                      </table>
                      {{-- <table class="table table-bordered table-hover">
                        <tr class="text-center">
                          <td class="text-center" style="width:12%;border: none;">INCH</td>
                          @foreach($topOptions as  $toptn)
                          <td class="text-{{$textAlign}} tdinch-{{$option->id}}-{{$toptn->id}}" style="width:22%;border: none;">
                            @php $inch_va = ''; @endphp
                            @if(count($sizeChartValues) > 0)
                            @foreach($sizeChartValues as $key => $value)
                             @if($value['option_id'] == $option->id && $value['size_chart_option_id'] == $toptn->id && $value['cm_inch'] == 'inch')
                               @php $inch_va = $value['value'];
                                $co = $value['option_id'] .'=='. $option->id .'&&'. $value['size_chart_option_id'] .'=='. $toptn->id .'value= '.$va;
                               @endphp
                               
                             @endif
                            @endforeach
                            @endif
                            <input type="text" name="inch[{{$option->id}}][{{$toptn->id}}][]" class="input-inch{{$option->id}}-{{$toptn->id}}" value="{{$inch_va}}" style="width: 100px;">
                          </td>

                          @endforeach
                        </tr>
                      </table> --}}
                    </td>
                  </tr>
                  @if($option->options == 19)
                   @php break; @endphp
                  @endif
             @endforeach
              
              {{-- @endforeach
              @else  --}}
              {{-- <tr class="text-center">
                <td colspan="4">No Bank Detail</td>
              </tr> --}}
              {{-- @endif --}}
            </tbody>
        </table>
      </div>
   
<script type="text/javascript">
function showForwordButton(courier_id){
  courier_id = parseInt(courier_id);
  if( courier_id > 0 ){
    $('.popup_btn_forword').removeClass('hidden');
  }else{
    $('.popup_btn_forword').addClass('hidden');
  }
}

function calculateInch(value, option, top) {
  console.log(value);
  if(value) {
    var inches = value/2.54;
    console.log(inches);
    inches = inches.toFixed(1);
    $('.input-inch'+option+'-'+top).val(inches);
  }
}  

</script>