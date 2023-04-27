<div class="modal fade video_popup" id="video_popup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content" >
        <div class="modal-header text-center">
          <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;">Add video links <span id="changed-group" style="color: green;"></span></h5>
          <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body">
          <form method="post" action="{{ route('employee-performance.photography.saveShootPosting') }}">
            {{ csrf_field() }}
            <input type="hidden" id="product_group_id" name="product_group_id" required>
            <input type="hidden" id="product_group_name" name="product_group_name" required>
            <input type="hidden" id="task_id" name="task_id" required>
            <input type="hidden" id="social_id" name="social_id" required>
            <div class="row">
              <div class="col-md-10">
                <label>DF video link</label>
                <div class="form-group">
                  <input type="text" class="form-control" name="df_video_link" placeholder="DF video link" style="border:1px solid gray">
                </div>
              </div>
               <div class="col-md-10">
                <label>BA video link</label>
                <div class="form-group">
                  <input type="text" class="form-control" name="ba_video_link" placeholder="BA video link" style="border:1px solid gray">
                </div>
              </div>
              <div class="col-md-12">
                <input type="submit" value="Save" class="btn btn-success pull-right">
              </div>
            </div>
          </form>
          <div class="modal-footer">
          </div>
        </div>
    </div>
  </div>
</div>