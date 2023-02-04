 <!--  Setting modal end -->
  <div class="modal fade stock_detail_modal" id="stock_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header text-center">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;">Chat history of <span id="changed-group" style="color: green;"></span></h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
       <div class="body-data" id="body-data"></div>

      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
</div>


<div class="modal fade setting_view_modal" id="promotion_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Change Schedule/ <span id="changed-group"></span></h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
      <div class="modal-content-loader"></div>
        <div id="schedule_view_content">
        
        
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
</div>
<!--  Setting modal end -->

<!--  chat history modal start -->
<div class="modal fade chat_history_view_modal" id="chat_history_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header text-center">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;margin-top:18px;color: green;">Rusult history<span id="changed-group" style="color: green;"></span></h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        {{-- <span id="top-title"></span> --}}
      </div>
      <div class="modal-body" >
      {{-- <div class="modal-content-loader"></div> --}}
      <table class="table table-bordered table-hover">
        <caption class="caption-text"></caption>
        <thead style="background-color: #eee;">
            <tr>
                <th class="text-center th-textf">Budget Used</th>
                <th class="text-center th-textf">Result</th>
                <th class="text-center th-textf">Cost Per Result</th>
                <input type="hidden" name="store" value="" id="store_76">
                <th class="text-center th-textl">Date</th>
          </tr>
        </thead>
        <tbody class="history">
        </tbody>
      </table>
        {{-- <div class="row text-center" id="schedule_view_content">
        <strong>Average Cost : <span class="av-cost"></span></strong> 
        
      </div> --}}
       <div class="body-data" id="body-data"></div>

      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
</div>

<!--  chat history modal end -->

<!--  Create campaign modal start -->
<div class="modal fade" id="create_campaign_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-right: 15px;">
  <div class="modal-dialog modal-lg" style="width: 100%">
    <div class="modal-content" style="width: 124%;">
      <div class="modal-header text-center">
        <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Create Campaign<span id="changed-group" style="color: green;"></span></h5>
        <button type="button" class="close close-modal" data-bs-dismiss="modal" aria-bs-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        {{-- <span id="top-title"></span> --}}
      </div>
      <div class="modal-body" >
        <tbody class="history">
          <form id="campaign-form">
            {{csrf_field()}}
            <div class="col-sm-12">
              <div class="col-sm-12">
              <input type="hidden" name="main_id" id="campaign-main-id">
                <div class="modal-content-loader text-center pt-2" id="loader">
                  @include('loader');
                </div>
                <div class="modal-template-content">
                </div>
                
              </div>
              
             <div class="col-sm-2 col-grid">
              <div class="form-group">
                <input type="button" name="Create" class="btn btn-info form-control active" id="btn-save-campaign" value="Create">
              </div>
             </div>
             <div class="col-sm-8 col-grid">
              <span class="alert-error alert-error-campaign text-left float-left" style="color:red;font-size: 18px;"></span>
                <span class="text-left float-left" id="error-msge" style="color:red;font-size: 18px;">  </span>
             </div>
        </div>
          </form>
        </tbody>
    </div>
  </div>
</div>
</div>

<!-- campaign modal end -->

<div class="modal fade chat_warring_modal" id="chat_warring_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
        <div id="chat_warring_mesge">
        
        
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
</div>

