@extends('admin.layout.main')
@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                    <h3 class="kt-portlet__head-title">Buying Orders</h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">

                            <div class="dropdown">


                                    <a href="{{route('admin.add_payment')}}" class="btn " style="background-color: #f0a202;"

                                    ><i
                                                class="fa fa-credit-card"></i> Payment
                                    </a>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <form action="#" method="get" class="kt-form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date"
                                           value="{{$from_date}}"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date"
                                           value="{{$to_date}}"/>
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-7 fv-row col-sm-4">
                                <!--begin::Label-->
                                <label for="product_id"  class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Products</span>
                                </label>
                                <select id='product_id'  name="product_id[]"  multiple style='width: 100%;'>
                                </select>
                            </div>



                            <div class="col-md-2">
                                <!--begin::Label-->
                                <label for="agent_id" >
                                    <span class="required mr-1">   Agents</span>
                                </label>
                                <select id='agent_id' name="agent_id" class="form-control" >
                                    <option @if(isset($request['agent_id'])) @if($request['agent_id']=='all') selected  @endif  @endif value="all">All</option>
                                        @foreach($agents as $agent)
                                            <option @if(isset($request['agent_id'])) @if($request['agent_id']==$agent->id) selected  @endif  @endif
                                                    value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                </select>
                            </div>






                            <div class="col-md-2">
                                <label class="control-label"><br/></label>
                                <button type="submit" class="btn btn-success btn-block">Search</button>
                            </div>

                        </div>
                    </div>
                </form>
                <table class="table table-striped- table-bordered table-hover table-checkable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order Number</th>
                        <th>Agent</th>
                        <th>Type</th>
                        <th>Items Number</th>
                        <th>Total Price</th>
                        <th>Payment Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr @if(!buy_order_notes_stats($order->id)) class="notes_not_viewed" @endif>
                            <td>{{date('Y-m-d', strtotime($order->shipping_date))}}</td>
                            <td>{{$order->id}}</td>
                            <td>{{optional($order->agent_info)->name}}</td>
                            <td>{{$order->type}}</td>
                            <td>{{$order->items->count()}}</td>
                            <td>{{$order->total_price}}</td>
                            <td>{{$order->payment_amount}}</td>
                        <td>
                                <div class="dropdown">
                                    <button class="btn btn-link" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_buying_order'))
                                            <a class="dropdown-item" href="{{route('buying_order.edit', $order->id)}}">Edit</a>
                                        @endif
                                        <a class="dropdown-item" href="{{route('buying_order.show', $order->id)}}">Details</a>
                                        @if($order->invoice != '')
                                            <a class="dropdown-item"
                                               href="{{url('/buying_order/orders_operation/Print_Invoice?orders='.$order->id)}}"
                                               target="_blank">Invoice</a>
                                        @endif
                                        <a class="dropdown-item sellorder_notes_viewer" order-num="{{$order->id}}"
                                           data-toggle="modal" url="{{url('buyingorder_notes_viewer')}}"
                                           href="#myNotes-{{ $order->id }}">Notes</a>
                                        @if($order->time_lines->count() > 0)
                                            <a class="dropdown-item" data-toggle="modal"
                                               href="#myTime-{{ $order->id }}">Time Line</a>
                                        @endif
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_buying_order'))
                                            <a class="dropdown-item" data-toggle="modal"
                                               href="#myModal-{{ $order->id }}">Delete</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal fade" id="myNotes-{{ $order->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order Notes</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-striped">
                                                    <tbody>
                                                    @if($order->note != '')
                                                        <tr>
                                                            <td>{{date('Y-m-d h:i A', strtotime($order->created_at))}}
                                                            <td>{{$order->note}}</td>
                                                        </tr>
                                                    @endif
                                                    @foreach ($order->notes as $note)
                                                        <tr>
                                                            <td>{{date('Y-m-d h:i A', strtotime($note->created_at))}}
                                                            <td>{{$note->note}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <form role="form" action="{{ url('buying_order_notes/'.$order->id) }}"
                                                      class="" method="POST" id="ajsuformreload">
                                                    {{ csrf_field() }}
                                                    <div id="ajsuform_yu"></div>
                                                    <div class="form-group">
                                                        <textarea name="note" class="form-control"></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-success" name='delete_modal'>
                                                        Save
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="myTime-{{ $order->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order Timeline</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body time_line_list">
                                                <ul>
                                                    @foreach ($order->time_lines as $line)
                                                        <li class="row">
                                                            <div class="col-md-4">{{date('Y-m-d h:i A', strtotime($line->created_at))}}</div>
                                                            <div class="col-md-8">{{$line->admin_info->name.$line->text}}</div>
                                                        </li>
                                                    @endforeach
                                                </ul>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_buying_order'))
                                    <div class="modal fade" id="myModal-{{ $order->id }}" tabindex="-1" role="dialog"
                                         aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Order</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" action="{{ url('buying_order/'.$order->id) }}"
                                                          class="" method="POST">
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        {{ csrf_field() }}
                                                        <p>Are You Sure?</p>
                                                        <button type="submit" class="btn btn-danger"
                                                                name='delete_modal'>Delete
                                                        </button>
                                                        <button type="button" class="btn btn-success"
                                                                data-dismiss="modal">Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$orders->appends($_GET)->links()}}
            </div>
        </div>
    </div>
@endsection

@section('scripts')


    <script>

        (function () {

            $("#product_id").select2({
                closeOnSelect: false,
                placeholder: 'Search...',
                // width: '350px',
                allowClear: true,
                ajax: {
                    url: '{{route('admin.getProducts')}}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });
        })();

    </script>


@endsection