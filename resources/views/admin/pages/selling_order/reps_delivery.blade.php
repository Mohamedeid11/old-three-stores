@extends('admin.layout.main')
@section('content')
<style>
    table thead {background: #001587; color: white;}
    .dataTables_wrapper .dataTable th {border: solid 1px white; color: white; font-size: 12px; width: auto !important;}
    table th hr {border-color: white;}
    .dataTables_wrapper .dataTable tbody tr.odd {background: #a3d1ff;}
    table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before, 
    table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {content: "";}
    .dataTables_wrapper .dataTable tbody tr.bg-shipped, tbody tr.bg-shipped {background-color: #cedaff !important; }
    .dataTables_wrapper .dataTable tbody tr.bg-delivered, tbody tr.bg-delivered {background-color: #ceffd0 !important; }
    .dataTables_wrapper .dataTable tbody tr.bg-rejected, tbody tr.bg-rejected {background-color: #ffd7ce !important; }
</style>
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Delivery Report</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			@if(Auth::guard('admin')->user()->position == 1)
			<form action="#" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
					    <div class="col-md-9 col-9">
							<div class="form-group">
								<label>REP</label>												
								<select class="form-control" name="admin[]" id="admin_select2" multiple>
									<option value="All">All REPs</option>
									@foreach ($admins as $admin)
										<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_admin)) selected @endif>{{$admin->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<label class="control-label"><br /></label>
							<button type="submit" class="btn btn-success btn-block">Search</button>
						</div>	
					</div>	
				</div>
			</form>
			@endif
			<hr />
			<div id="orders_stats" class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-6">
                        <p class="stat_title text-primary">Delivered Total</p>
                        <p class="stat_amount_wl text-primary">{{number_format($all_orders->where('status', 5)->sum('total_price') + $all_orders->where('status', 5)->sum('shipping_fees')-$all_orders->where('status', 5)->sum('payment_amount'))}}</p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <p class="stat_title text-warning">Shipped</p>
                        <p class="stat_amount_wl text-warning">{{$all_orders->where('status', 4)->count()}}</p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <p class="stat_title text-success">Delivered</p>
                        <p class="stat_amount_wl text-success">{{$all_orders->where('status', 5)->count()}}</p>
                    </div>
                    <div class="col-sm-3 col-6">
                        <p class="stat_title text-danger">Rejected</p>
                        <p class="stat_amount_wl text-danger">{{$all_orders->where('status', 7)->count()}}</p>
                    </div>

                </div>
            </div>
            <hr />
            
			<div class="table-responsive">
			    <!-- <table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_1"> -->
			    <table class="table table-striped table-bordered table-hover table-checkable">
				<thead>
					<tr>
						<th>#</th>
						<th>Client Name <hr /> Client Phone</th>
						<th>Status <hr /> REP</th>
						<th>Client Location & Address <hr /> Last Note</th>
						<th>City <hr /> Total</th>
						<th>Action <hr /> Call</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($orders as $order)
						<tr @if($order->status == 4) class="bg-shipped" @elseif($order->status == 5) class="bg-delivered" @elseif($order->status == 7) class="bg-rejected" @endif>
							<td><b>{{$order->order_number}}</b></td>
							<td><b>{{$order->client_info->name}}</b> <hr /> {{$order->client_info->phone}}</td>
							<td>@if($order->status > 0) {{$order->status_info->title}} @else {{$statuss[0]->title}} @endif <hr /> @if($order->delivered_by > 0) {{$order->delivery_info->name}} @endif</td>
							<td>
							    @if($order->location != '')
							        <a target="_blank" href="{{ $order->location }}"><i class="fas fa-map-marker-alt  text-danger fa-2x mr-3"></i></a>
							    @elseif($order->client_info->location != '')
							        <a target="_blank" href="{{ $order->client_info->location }}"><i class="fas fa-map-marker-alt  text-danger fa-2x mr-3"></i></a>
							    @endif
							    {{$order->address}}
							    <hr />
							    @if(count($order->notes) > 0) {{$order->notes[count($order->notes) - 1]->note}} @endif
							</td>
							<td>@if($order->city > 0) {{$order->city_info->title}} @endif <hr /> {{$order->total_price + $order->shipping_fees - $order->payment_amount}}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										<i class="fas fa-ellipsis-h"></i>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a class="dropdown-item" href="{{url('selling_order/delivered/'.$order->id)}}">Delivered</a>
										<a class="dropdown-item" href="{{url('selling_order/rejected/'.$order->id)}}">Rejected</a>
							            <a class="dropdown-item" data-toggle="modal" href="#OrderLocation-{{ $order->id }}">Order Location</a>
										<a class="dropdown-item sellorder_notes_viewer" order-num="{{$order->id}}" data-toggle="modal" url="{{url('sellorder_notes_viewer')}}" href="#myNotes-{{ $order->id }}">Notes</a>
									</div>
								</div>
								<hr />
								<a href="tel:{{$order->client_info->phone}}"><i class="fas fa-phone fa-flip-horizontal text-success fa-2x mr-3"></i></a> 
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
								    <label>Note</label>
									<textarea name="note" class="form-control"></textarea>
								</div>
								<div class="form-group">
								    <label>Rep </label>
									<select name="rep[]" multiple class="d-block form-control orders_selector_mul_reps">
									    @foreach ($repps as $sa)
									    <option value="{{$sa->id}}">{{$sa->name}}</option>
									    @endforeach
									</select>
								</div>
								<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
								</form>
								</div>
								</div>
								</div>
								</div>
								

								<div class="modal fade" id="OrderLocation-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Order Location</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								
								<form role="form" action="{{ url('order_location/'.$order->id) }}" class="ajsuformreditloc" method="POST" data-num="{{$order->id}}">
								{{ csrf_field() }}
								<div id="ajsuform_yu_loc_{{$order->id}}"></div>
								<div class="form-group">
									<textarea name="location" class="form-control">@if($order->location != ''){{$order->location}}@elseif($order->client_info->location != ''){{$order->client_info->location}}@endif</textarea>
								</div>

								<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
								</form>
								
								</div>
								</div>
								</div>
								</div>
								
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			</div>
        </div>
        {{$orders->appends($_GET)->links()}}
	</div>
</div>					
@endsection