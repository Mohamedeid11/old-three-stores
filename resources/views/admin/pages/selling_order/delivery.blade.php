@extends('admin.layout.main')
@section('content')
<style>
    table thead {background: #001587; color: white;}
    .dataTables_wrapper .dataTable th {border: solid 1px white; color: white; font-size: 12px; width: auto !important;}
    table th hr {border-color: white;}
    .dataTables_wrapper .dataTable tbody tr.odd {background: #a3d1ff;}
    table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before, 
    table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {content: "";}
</style>
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Delivery</h3>
            </div>

			<!--<div class="kt-portlet__head-toolbar">-->
   <!--         	<div class="kt-portlet__head-wrapper">-->
   <!--             	<div class="kt-portlet__head-actions">-->

			<!--		<div class="dropdown">-->
			<!--			<button class="btn btn-dark  d-none d-sm-inline-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" -->
			<!--				aria-expanded="false"><i class="fas fa-info"></i> Shipping Info-->
			<!--			</button>-->
			<!--			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">-->
							
			<!--				<button type="button" class="dropdown-item get_selected_orders_shiping_info" task="Shipping_Info" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-list"></i> Delivery List</button>-->
			<!--				<button type="button" class="dropdown-item get_selected_orders_shiping_info" task="Shipping_Products" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-list"></i> Inventory List</button>-->
			<!--			</div>-->
			<!--			@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))-->
			<!--			<a href="#" class="btn btn-danger d-block d-md-inline-block mb-1" data-toggle="modal" data-target="#myModalDALL"><i class="fas fa-trash"></i> Delete</a>-->
			<!--			@endif-->
			<!--			<a href="#" class="btn btn-warning d-block d-md-inline-block mb-1" data-toggle="modal" data-target="#myModalSTATUS"><i class="fas fa-check-square"></i> Orders Status</a>-->
			<!--			<a href="#" class="btn btn-success d-block d-md-inline-block mb-1" data-toggle="modal" data-target="#myModalREPALL"><i class="fas fa-user"></i> REP</a>-->
			<!--			<button type="button" class="btn btn-primary get_selected_orders_shiping_info  d-none d-sm-inline-block" task="Print_Invoice" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-file-invoice"></i> Ivoices</button>-->
			<!--			<a href="#" class="btn btn-info d-block d-md-inline-block mb-1" id="calculate_selected_orders_amount" task="CalculateTotalAmount" url="{{url('selling_order/orders_task')}}" -->
			<!--			data-toggle="modal" data-target="#myModalTotalAmount"><i class="fas fa-money-bill"></i> Total Amount</a>-->

			<!--		</div>-->
			<!--		<div class="modal fade" id="myModalTotalAmount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
			<!--			<div class="modal-dialog">-->
   <!-- 						<div class="modal-content">-->
   <!--     						<div class="modal-header">-->
   <!--     							<h5 class="modal-title">Selected Orders</h5>-->
   <!--     							<button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
   <!--     							<span aria-hidden="true">&times;</span>-->
   <!--     							</button>-->
   <!--     						</div>-->
   <!--     						<div class="modal-body">-->
   <!--     						    <div id="calcualte_selected_orders_amount"></div>-->
   <!--     						</div>-->
   <!-- 						</div>-->
			<!--			</div>-->
			<!--		</div>-->


			<!--		<div class="modal fade" id="myModalSTATUS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
			<!--			<div class="modal-dialog">-->
			<!--			<div class="modal-content">-->
			<!--			<div class="modal-header">-->
			<!--				<h5 class="modal-title">Orders Status</h5>-->
			<!--				<button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
			<!--				<span aria-hidden="true">&times;</span>-->
			<!--				</button>-->
			<!--			</div>-->
			<!--			<div class="modal-body">-->
			<!--			<div id="change_selected_orders_status_res"></div>-->
			<!--			<div class="form-group">-->
			<!--				<label>Status </label>-->
			<!--				<select class="form-control" name="orders_status" id="all_orders_status_seelctor">-->
			<!--					<option value="" disabled selected>Choose Status</option>-->
			<!--					@foreach ($statuss as $status)-->
			<!--					    @if($status->id != 1 && $status->id != 11)-->
			<!--						    <option value="{{$status->id}}">{{$status->title}}</option>-->
			<!--					    @endif-->
			<!--					@endforeach-->
			<!--				</select>-->
			<!--			</div>-->

			<!--			<button type="button" class="btn btn-info" id='change_selected_orders_status' task="Change_Status" url="{{url('selling_order/orders_task')}}">Save</button>-->
			<!--			<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--		</div>-->
   <!--                 @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))-->
			<!--		<div class="modal fade" id="myModalDALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
			<!--			<div class="modal-dialog">-->
			<!--			<div class="modal-content">-->
			<!--			<div class="modal-header">-->
			<!--				<h5 class="modal-title">Delete Selected Orders</h5>-->
			<!--				<button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
			<!--				<span aria-hidden="true">&times;</span>-->
			<!--				</button>-->
			<!--			</div>-->
			<!--			<div class="modal-body">-->
			<!--			<button type="button" class="btn btn-danger" id='delete_selected_orders' task="Delete" url="{{url('selling_order/orders_task')}}">Delete</button>-->
			<!--			<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--		</div>-->
   <!--                 @endif-->
			<!--		<div class="modal fade" id="myModalREPALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
			<!--			<div class="modal-dialog">-->
			<!--			<div class="modal-content">-->
			<!--			<div class="modal-header">-->
			<!--				<h5 class="modal-title">Orders REP</h5>-->
			<!--				<button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
			<!--				<span aria-hidden="true">&times;</span>-->
			<!--				</button>-->
			<!--			</div>-->
			<!--			<div class="modal-body">-->
			<!--			<div id="change_selected_orders_rep_res"></div>-->
			<!--			<div class="form-group">-->
			<!--				<label>REP </label>-->
			<!--				<select class="form-control" name="rep" id="all_orders_rep_seelctor">-->
			<!--					<option value="" disabled selected>Choose REP</option>-->
			<!--					<option value="0"></option>-->
			<!--					@foreach ($admins as $admin)-->
			<!--						<option value="{{$admin->id}}">{{$admin->name}}</option>-->
			<!--					@endforeach-->
			<!--				</select>-->
			<!--			</div>-->

			<!--			<button type="button" class="btn btn-info" id='change_selected_orders_rep' task="Change_REP" -->
			<!--			url="{{url('selling_order/orders_task')}}">Save</button>-->
			<!--			<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--			</div>-->
			<!--		</div>-->
						
   <!--                 </div>-->
   <!--             </div>-->
   <!--         </div>-->
        </div>
        <div class="kt-portlet__body">
			<form action="#" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3 d-none d-sm-inline-block">
							<div class="form-group">
								<label>Day</label>												
								<select class="form-control" name="day" id="day">
										<option value="All">All Days</option>
										@for ($day = 1; $day <= 31; $day++)
										<option value="{{$day}}" @if($selected_day == $day) selected @endif>{{$day}}</option>
										@endfor
								</select>
							</div>
						</div>
						<div class="col-md-3 d-none d-sm-inline-block">
							<div class="form-group">
								<label>Month</label>												
								<select class="form-control" name="month" id="month">
										<option value="All">All Months</option>
										@for ($month = 1; $month <= 12; $month++)
										<option value="{{$month}}" @if($selected_month == $month) selected @endif>{{date('F', strtotime($month.'/02/2020'))}}</option>
										@endfor
								</select>
							</div>
						</div>
						<div class="col-md-3  d-none d-sm-inline-block">
							<div class="form-group">
								<label>Year</label>												
								<select class="form-control" name="year" id="year">
										@if(date('Y') > 2020)
											<option value="All">All Years</option>
											@for ($year = 2020; $year <= date('Y'); $year++)
												<option value="{{$year}}" @if($selected_year == $year) selected @endif>{{$year}}</option>
											@endfor
										@else
											<option value="2020" @if($selected_year == 2020) selected @endif>2020</option>
										@endif
								</select>
							</div>
						</div>
						{{--
						<div class="col-md-3 col-6">
							<div class="form-group">
								<label>Order Status</label>												
								<select class="form-control" name="status" id="status">
								    <option value="" selected>Choose Status</option>
									<option value="All" @if($selected_status == "All") selected @endif>All Status</option>
									@foreach ($statuss as $status)
										<option value="{{$status->id}}" @if($status->id == $selected_status) selected @endif>{{$status->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						--}}
						<!--<div class="col-md-3 col-6">-->
						<!--	<div class="form-group">-->
						<!--		<label>REP</label>												-->
						<!--		<select class="form-control" name="admin" id="admin">-->
						<!--			<option value="All">All REPs</option>-->
						<!--			@foreach ($admins as $admin)-->
						<!--				<option value="{{$admin->id}}" @if($admin->id == $selected_admin) selected @endif>{{$admin->name}}</option>-->
						<!--			@endforeach-->
						<!--		</select>-->
						<!--	</div>-->
						<!--</div>-->
						<div class="col-md-3  d-none d-sm-inline-block">
							<div class="form-group">
								<label>City</label>												
								<select class="form-control" name="city" id="city">
									<option value="All">All Cities</option>
									@foreach ($cities as $city)
										<option value="{{$city->id}}" @if($city->id == $selected_city) selected @endif>{{$city->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3  d-none d-sm-inline-block">
							<div class="form-group">
								<label>Product</label>												
								<select class="form-control" name="product" id="product">
									<option value="All">All Products</option>
									@foreach ($products as $product)
										<option value="{{$product->id}}" @if($product->id == $selected_product) selected @endif>{{$product->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
								<label>Orders per Page</label>												
								<select class="form-control" name="orders_per_page" id="orders_per_page">
									<option value="All">All Orders</option>
									<option value="25" @if($orders_per_page == 25) selected @endif>25</option>
									<option value="50" @if($orders_per_page == 50) selected @endif>50</option>
									<option value="75" @if($orders_per_page == 75) selected @endif>75</option>
									<option value="100" @if($orders_per_page == 100) selected @endif>100</option>
								</select>
							</div>
						</div> -->
						<!--<div class="col-md-3">-->
						<!--	<label>Search</label>-->
						<!--	<input type="text" name="search" value="{{$search_filter}}" class="form-control" />-->
						<!--</div>-->
						<div class="col-md-3">
							<label class="control-label"><br /></label>
							<button type="submit" class="btn btn-success btn-block">Search</button>
						</div>	
					</div>	
				</div>
			</form>
			<div class="table-responsive">
			    <table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<!--<th class="disable_sort">-->
						<!--	<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">-->
						<!--		<input type="checkbox" id="checkAll">-->
						<!--		<span></span>-->
						<!--	</label>-->
						<!--</th>-->
						<th>#</th>
						<th>Client Name <hr /> Client Phone</th>
						<th>Status <hr /> REP</th>
						<th class="d-none d-sm-table-cell">Client Address</th>
						<th>{{-- Shipping Date <hr /> --}} City</th>
						<th class="d-none d-sm-table-cell">{{-- Items <hr /> --}} Total</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($orders as $order)
						<tr @if(!order_notes_stats($order->id)) class="notes_not_viewed" @endif>
							<!--<td>-->
							<!--	<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">-->
							<!--		<input type="checkbox" class="check_single" name="item[]" value="{{$order->id}}" />-->
							<!--		<span></span>-->
							<!--	</label>-->
							<!--</td>-->
							<td><b>{{$order->order_number}}</b></td>
							<td><b>{{$order->client_info->name}}</b> <hr /> {{$order->client_info->phone}}</td>
							<td>@if($order->status > 0) {{$order->status_info->title}} @else {{$statuss[0]->title}} @endif <hr /> @if($order->delivered_by > 0) {{$order->delivery_info->name}} @endif</td>
							<td class="d-none d-sm-table-cell">{{$order->address}}</td>
							<td>{{-- {{date('Y-m-d', strtotime($order->shipping_date))}} <hr /> --}} @if($order->city > 0) {{$order->city_info->title}} @endif</td>
							<td class="d-none d-sm-table-cell">{{-- PCS : {{$order->itemsq->sum('qty')}} <hr /> --}} {{$order->total_price + $order->shipping_fees}}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										<i class="fas fa-ellipsis-h"></i>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders'))
										<a class="dropdown-item" href="{{route('selling_order.edit', $order->id)}}">Edit</a>
										@endif
										<a class="dropdown-item" href="{{route('selling_order.show', $order->id)}}">Details</a>
										<a class="dropdown-item" href="{{url('/selling_order/orders_operation/Print_Invoice?orders='.$order->id)}}" target="_blank">Invoice</a>
										
										<a class="dropdown-item sellorder_notes_viewer" order-num="{{$order->id}}" data-toggle="modal" url="{{url('sellorder_notes_viewer')}}" href="#myNotes-{{ $order->id }}">Notes</a>
										@if($order->time_lines->count() > 0)
										<a class="dropdown-item" data-toggle="modal" href="#myTime-{{ $order->id }}">Time Line</a>
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $order->id }}">Delete</a>
										@endif
									</div>
								</div>
								<div class="modal fade" id="myNotes-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Order Notes</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
								<form role="form" action="{{ url('selling_order_notes/'.$order->id) }}" class="" method="POST" id="ajsuformreload">
								{{ csrf_field() }}
								<div id="ajsuform_yu"></div>
								<div class="form-group">
									<textarea name="note" class="form-control"></textarea>
								</div>
								<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
								</form>
								</div>
								</div>
								</div>
								</div>


                                <div class="modal fade" id="myTime-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    								<div class="modal-dialog modal-lg">
    								<div class="modal-content">
    								<div class="modal-header">
    									<h5 class="modal-title">Order Timeline</h5>
    									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    									<span aria-hidden="true">&times;</span>
    									</button>
    								</div>
    								<div class="modal-body time_line_list">
    								<ul>
    								    @foreach ($order->time_lines as $line)
    								    <li class="row">
    								        <div class="col-md-3"><b>{{date('Y-m-d h:i A', strtotime($line->created_at))}}</b></div>
    								        <div class="col-md-9">{{$line->admin_info->name.$line->text}}</div>
    								    </li>
    								    @endforeach
    								</ul>
    								    
    								
    								</div>
    								</div>
    								</div>
								</div>
								
								@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))
								<div class="modal fade" id="myModal-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete Order</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('selling_order/'.$order->id) }}" class="" method="POST">
								<input name="_method" type="hidden" value="DELETE">
								{{ csrf_field() }}
								<p>Are You Sure?</p>
								<button type="submit" class="btn btn-danger" name='delete_modal'>Delete</button>
								<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
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
			</div>
			@if($orders_per_page > 0)
				{{$orders->appends($_GET)->links()}}
			@endif
        </div>
	</div>
</div>					
@endsection