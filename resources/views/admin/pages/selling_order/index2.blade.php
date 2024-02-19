@extends('admin.layout.main')
@section('styles')
<link rel="stylesheet" href="{{asset('tagsinput/tagsinput.css')}}">

<style>
    table thead {background: #001587; color: white;}
    .dataTables_wrapper .dataTable th {border: solid 1px white; color: white; font-size: 12px; width: auto !important;}
    table th hr {border-color: white;}
    .dataTables_wrapper .dataTable tbody tr.odd {background: #a3d1ff;}
    table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before, 
    table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {content: "";}
</style>
@endsection
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Selling Orders</h3>
            </div>

			<div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions d-flex mx-5">

					<div class="dropdown mr-3">
						<button class="btn btn-warning d-sm-inline-block" type="button" id="dropdownMenuButtonA" data-toggle="dropdown" aria-haspopup="true" 
							aria-expanded="false"><i class="fas fa-cogs"></i> Action
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButtonA">
							<a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalSTATUS" id="selling_order_changing_status"><i class="fas fa-check-square"></i> Orders Status</a>
							<a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalREPALL"><i class="fas fa-user"></i> REP</a>
							<a href="#" class="dropdown-item" id="calculate_selected_orders_amount" task="CalculateTotalAmount" url="{{url('selling_order/orders_task')}}" 
							data-toggle="modal" data-target="#myModalTotalAmount"><i class="fas fa-money-bill"></i> Total Amount</a>
							<a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModaTags" id="selling_order_changing_tags"><i class="fas fa-list"></i> Bulk Tags</a>
							<a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalNOTE"><i class="fas fa-comment"></i> Orders Notes</a>
							@if($delete_selling_order)
								<a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalDALL"><i class="fas fa-trash"></i> Delete</a>
							@endif
						</div>
					</div>

					<div class="dropdown mr-5">
						<button class="btn btn-dark  d-none d-sm-inline-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
							aria-expanded="false"><i class="fas fa-print"></i> Print
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<button type="button" class="dropdown-item get_selected_orders_shiping_info" task="Shipping_Info" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-list"></i> Delivery List</button>
							<button type="button" class="dropdown-item get_selected_orders_shiping_info" task="Shipping_Products" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-list"></i> Inventory List</button>
							<button type="button" class="dropdown-item get_selected_orders_shiping_info" task="Print_Mylerz_Invoice" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-file-invoice"></i> Mylerz Ivoices</button>
							<button type="button" class="dropdown-item get_selected_orders_shiping_info mb-1  d-none d-sm-inline-block" task="Print_Invoice" url="{{url('selling_order/orders_operation')}}"><i class="fas fa-file-invoice"></i> Invoices</button>
						</div>
					</div>
					<div class="modal fade" id="myModalTotalAmount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
    						<div class="modal-content">
        						<div class="modal-header">
        							<h5 class="modal-title">Selected Orders</h5>
        							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
        							<span aria-hidden="true">&times;</span>
        							</button>
        						</div>
        						<div class="modal-body">
        						    <div id="calcualte_selected_orders_amount"></div>
        						</div>
    						</div>
						</div>
					</div>

					<div class="modal fade" id="myModalNOTE" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Orders Notes</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<div id="change_selected_orders_notes_res"></div>
						<form role="form" action="{{ url('selling_order_notes_multi') }}" class="" method="POST" id="ajsuformreload_mnotes">
							{{ csrf_field() }}
							<div id="ajsuform_yu_mnotes"></div>
							<input type="hidden" id="selected_note_orders" name="order" />
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
							<div class="form-group">
								<label>Tags </label>
								<select name="tag[]" multiple class="d-block form-control orders_selector_mul_tags">
									@foreach ($tags as $sa)
									<option value="{{$sa->id}}">{{$sa->title}}</option>
									@endforeach
								</select>
							</div>

							<button type="button" class="btn btn-info" id='create_mutiple_orders_notes'>Save</button>
							<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</form>
						</div>
						</div>
						</div>
					</div>

					<div class="modal fade" id="myModalSTATUS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Orders Status</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<div id="change_selected_orders_status_res"></div>
						<div class="form-group">
							<label>Status </label>
							<select class="form-control" name="orders_status" id="all_orders_status_seelctor">
								<option value="" disabled selected>Choose Status</option>
								@foreach ($statuss as $status)
								    @if($status->id != 1 && $status->id != 11)
									    <option value="{{$status->id}}">{{$status->title}}</option>
								    @endif
								@endforeach
							</select>
						</div>

						<button type="button" class="btn btn-info" id='change_selected_orders_status' task="Change_Status" url="{{url('selling_order/orders_task')}}">Save</button>
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</div>
						</div>
						</div>
					</div>

					<div class="modal fade" id="myModaTags" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Orders Tags</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<div id="change_selected_orders_tags_res"></div>
						<div class="form-group">
							<label>Tags</label>
							<select class="form-control" name="orders_tags[]" id="all_orders_tags_seelctor" multiple>
								@foreach ($all_tags as $tag)
									<option value="{{$tag->id}}">{{$tag->title}}</option>
								@endforeach
							</select>
						</div>

						<button type="button" class="btn btn-info" id='change_selected_orders_tags' task="Change_Tags" url="{{url('selling_order/orders_task')}}">Save</button>
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</div>
						</div>
						</div>
					</div>
                    @if($delete_selling_order)
					<div class="modal fade" id="myModalDALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Delete Selected Orders</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<button type="button" class="btn btn-danger" id='delete_selected_orders' task="Delete" url="{{url('selling_order/orders_task')}}">Delete</button>
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</div>
						</div>
						</div>
					</div>
                    @endif
					<div class="modal fade" id="myModalREPALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Orders REP</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<div id="change_selected_orders_rep_res"></div>
						<div class="form-group">
							<label>REP </label>
							<select class="form-control" name="rep" id="all_orders_rep_seelctor">
								<option value="" disabled selected>Choose REP</option>
								<option value="0"></option>
								@foreach ($admins as $admin)
									<option value="{{$admin->id}}">{{$admin->name}}</option>
								@endforeach
							</select>
						</div>

						<button type="button" class="btn btn-info" id='change_selected_orders_rep' task="Change_REP" 
						url="{{url('selling_order/orders_task')}}">Save</button>
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</div>
						</div>
						</div>
					</div>
						
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form action="{{url('selling_order_search/home_search')}}" method="get" class="kt-form" id="selling_order_search_form">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3 d-none d-sm-inline-block">
							<div class="form-group">
								<label>From</label>												
								<input type="date" class="form-control" name="from_date" id="from_date" value="{{$from_date}}" />
							</div>
						</div>
						<div class="col-md-3 d-none d-sm-inline-block">
							<div class="form-group">
								<label>To</label>				
								<input type="date" class="form-control" name="to_date" id="to_date" value="{{$to_date}}" />
							</div>
						</div>
						<div class="col-md-6 col-6">
							<div class="form-group">
								<label>Order Status</label>												
								<select class="form-control" name="status[]" id="status_select2" multiple>
									<option value="All" @if(in_array("All", $selected_status)) selected @endif>All Status</option>
									@foreach ($statuss as $status)
										<option value="{{$status->id}}" @if(in_array($status->id, $selected_status)) selected @endif>{{$status->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 d-none d-sm-inline-block">
							<div class="form-group" id="order_number_gr">
								<label>Order Number / Shipping Number</label>												
								<input type="text" class="form-control" name="order_number" id="order_number"  data-role="tagsinput" />
							</div>
						</div>

						<div class="col-md-3 col-6">
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
						<div class="col-md-3 d-none d-sm-inline-block">
							<div class="form-group">
								<label>Moderator </label>
								<select name="moderator[]" multiple class="d-block form-control orders_selector_mul_reps" id="moderators_select2">
									@foreach ($moderators as $admin)
									<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_moderator)) selected @endif>{{$admin->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-md-12" id="order_number_gr">
							<label>Tags</label>
							<select class="form-control" name="tags[]" id="dashboard_order_tag" multiple>
								@foreach ($all_tags as $tag)
									<option value="{{$tag->id}}" @if(in_array($tag->id, $selected_tags)) selected @endif>{{$tag->title}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-11 col-6">
							<div class="form-group">
								<label>Product</label>												
								<select class="form-control" name="product[]" id="dashboard_product_item" multiple>
									@foreach ($all_products as $cat)
									    <optgroup label="{{$cat->title}}">
									        @foreach ($cat->sub_cats() as $scat)
									            @foreach($scat->products() as $product)
        									        <option value="{{$product->id}}" @if(in_array($product->id, $selected_product)) selected @endif>{{$product->title}}</option>
	                                            @endforeach
	                                        @endforeach
									    </optgroup>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-1">
							<label class="control-label"><br /></label>
							<button type="submit" class="btn btn-success btn-block"><i class="fas fa-search"></i></button>
						</div>	
					</div>	
				</div>
			</form>
			<div class="table-responsive" id="selling_order_results"></div>
        </div>
	</div>
</div>					
@endsection

@section('scripts')
<script src="{{ asset('tagsinput/tagsinput.js')}}"></script>

<script>
$(document).ready(function() { 

    $('body').on('keyup', function(event){
        if(event.ctrlKey && event.key === 'i')
        {
            $('#order_number_gr .bootstrap-tagsinput input').focus();
        }
    });
    $('body').on('submit', '#selling_order_search_form', function(e){
        e.preventDefault(); 
        $('#selling_order_results').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');	
        var action = $(this).attr('action');
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var admin_select2 = $('#admin_select2').val();
        var status_select2 = $('#status_select2').val();
        var order_number = $('#order_number').val();
        var products = $('#dashboard_product_item').val();
		var tags = $('#dashboard_order_tag').val();
		var moderators_select2 = $('#moderators_select2').val();
		$.ajax({
            type: 'GET',
            data: {from_date: from_date, to_date: to_date, status: status_select2, admin: admin_select2, order_number: order_number, products: products,
				moderators: moderators_select2, tags: tags},
            url: action,
            success: function(data) 
            {
                $('#selling_order_results').html(data);
				$('.orders_selector_mul_reps').select2({placeholder: "Select Rep"});
		        $('.orders_selector_mul_tags').select2({placeholder: "Select Tags"});
                $('#kt_table_2').DataTable({"iDisplayLength": 100,
                    columnDefs: [ {
                        orderable: false,
                        className: 'checkbox',
                        targets:   0
                    },
                    {
                        orderable: true,
                        className: '',
                        targets:   1,
                        type: "numeric"
                    } ],
                    select: {
                        style:    'os',
                        selector: 'td:first-child'
                    },
                    order: [[ 1, 'asc' ]]
                });
            }
        });
        return false;
    });
 });
</script>
@endsection