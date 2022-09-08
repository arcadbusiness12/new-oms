<div class="table-responsive m-t-15">
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($response as $orderID => $orderResp)
            <tr>
                <td>{{$orderID}}</td>
                <td>{{$orderResp}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
