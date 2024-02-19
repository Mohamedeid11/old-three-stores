@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-plus-square"></i></span>
                <h3 class="kt-portlet__head-title">Create Buying Order</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('buying_order')}}" enctype="multipart/form-data" id="ajsuform">
				{{csrf_field()}}
				<div id="ajsuform_yu"></div>
				<div class="form-group" id="client_finder">
					<div class="row">
						<div class="col-md-12">	
							<label>Find Agent</label>
							<select class="form-control agents_selector" name="client" id="client">
								<option value="" disabled selected>Choose Agent</option>
								@foreach ($agents as $agent)
									<option value="{{ $agent->id }}">{{ $agent->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				
				<hr />
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">	
							<label>Order NUmber</label>												
							<input class="form-control" type="text" readonly placeholder="order number" name="masd" value="{{$order_number}}"  />
						</div>
						{{-- <div class="col-md-6">	
							<label>Order Number</label>												
							<input class="form-control" type="text" placeholder="Order Number" name="order_number" value="{{$order_number}}"  />
						</div> --}}
						<div class="col-md-3">	
							<label>Invoice Date</label>												
							<input class="form-control" type="date" placeholder="Invoice Date" name="shipping_date" value="{{date('Y-m-d')}}"  />
						</div>
						<div class="col-md-6">	
							<label>Order Invoice</label>												
							<input class="form-control" type="file" name="order_invoice"  />
						</div>

						<div class="col-md-6">
							<label>Payment Status</label>

							<select class="form-control" id='payment_status' name="payment_status"  >
								<option value="not_paid">Not Paid</option>
								<option value="paid"> Paid</option>
								<option value="partly_paid">Partly Paid </option>


							</select>
						</div>

						<div class="col-md-6" id="payment_amount_container">


						</div>
					</div>
				</div>
				{{-- <div class="form-group">
					<div class="row">
						
					</div>
				</div> --}}
				<h3 class="selling_order_products_title">
					<i class="fas fa-list"></i> Order Items
				</h3>
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<td colspan="2">Product Name</td>
								<td>Color</td>
								<td>Size</td>
								<td>Qty</td>
								<td>Price</td>
								<td></td>
							</tr>
						</thead>
						<tbody id="order_products">
							<tr>
								<td></td>
								<td>
									<input type="hidden" name="order_item[]" value="2" />
									<select class="order_product_item form-control" name="product[]" id="order_item_{{$first_box_id}}"
									options-url="{{url('product_options')}}" item-id="{{$first_box_id}}">
										<option value="" disabled selected>Choose Order Item</option>
										@foreach ($products as $product)
											<option value="{{$product->id}}">{{$product->title}}</option>
										@endforeach
										<!-- <option value="0">New Product</option> -->
									</select>
									<input type="hidden" name="note[]" value="" />
								</td>
								<td id="color_order_item_{{$first_box_id}}">

								</td>
								<td id="size_order_item_{{$first_box_id}}">

								</td>
								<td>
									<input type="number" step="1" id="buyorder_qty_{{$first_box_id}}" name="qty[]" class="form-control buyorder_items_qty" data-url="{{ url('calculate_buyorder_qtys') }}"
									item-id="{{$first_box_id}}"/>
								</td>
								<td>
									<input type="number" step="0.01" name="price[]" class="form-control buyorder_items_price" data-url="{{ url('calculate_buyorder_qtys') }}" id="buyorder_price_{{$first_box_id}}"
									item-id="{{$first_box_id}}" />
								</td>
								<td id="buyorder_subtotal_{{$first_box_id}}">

								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4"></td>
								<td id="buyorder_qty"></td>
								<td></td>
								<td id="buyorder_total"></td>
							</tr>
						</tfoot>
					</table>
					<button type="button" class="btn btn-success w-100" id="add_order_item" item-type="buy"
					button-url="{{url('add_order_item')}}">Add</button>
				</div>


				<div class="row form-group">
					<div class="col-md-12">
						<label>Note</label>												
						<textarea class="form-control" placeholder="Note" name="order_note"></textarea>
					</div>
				</div>
				
				<div class="kt-portlet__foot">
					<div class="kt-form__actions text-right">
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>					
@endsection

@section('scripts')
	<script>
		$(document).on('change','#payment_status',function (){
			var input=`
							<label>Payment Amount </label>
							<input required class="form-control" type="number" min="0" accept="any" placeholder="" name="payment_amount" id="payment_amount" value="0"  />

			`;
			var payment_status=$(this).val();
			if (payment_status=='partly_paid'){
				$('#payment_amount_container').html(input);
			}
			else {
				$('#payment_amount_container').html('');

			}
		});
	</script>

@endsection