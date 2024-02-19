@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Edit Buying Order</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('buying_order/'.$order->id)}}" enctype="multipart/form-data" id="ajsuform">
				{{csrf_field()}}
				<input type="hidden" name="type" value="{{$order->type}}">
				<div id="ajsuform_yu"></div>
				<input type="hidden" name="_method" value="PUT" />
				<div class="form-group" id="client_finder">
					<div class="row">
						<div class="col-md-12">	
							<label>Find Agent</label>												
							<select class="form-control agents_selector" name="client" id="client">
								<option value="" disabled selected>Choose Agent</option>
								@foreach ($agents as $agent)
									<option value="{{ $agent->id }}" @if($agent->id == $order->agent) selected @endif>{{ $agent->name }}</option>
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
							<input class="form-control" type="text" readonly placeholder="order number" name="masd" value="{{$order->id}}"  />
						</div>
						<div class="col-md-3">	
							<label>Invoice Date</label>												
							<input class="form-control" type="date" placeholder="Invoice Date" name="shipping_date" value="{{date('Y-m-d', strtotime($order->shipping_date))}}"  />
						</div>
						<div class="col-md-4">	
							<label>Order Invoice</label>												
							<input class="form-control" type="file" name="order_invoice"  />
						</div>
                        <div class="col-md-2">	
                            @if($order->invoice != '')
                                <a href="{{asset($order->invoice)}}" class="btn btn-dark" target="_blank">Invoice</a>
                            @endif
                        </div>

						<div class="col-md-6">
							<label>Payment Status</label>

							<select class="form-control" id='payment_status' name="payment_status"  >
								@if($order->type=='invoice')
								<option @if($order->payment_status=='not_paid') selected  @endif value="not_paid">Not Paid</option>
								<option @if($order->payment_status=='paid') selected  @endif value="paid"> Paid</option>
								<option @if($order->payment_status=='partly_paid') selected  @endif value="partly_paid">Partly Paid </option>
                                       @else
									<option @if($order->payment_status=='partly_paid') selected  @endif value="partly_paid">Partly Paid </option>

								@endif

							</select>
						</div>

						<div class="col-md-6" id="payment_amount_container">
							@if($order->payment_status=='partly_paid')
								<label>Payment Amount </label>
								<input required class="form-control" type="number" min="0" accept="any" placeholder="" name="payment_amount" id="payment_amount" value="{{$order->payment_amount}}"  />
							@endif
						</div>

					</div>
				</div>
				@if($order->type=='invoice')

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
							@php
								$qty = 0;
								$total = 0;
								$subtotal = 0;
							@endphp
							@foreach ($order->items as $item)
							<tr id="single_order_item_box_ABCD{{$item->id}}">
								<td>
									@if($loop->index > 0)
									<button type="button" class="btn btn-danger btn-sm btn-block buy_delete_order_item" box="ABCD{{$item->id}}"><i class="fas fa-trash-alt"></i></button>
									@endif
								</td>
								<td>
									<input type="hidden" name="order_item[]" value="{{ $item->id }}" />
									<select class="order_product_item form-control" name="product[]" id="order_item_ABCD{{$item->id}}"
									options-url="{{url('product_options')}}" item-id="ABCD{{$item->id}}">
										<option value="" disabled selected>Choose Order Item</option>
										@foreach ($products as $product)
											<option value="{{$product->id}}" @if($item->product == $product->id) selected @endif>{{$product->title}}</option>
										@endforeach
										<!-- <option value="0">New Product</option> -->
									</select>
									<input type="hidden" name="note[]" value="" />
								</td>
								<td id="color_order_item_ABCD{{$item->id}}">
									@if(count($item->product_info->colors) > 0)
										<select class="form-control item_color_selector" name="color[]" 
											id="item_color_selectorABCD{{$item->id}}">
												<option value="" disabled selected>Choose Color</option>
											<?php
											foreach ($item->product_info->colors as $color)
											{
												?>
												<option value="<?php echo $color->color_info->id; ?>" @if($color->color_info->id == $item->color) selected @endif><?php echo $color->color_info->title; ?></option>
												<?php
											}
											?>
										</select>
									@else
										<input type="hidden" name="color[]" value="0" />
									@endif
								</td>
								<td id="size_order_item_ABCD{{$item->id}}">
									@if(count($item->product_info->sizes) > 0)
										<select class="form-control item_size_selector" name="size[]" 
											id="item_size_selectorABCD{{$item->id}}">
												<option value="" disabled selected>Choose Size</option>
											<?php
											foreach ($item->product_info->sizes as $size)
											{
												?>
												<option value="<?php echo $size->size_info->id; ?>" @if($size->size_info->id == $item->size) selected @endif><?php echo $size->size_info->title; ?></option>
												<?php
											}
											?>
										</select>
									@else
										<input type="hidden" name="size[]" value="0" />
									@endif
								</td>
								<td>
									<input type="number" step="1" id="buyorder_qty_ABCD{{$item->id}}" name="qty[]" class="form-control buyorder_items_qty" data-url="{{ url('calculate_buyorder_qtys') }}"
									item-id="ABCD{{$item->id}}" value="{{ $item->qty }}" />
								</td>
								<td>
									<input type="number" step="0.01" name="price[]" class="form-control buyorder_items_price" data-url="{{ url('calculate_buyorder_qtys') }}" id="buyorder_price_ABCD{{$item->id}}"
									item-id="ABCD{{$item->id}}" value="{{ $item->price }}" />
								</td>
								<td id="buyorder_subtotal_ABCD{{$item->id}}">
									{{ $item->qty * $item->price }}
									@php
									$qty = $item->qty + $qty;
									$total = $total + ($item->qty * $item->price);
									@endphp
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4"></td>
								<td id="buyorder_qty">{{ $qty }}</td>
								<td></td>
								<td id="buyorder_total">{{ $total }}</td>
							</tr>
						</tfoot>
					</table>
					<button type="button" class="btn btn-success w-100" id="add_order_item" item-type="buy"
					button-url="{{url('add_order_item')}}">Add</button>
				</div>

				<div class="row form-group">
					<div class="col-md-12">
						<label>Note</label>												
						<textarea class="form-control" placeholder="Note" name="order_note">{{$order->note}}</textarea>
					</div>
				</div>
				@endif


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
							<input required class="form-control" type="number" min="0" accept="any" placeholder="" name="payment_amount" id="payment_amount" value="{{$order->payment_amount}}"  />

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