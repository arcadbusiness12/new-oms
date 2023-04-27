<div class="modal fade new_group_entry_model" id="new_group_entry_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header text-center">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;">New Group Entry <span id="changed-group" style="color: green;"></span></h5>
          <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('employee-performance.photography.changeGroup') }}">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-10">
                <div class="form-group form-float">
                  <select class="show-tick" data-live-search="true" name="group_id" onchange="loadDetails(this.value)">
                    @forelse($all_groups as $key => $group)
                      <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @empty
                    @endforelse
                  </select>
                  <input type="hidden" id="posting_date" name="posting_date" />
                  <input type="hidden" id="model_id" name="model_id" value="{{ $request_model_id }}" />
                  <input type="hidden" id="row_id" name="row_id" />
                </div>
              </div>
              <div class="col-md-2">
                <input type="submit" value="Save" class="btn btn-success">
              </div>
            </div>
          </form>
          <div  class="body-data"></div>
          <div  class="history-area"></div>
          <div class="modal-footer">
          </div>
        </div>
    </div>
  </div>
</div>
<script>
  function loadDetails(value) {
    //console.log(value);
    $('.body-data').html('');
    if(value) {
      $('.history-loader').css('display', 'block');
      $.ajax({
        url: "{{url('get/schedule/group/detail')}}/"+value,
        type: 'GET',
        cache: false,
        success: function(resp) {
          console.log(resp);
          $('.history-loader').css('display', 'none');
          $('.body-data').html(resp);
          $('#history-tbl').hide();
        }
      })
    }
    //
    history(value);
  }
  function history(sku_group_id){
    //alert(sku_group_id);
    $.ajax({
      url: "{{url('employee-performance/photography/posting-history')}}",
      type: 'GET',
      data: { sku_group_id: sku_group_id },
      cache: false,
      success: function(resp) {
        console.log(resp);
        $('.history-loader').css('display', 'none');
        $('.history-area').html(resp);
        $('#history-tbl').hide();
      }
    })
  }
</script>