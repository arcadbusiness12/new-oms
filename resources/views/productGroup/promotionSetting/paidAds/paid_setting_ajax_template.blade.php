@if(count($promotion_main_setting) > 0)
@foreach(@$promotion_main_setting as $key=>$setting)
<tr id="row_{{$key}}" style="border-top: 1px solid gray">

          <td><center><label>{{ $setting->setting_name }}</label></center></td>
          <td><center><label>{{ @$setting->user->firstname }} {{ @$setting->user->lastname }}</label></center></td>
          <td><center><label>{{ $setting->title }}</label></center></td>
          <td><center><label>{{ $setting->adsType->name }}</label></center></td>
          <td>
          <center><label>
          <a href="#"><i class="icon icon-pencil-square-o fa-2x" aria-hidden="true" title="Add Products" onclick="checkSettings('{{$setting->store_id}}','{{$setting->id}}')" data-toggle="modal" data-target=".setting_view_modal"></i></a> |
          
          <a href="#"><i class="icon icon-trash-o fa-2x" onclick="deleteGroup('{{ $setting->id }}', '{{$key}}')" aria-hidden="true" title="Delete" data-toggle="tooltip"></i></a> | 
          <a href="#"><i class="icon icon-copy fa-2x" onclick="createCupy('{{ $setting->id }}', '{{$key}}','ba')" aria-hidden="true" title="Create Cupy" data-toggle="tooltip"></i></a>

          
          </label></center>
          </td>

</tr>
@endforeach

@else
<tr id="tr_{{@$group->id}}" style="border-top: 1px solid gray">

<td colspan="2" class="column col-sm-12">
  <center><label>No Setting Available..</label></center>
</td>
</tr>
@endif