
<!--  size chart modal start -->
<div class="modal fade size_chart_modal" id="size_chart_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" >
        <div class="modal-header" style="text-align: center;
        background-color: gray;
        border: 1px solid white;">
           <h5 class="modal-title" id="sizeChartModalTitle" style="display: inline-block;color: white;"></h5> 
          <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close" style="color: white;">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
          <form method="post" id="frm_update_size_chart" action="">
            {{csrf_field()}}
            <input type="hidden" name="group_name" id="group_name">
            <div class="row text-center" id="size_chart_popup_content">
            </div>
            <div class="modal-footer">
              <div class="col-12">
                <div class="col-8 col-grid">
                  <span class="text-success" style="color: green!important;"></span>
                  <span class="text-error" style="color: red;"></span>
                </div>
                <div class="col-4 col-grid">
                  <button type="submit" class="btn btn-success float-end">SAVE</button>
                </div>
              </div>
            </div>
          </form>
        
      </div>
    </div>
  </div>
  </div>
  <!--  size chart modal end -->

  <!--  update price modal start -->
<div class="modal fade update_price_modal" id="update_price_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 60%">
      <div class="modal-content" >
        <div class="modal-header">
          {{--  <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Add/Update Prices</h5>  --}}
          <button type="button" class="close close-popup" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
          <span id="top-title"></span>
        </div>
        <div class="modal-body" >
          <form method="post" id="frm_update_site_prices" action="">
            {{csrf_field()}}
            <input type="hidden" name="group_name" id="site_prices_group_name">
            <div class="row" id="update_prices_popup_content" >
            </div>
        <div class="modal-footer">
          <div class="col-12 text-right">
              <button type="submit" class="btn btn-success pull-right">SAVE</button>
            
        </div>
      </div>
    </form>
    </div>
  </div>
  </div>
</div>

  <!--  update promotion prices modal start -->
<div class="modal fade update_promoton_price_modal" id="update_promoton_price_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
      <div class="modal-header">
        {{--  <h5 class="modal-title" id="exampleModalCenterTitle" style="display: inline-block;">Add/Update Prices</h5>  --}}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <span id="top-title"></span>
      </div>
      <div class="modal-body" >
        <form method="post" id="frm_update_site_promotion_prices" action="">
          {{csrf_field()}}
          <input type="hidden" name="promotion_group_name" id="promotion_group_name">
          <div class="row" id="update_promotion_prices_popup_content">
          </div>
          
          <div class="modal-footer">
            <div class="col-sm-12">
              <div class="col-8 col-grid">
                <span class="promotion-text-error" style="color: red;"></span>
              </div>
              <div class="col-4 col-grid text-right">
              <button type="submit" class="btn btn-success pull-right">SAVE</button>
              </div>
            </div>
          </div>
        </form>
      
    </div>
  </div>
</div>
</div>