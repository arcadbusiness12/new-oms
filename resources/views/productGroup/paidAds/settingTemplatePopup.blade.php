<style>
   .tab-links a {
        text-decoration: none;
        border: 1px solid gray;
        padding: 5px 10px;
        text-transform: uppercase;
        text-align: center;
        font-size: 13px;
        float: left;
        margin-right: 5px;
    }
    .listing, .btn-defualt {
    margin-bottom: 7px;
    width: auto;
}
.tab-links a {
    text-decoration: none;
    border: 1px solid gray;
    padding: 5px 10px;
    text-transform: uppercase;
    text-align: center;
    font-size: 13px;
    float: left;
    margin-right: 5px;
}
.main-tag, .tab-links {
    padding-right: 0px !important;
    padding-left: 0px !important;
}
.btn-active {
    background-color: green !important;
    color: white !important;
}
  </style>
  <!--<button class="btn btn-success" id="printtbl" style="text-align: right;">Print</button> -->
  <div style="background-color:white;color:#ed2222" class="col-12 col-md-12 col-sm-12 main-tag">
    <div class="top-social-sec">
          <input type="hidden" name="group" value="{{$group}}">
          <input type="hidden" name="store" value="{{$store}}">
     
      <div class="setting-form-row tab-links">
        {{-- <select class="form-control" onchange="getSchedules(this.value, '{{$type}}', '{{$store}}', '{{$group}}')" class="template-select">
          <option value="">Select template</option>
          @foreach($setting_templates as $template)
          <option value="{{$template->id}}">{{$template->title}} | {{$template->setting_name}} | {{$template->user['firstname'] ? $template->user['firstname']. ' '.$template->user['lastname'] : ''}}</option>
          @endforeach
        </select> --}}
        <div class="row" style="margin-left: 11px;">
          @foreach($setting_templates as $template)
            @if($template->setting_name)
            {{-- <div class="col-2 col-md-2 col-sm-2"> --}}
              <a href="javascript:;" class="listing active-list{{$template->id}}" onclick="getSchedules('{{$template->id}}', '{{$type}}', '{{$store}}', '{{$group}}','{{$select_cate}}','{{$template->remain_days}}')">
                <div class="tab-box">{{$template->setting_name}}</div>
              </a>
            {{-- </div> --}}
            @endif
            
          
          @endforeach
  
        </div>
        <div class="row" style="margin-left: 11px;">

          <button type="button" class="btn btn-defualt btn-action btn-current btn-active" onclick="currentOrNext('current')">Current</button> 
          <button type="button" class="btn btn-defualt btn-action btn-next upcoming-btn" style="border: 1px solid darkgray; margin-left: 12px;" onclick="currentOrNext('next')">Upcoming</button> 
        </div>
      </div>
    </div>
    <div class="row text-center" >
      <span class="text-right" id="error_mesge" style="color:red;">  </span>
      <span class="schedule_error text-center" id="schedule_error" style="color: red; font-weight: bold; margin-right: 245px; display: none;"></span>
    </div>
  </div>
        <div class="view schedules-data">
          <div class="temp-loader">
            <div class="modal-content-loader tmp-loader" style="display: none;"></div>
          </div>
  
      </div>
      <div class="row text-center mt-4" >
       <span class="history-loader" style="display: none;">History is loading..</span>
       <div class="history-data"></div>
      </div>
  <script>
    function createCampaign(template, main_id, date) {
      console.log(template+'_'+date);
      $('#campaign-input').val(template+'_'+date);
      $('#campaign-input').focus();
      $('#campaign-input').select();
      $('#campaign-main-id').val(main_id);
    }
    $(document).ready(function(){
      console.log("Yes Load");
      console.log(<?php echo @$group ?>);
      loadDetails(<?php echo @$group ?>);
  
    });
  
  function loadDetails(value, list = null) {
    $('.history-data').html('');
    // $('tr td .history-tbl-'+value).html('');
      console.log("Popup "+ value);
    if(value) {
      $('.history-loader').css('display', 'block');
      $.ajax({
        url: "{{url('/productgroup/get/schedule/group/detail')}}/"+value,
        type: 'GET',
        cache: false,
        success: function(resp) {
          $('.history-loader').css('display', 'none');
        $('.history-data').html(resp);
        if(list) {
          $.ajax({
            url: "{{url('get/schedule/group/history')}}/"+value,
            type: 'GET',
            cache: false,
            success: function(respo) {
              // history_row = resp.match(/<t(.)>.*?<\/t\r>/g);
              $('tr td .history-tbl-'+value).html(respo);
            
            }
          })
          $('tr td .history-tbl-'+value).html(resp);
        }
        
        }
      })
    }
  }
  
    $("#printtbl").on('click', function() {
      var printContents = document.getElementById('print-table').innerHTML;
          var originalContents = document.body.innerHTML;
  
          // document.body.innerHTML = printContents;
    // window.open(printContents);
    var htmlToPrint = '' +
      '<style type="text/css">' +
      'table {' +
        'border-spacing: 0;' +
        'border-collapse: collapse;' +
        '}'+
      'table tr {' +
      'border-top: 1px solid gray !important;' +
      'padding:0.5em;' +
      '}' +
      '</style>';
      htmlToPrint += printContents;
    var printWindow = window.open('', 'PRINT');
    printWindow.document.write(htmlToPrint);
    printWindow.focus();
          printWindow.document.close(); // necessary for IE >= 10
    printWindow.focus(); // necessary for IE >= 10*/
    printWindow.print();
    setTimeout(() => {
      printWindow.close();
    }, 2500);
  });
  
  function getSchedules(value,type, store, group_id, selected_cate, remain_days = null) {
    $('.listing').removeClass('btn-active');
    $('.active-list'+value).addClass('btn-active');
    $('.btn-action').removeClass('btn-active');
    $('.btn-current').addClass('btn-active');
    // if(remain_days && remain_days <= 3) {
    //   $('.upcoming-btn').css('display', 'inline-block');
    // }else {
    //   $('.upcoming-btn').css('display', 'none');
    // }
    if(value) {
      $.ajax({
        url: "{{url('/productgroup/get/template/schedules')}}/"+ value +'/' +type +'/' +group_id +'/' +store+ '/'+ selected_cate,
        type: "GET",
        cache: false,
        beforeSend: function() {
          $('.tmp-loader').css('display', 'block');
        },
        complete: function() {
          $('.tmp-loader').css('display', 'none');
        }
      }).then(function(resp) {
        $('.schedules-data').html(resp);
      })
    }
  }
  
  function currentOrNext(v) {
    console.log(v);
    $('.btn-action').removeClass('btn-active');
    if(v == 'current') {
      $('.current-campaign').css('display', 'block');
      $('.next-campaign').css('display', 'none');
      $('.btn-current').addClass('btn-active');
      $('.current-section').css('display', 'revert');
      $('.next-section').css('display', 'none');
    }else {
      $('.current-campaign').css('display', 'none');
      $('.next-campaign').css('display', 'block');
      $('.btn-next').addClass('btn-active');
      $('.current-section').css('display', 'none');
      $('.next-section').css('display', 'revert');
    }
  }
  function changeSchedule(main_setting_id,setting_id,type, category, group_type, group_code,group_id,post_id,socials,date,store,range, action, sub_category=null) {
    console.log(action);
      $('.modal-content-loader').css('display', 'block');
    $('#changed-group').text(group_code);
    $.ajax({
      url: "{{url('/promotion/get/new/schedule')}}/"+main_setting_id +"/"+setting_id +"/"+type +"/"+ category +"/"+group_type +"/"+ group_code+"/"+ group_id +"/"+ post_id+"/"+socials+ "/" +date +"/"+ store+ "/" +2 +"/"+range+"/"+action+"/"+sub_category,
      type: "GET",
      cache: false,
      success: function(resp) {
        $('.modal-content-loader').css('display', 'none');
        
          $('#schedule_view_content').html(resp);
      }
    })
  }
  
  function getNewForEmptyDaySchedule(row,main_setting_id,setting_id,type, category,category_ids, group_type,socials,store, range,budget, action = null, start_date = null, end_date = null, sub_category=null) {
      $('.modal-content-loader').css('display', 'block');
      console.log(sub_category);
    $.ajax({
      url: "{{url('/promotion/get/new/schedule/For/empty/paid/ads')}}/"+row +"/" +main_setting_id +"/"+setting_id +"/"+type +"/"+ category  +"/"+ category_ids +"/"+group_type +"/"+socials +"/"+ store+ "/" +2+"/"+range+"/"+ budget+"/"+ action+"/"+ start_date+"/"+ end_date+"/"+sub_category,
      type: "GET",
      cache: false,
      success: function(resp) {
        $('.modal-content-loader').css('display', 'none');
        
          $('#schedule_view_content').html(resp);
      }
    })
  }
  
  
  function closeModal(modal) {
    $('#setting_view_modal').modal('toggle');
      $(document).find('#setting_view_modal').on('hidden.bs.modal', function () {
      console.log('hiding child modal');
      $('body').addClass('modal-open');
  })
  }
  </script>