<style>
    table thead {background: #001587; color: white;}
    .dataTables_wrapper .dataTable th {border: solid 1px white; color: white; font-size: 12px; width: auto !important;}
    table th hr {border-color: white;}
    .dataTables_wrapper .dataTable tbody tr.odd {background: #a3d1ff;}
    table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before,
    table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {content: "";}
</style>

<table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_2">
    <thead>
    <tr>
        <th class="disable_sort">
            <label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
                <input type="checkbox" id="checkAllJX" >
                <span></span>
            </label>
        </th>
        <th>#</th>
        <th>Client Name <hr /> Client Phone</th>
        <th>Status <hr /> REP</th>
        <th>Order Dated <hr /> City</th>
        <th class="d-none d-sm-table-cell">Items <hr /> Total</th>
        <th>Notes</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $order)
        <tr @if(!order_notes_stats($order->id)) class="notes_not_viewed" @endif id="tr_{{$order->id}}">
            <td>
                <label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
                    <input type="checkbox" class="check_single" name="item[]" value="{{$order->id}}"  />
                    <span></span>
                </label>
            </td>
            <td><b>{{$order->order_number}}</b></td>
            <td><b>{{$order->client_info->name}}</b> <hr /> {{$order->client_info->phone}}</td>
            <td>@if($order->status > 0) {{$order->status_info->title}} @else {{$statuss[0]->title}} @endif <hr /> @if($order->delivered_by > 0) {{$order->delivery_info->name}} @endif</td>
            <td>{{date('Y-m-d', strtotime($order->created_at))}} <hr /> @if($order->city > 0) {{$order->city_info->title}} @endif</td>
            <td class="d-none d-sm-table-cell">PCS : {{$order->itemsq->sum('qty')}} <hr /> {{$order->total_price + $order->shipping_fees}}</td>
            <td>
                @foreach(\App\OrderNote::where('order',$order->id)->latest()->take(3)->get() as $note)
                    {{$note->note}}
                    <hr/>

                @endforeach
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if($edit_selling_orders)
                            <a class="dropdown-item" href="{{route('selling_order.edit', $order->id)}}">Edit</a>
                        @endif
                        @if($order->mylerz_barcode != '')
                            <a class="dropdown-item" href="{{url('mylerz_shipping/get_awb/'.$order->id)}}">Mylerz AWB</a>
                        @endif
                        @if($add_selling_order)
                            @if($order->order_id == 0)
                                <a class="dropdown-item" href="{{url('selling_reorder/'.$order->id)}}">Re-Order</a>
                            @else
                                <a class="dropdown-item" href="{{url('selling_reorder/'.$order->order_id)}}">Re-Order</a>
                            @endif
                        @endif
                        <a class="dropdown-item" href="{{route('selling_order.show', $order->id)}}">Details</a>
                        <a class="dropdown-item" href="{{url('/selling_order/orders_operation/Print_Invoice?orders='.$order->id)}}" target="_blank">Invoice</a>

                        <button class="dropdown-item sellorder_notes_viewer showNotes"  data-route-notes="{{route('admin.order_notes',$order->id)}}" order-num="{{$order->id}}"  url="{{url('sellorder_notes_viewer')}}" >Notes</button>

                        <button class="dropdown-item showTimeLine" data-route="{{route('admin.order_time_line',$order->id)}}"  data-id="{{$order->id}}" >Time Line</button>


                        @if($delete_selling_order)
                            <button  class="dropdown-item deleteOrderBtn" data-route="{{ url('selling_order/'.$order->id) }}" data-id="{{$order->id}}" >Delete</button>
                        @endif
                    </div>
                </div>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>




