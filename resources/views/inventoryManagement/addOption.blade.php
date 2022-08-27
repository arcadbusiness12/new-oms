{{-- <h4 class="text-center"><strong>{{$details->option_name}}</strong></h4> --}}

  <form action="{{route('add.option.details',$id)}}" method="post" name="form-setting" id="myForm">
    {{ csrf_field() }}
    <input type="hidden" name="option_name_id" value="{{$details->id}}">
    <input type="text" name="option_name" value="{{$details->option_name}}" autocomplete="off" onkeyup="checkName(this.value)" class="form-control">
    @error('option_name')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
    <table class="table thead-dark">
     <thead>
      <th>Option Values</th>
      <th>Remove</th>
    </thead>
    <tbody class="rowNew">
      @foreach($option_values as $value)
      <tr id="value-row{{$value->id}}">
       <td>
         <input type="hidden" name="id[]" value="{{$value->id}}" autocomplete="off">
         <input type="text" name="title[]" value="{{$value->value}}" autocomplete="off" class="form-control">
       </td>
       <td><a class="btn-danger btn" href = "javascript:;"  onclick="destroyOptionValue({{$value->id}})"><i class="icon-close"></i></a></td>
     </tr>
     @endforeach
     @if(count($option_values) < 1)
     <tr>
      <td>
        <input type="text" name="title[]" value="" autocomplete="off" class="form-control">
      </td>
      <td><button id="removeRow" type="button" class="btn btn-danger"><i class="icon-close"></i></button></td>
    </tr>
    @endif
   </tbody>
 </table>
 <div class="form-group">
  <div class="row">
    <div class="col-lg-8">
      <div id="popupNewRow" style="width: 225%!important; margin-left:10px;"></div>
    </div>
    <div class="col-md-4" style="margin-top:27px;">
    </div>
  </div>
</div>
<input class="btn btn-success" type="submit" name="submit" id="btn-update" value="Update All">
<button id="addNewRow" type="button" class="btn btn-primary" style=""><i class="icon icon-plus-square"></i> Add More</button>
</div>
</form>