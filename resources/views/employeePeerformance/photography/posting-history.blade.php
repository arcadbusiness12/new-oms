<strong>History</strong>
<table class="table table-bordered">
  <tr>
    <td>Action</td>
    <td>Date</td>
  </tr>
  @forelse ($data as $key => $value )
      <tr>
        <td>{{ $value->model->firstname }} <i style="color:green">Shooted</i></td>
        <td>{{ $value->created_at }}</td>
      </tr>
  @empty
    
  @endforelse
  
</table>