
@if($client)
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Client Name  </th>
            <th>City</th>
            <th>Lost</th>
            <th>Won</th>
            <th>Open</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$client->name}}</td>
                <td>{{$client->city_info->title??''}}</td>
                <td>{{$lost}}</td>
                <td>{{$won}}</td>
                <td>{{$open}}</td>
            </tr>
        </tbody>
    </table>


    <table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Order Date </th>
        <th>Order Number</th>
        <th>Status</th>
        <th>Total Price</th>
    </tr>
    </thead>
    <tbody>
       @forelse($orders as $order )
         <tr>
             <td>
                 {{date('Y-m-d', strtotime($order->created_at))}}
             </td>
             <td>{{$order->order_number}}</td>
             <td>{{$order->status_info->title??''}}</td>
             <td>{{$order->total_price + $order->shipping_fees}}</td>
         </tr>
         @empty
           <tr>
               <td colspan="4">No Orders Found</td>
           </tr>
       @endforelse
    </tbody>
</table>
@else


No Client Found


@endif
