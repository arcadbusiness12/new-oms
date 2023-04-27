
            <div class="sub-setting-loop col-md-12" >
              <form name="setting_form" class="setting_form" class="" action="" method="post">
                          {{ csrf_field() }}
                          <div class="row setting-form-row">
                            <div class="col-md-4 setting-input">
                              <input type="text" name="setting_name" class="form-control" value="{{ $data->name }}" placeholder="Setting Name">
                              <span class="alert-error alert-error-setting_name"></span>
                            </div>
                            <div class="col-md-4 setting-input">
                              <select class="form-control" name="user" id="product_change_status">
                                  <option value="">Select Model</option>
                                  @foreach($models as $model)

                                  <option value="{{$model->user_id}}" {{ ($model->user_id == $data->model_id) ? "selected" : "" }} >{{$model->firstname}} {{$model->lastname}}</option>
                                  @endforeach
                                </select>
                              <span class="alert-error alert-error-user"></span>
                            </div> 
                          </div>
                           <div class="row setting-form-row">
                              @foreach($socials as $social)
                                  @php
                                    $social_checked = "";
                                    $user_selected  = "";
                                    if( $data->SettingsSocialPosting ){
                                      foreach( $data->SettingsSocialPosting as $key => $val ){
                                        if( $val->promotion_social_id == $social->id ){
                                          $social_checked = 1;
                                          $user_selected  = $val->user_id;
                                          break;
                                        }
                                      }
                                    }
                                  @endphp
                                <div class="col-md-2" style="border:1px solid lightgray; padding:9px">                                
                                  <center><label class="social-lable"><input type="checkbox" name="social[]" class="social-check" id="social-check{{$social->id}}" value="{{$social->id}}" {{ $social_checked==1 ? "checked" : ""  }} > {{$social->name}}</label>
                                  <select class="form-control {{ $user_selected > 0 ? '' : 'hidden' }} social_postig_user{{ $social->id }}" name="posting_staff[{{$social->id}}]">
                                    <option value="">Select</option>
                                    @forelse( $posting_staff as $key => $staff )
                                    <option value="{{ $staff->user_id }}" {{ ( $staff->user_id == $user_selected ? "selected" : "" ) }}>{{ $staff->firstname }}</option>
                                    @empty
                                    @endforelse
                                  </select></center>
                                </div>
                              @endforeach
                           </div>
                          <input name="postIng_type" type="hidden" value="2">
                          <input name="main_setting_id" type="hidden" value="">

                          @if( $data->settingsDetail )
                            @foreach( $data->settingsDetail as $key => $settingsDetail )
                              <div class="row" id="exist_product0">
                                <div class="col-md-2 setting-input">
                                  <select class="form-control product_change_status0" name="type[]" id="product_change_status" onchange="checkType(0)">
                                      <option value="">Select Type</option>
                                      @foreach($types_for_setting as $type)
                                      <option value="{{$type->id}}" {{ ( $settingsDetail->promotion_product_type_id == $type->id ) ? "selected" : "" }}>{{$type->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 setting-input">
                                  <select style="" class="form-control singleselect0" onchange="cateVlue(0, 1), getSubCategories(this.value, 0)" data-live-search="true" placeholder="Select upto 5 tags" id="product_change_status">
                                    <option value="">Select Category</option> 
                                    @foreach($categories as $cate)                                          
                                      <option value="{{$cate->id}}" {{ ( $settingsDetail->category_id == $cate->id ) ? "selected" : "" }}>{{$cate->name}}</option>
                                    @endforeach
                                  </select>                                     
                                </div>
                                <div class="col-md-2 setting-input">
                                  <input class="form-check-input" type="checkbox" id="is_active_check01" onchange="isActiveOrNot('0',1)" {{ $settingsDetail->status ? "checked" : "" }}  />&nbsp;&nbsp; Status
                                </div>
                              </div>
                            @endforeach
                          @endif  
                            <div class="form-rows"></div>
                            
                            <div class="row">
                              <div class="col-md-12">
                                <div class="col-md-2">
                                
                                </div>
                                <div class="col-md-6 error-msge-div">
                                
                                  <span class="error-msge" ></span>
                                </div>
                                <div class="col-md-4">
                                  <button type="button" id="add-more" class="btn btn-sm btn-success add-more" style="margin-right: -11px;"><i class="fa fa-plus-circle"></i> </button>
                                </div>
                                </div>  
                            </div>
                            <div class="modal-footer">
                              <span class="text-right" id="error_mesge" style="color:red;">  </span>
                              <span class="m_success text-right" id="m_success" style="color:green; font-weight: bold;"></span>
                              <button type="submit" class="btn btn-info save-btn">Save</button>
                            </div>
                    </form>
            </div>
           