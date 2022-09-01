 <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Order Activity Log</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" >
            <table class="table table-bordered">
                <thead style="background: #3379b7; color:white">
                    <tr>
                    <td align="center"><b>SN</b></td>
                    <td align="center"><b>User</b></td>
                    <td align="center"><b>Activity</b></td>
                    <td align="center"><b>Date</b></td>
                    </tr>
                </thead>
                <tbody id="historyModal_content" style="background: lightgray;">

                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
        </div>
        </div>
    </div>
    </div>
    @push('scripts')
    <script type="text/javascript">
    $(document).on('click','#order_history',function(event){
      //console.log(event);
      console.log($(this).attr('data-orderid'));
      var order_id = $(this).attr('data-orderid');
      var store = $(this).attr('data-store');
      $.ajax({
          method: "POST",
          url: APP_URL + "/orders/activity-details",
          data: {order_id:order_id,store:store},
          dataType: 'json',
          cache: false,
          headers:
          {
              'X-CSRF-Token': $('input[name="_token"]').val()
          },
      }).done(function (data)
      {
          console.log(data);
          var rows = "";
          var counter = 0;
          var created_by = "";
          var comment = "";
          for (const res of data){
              if( res.user == null ){
                created_by = res.courier.name;
                if( res.courier.auto_deliver == 1 ){
                  created_by = res.courier.name+"(Cron Job)";
                }
              }else{
                created_by = res.user.firstname+" "+res.user.lastname;
              }
              comment = "";
              if( res.comment ){
                comment = "<br><small>("+res.comment+")</small>";
              }
              counter++;
              rows +="<tr>";
              rows +="<td align='center'>"+counter+"</td>";
              rows +="<td align='center'>"+created_by+"</td>";
              rows +="<td align='center'>"+res.activity.title+comment+"</td>";
              rows +="<td align='center'>"+res.created_at+"</td>";
              rows +="</tr>";
          }
          $('#historyModal_content').html(rows);
      }); // End of Ajax
    });
    </script>
    @endpush
