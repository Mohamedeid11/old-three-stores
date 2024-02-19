@extends('admin.layout.main')
@section('styles')
<link rel="stylesheet" href="{{asset('tagsinput/amsify.suggestags.css')}}">
@endsection

@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Edit Selling Order</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('selling_order/'.$order->id)}}" enctype="multipart/form-data" id="ajsuform">
				{{csrf_field()}}
				<div id="ajsuform_yu"></div>
				<input type="hidden" name="_method" value="PUT" />
				<div class="form-group" id="client_finder">
					<div class="row">
						<div class="col-md-12">
							<label>Find Client</label>
							<input class="form-control" type="text" placeholder="Find Client" name="client_search" value="{{$order->client_info->phone}}" id="client_search"
							data-url="{{url('find_client')}}" />
						</div>
					</div>
				</div>
				<div class="form-group" id="order_client_info">
					<div id='cient_finder_data'>
						<input type="hidden" name="client" value="{{$order->client_info->id}}" />
						<h3>Client Details</h3>
						<div class='row'>
							<div class="col-md-4">
							    <div class="form-group">
    								<label>Name</label>
    								<input class="form-control" type="text"  placeholder="Name" value="{{$order->client_info->name}}" id="name" name="name" />
							    </div>
							</div>
							<div class="col-md-4">
							    <div class="form-group">
    								<label>Phone No.</label>
    								<input class="form-control" type="text"  placeholder="Phone No." value="{{$order->client_info->phone}}" id="phone" name="phone" />
							    </div>
							</div>
							<div class="col-md-4">
							    <div class="form-group">
    								<label>Email</label>
    								<input class="form-control" type="text"  placeholder="Email" value="{{$order->client_info->email}}" id="email" name="email" />
							    </div>
							</div>
						</div>
						<div class='row'>
							<div class="col-md-4">
							    <div class="form-group">
								<label>Address</label>
								<input class="form-control" type="text"  placeholder="Address" value="{{$order->address}}" id="address" name="address" />
							</div>
							</div>
							<div class="col-md-4">
							    <div class="form-group">
    								<label>City</label>
    								<select class="form-control" name="city" id="client_city_selector" shipping-url="{{url('shipping_price_info')}}">
    									<option value="" disabled selected>Choose City</option>
    									@foreach ($cities as $city)
    										<option value="{{$city->id}}" @if($order->city == $city->id) selected @endif>{{$city->title}}</option>
    									@endforeach
    								</select>
    							</div>
							</div>
							<div class="col-md-4">
							    <div class="form-group">
                                    <label>Shipping Price (EGP)</label>
                                    <input class="form-control" type="text"  placeholder="Shipping Price" value="{{$order->shipping_fees}}" id="order_ship_price" name="ship_price" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input class="form-control" type="text"  placeholder="Order Location" id="order_location" name="location" value="{{$order->location}}" />
                                </div>
                            </div>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label>Order Number</label>
							<input class="form-control" type="text" placeholder="Order Number" name="order_number" value="{{$order->order_number}}"  />
							<input class="form-control" type="hidden" readonly disabled placeholder="Order Number" name="order_number_xx" value="{{$order->order_number}}"  />
						</div>
						<div class="col-md-3">
							<label>Shipping Number</label>
							<input class="form-control" type="text" placeholder="Shipping Number" name="shipping_number" id="shipping_number" value="{{$order->shipping_number}}"  />
						</div>
						<div class="col-md-4">
							<label>Order Date</label>
							<input class="form-control" type="date" placeholder="Order Date" name="shipping_date" value="{{date('Y-m-d', strtotime($order->created_at))}}"  />
						</div>
{{--						<div class="form-group col-md-12 mt-3" id="order_number_gr">--}}
{{--							<label>Tags</label>--}}
{{--							<input type="text" class="form-control" name="tags" id="order_tags"  data-role="tagsinput" value="{{$all_tags}}" />--}}
{{--						</div>--}}

						<div class="form-group col-md-3 mt-3" >
							<label>Tags</label>

							<select class="js-example-basic-multiple" id='tag_id' name="tag_id[]" multiple style='width:100%;'>
								@foreach($tags as $tag)
									<option @foreach($tag_ides as $pivot) @if($pivot==$tag->id) selected  @endif  @endforeach value="{{$tag->id}}">{{$tag->title??''}}</option>
								@endforeach


							</select>
						</div>
                         @if(permission_checker(Auth::guard('admin')->user()->id, 'update_moderator') )
							<div class="form-group col-md-3 mt-3">
							<label>Moderator</label>
							<select name="moderator" class="form-control" id="order_category_selector">
								@foreach ($admins as $admin)
									<option value="{{$admin->id}}" @if($order->added_by==$admin->id) selected   @endif   >{{$admin->name}}</option>
								@endforeach
							</select>
						</div>
						@endif



						<div class="form-group col-md-3 mt-3">
							<label>Payment Status</label>

							<select class="form-control" id='payment_status' name="payment_status"  >
								<option @if($order->payment_status=='not_paid') selected  @endif value="not_paid">Not Paid</option>
								<option @if($order->payment_status=='paid') selected  @endif value="paid"> Paid</option>
								<option @if($order->payment_status=='partly_paid') selected  @endif value="partly_paid">Partly Paid </option>


							</select>
						</div>

						<div class="form-group col-md-3 mt-3" id="payment_amount_container">
							@if($order->payment_status=='partly_paid')
								<label>Payment Amount </label>
								<input required class="form-control" type="number" min="0" accept="any" placeholder="" name="payment_amount" id="payment_amount" value="{{$order->payment_amount}}"  />
								@endif
						</div>









					</div>
				</div>
				<h3 class="selling_order_products_title">
					<i class="fas fa-list"></i> Order Items
				</h3>
				<ol id="order_products">
					@foreach ($order->items as $item)
						<li class="single_order_item_x" id="single_order_item_box_ABCD{{$item->id}}">
							<input type="hidden" name="order_item[]" value="ABCD{{$item->id}}" />
							<input type="hidden" name="order_item_id[]" value="{{$item->id}}" />
							<div class="order_item_details container-fluid">
								<div class="row mb-1">
									<div class="col-md-3">
										<label class="d-none">Product</label>
										<select class="order_product_item form-control" name="product[]" id="order_item_ABCD{{$item->id}}"
										options-url="{{url('product_options')}}" item-id="ABCD{{$item->id}}"
										price-url="{{url('product_price')}}" available-url="{{url('product_available_units')}}">
											<option value="" disabled selected>Choose Order Item</option>
											@foreach ($products as $product)
												<option value="{{$product->id}}" @if($product->id == $item->product) selected @endif>{{$product->title}}</option>
											@endforeach
											<!-- <option value="0">New Product</option> -->
										</select>
									</div>
									<div class="col-md-2">
										<label class="d-none">QTY</label>
										<input class="form-control sell_order_qty" type="number" placeholder="QTY" name="qty[]" value="{{$item->qty}}"
										id="sell_order_qty_ABCD{{$item->id}}" item-id="ABCD{{$item->id}}" price-url="{{url('product_price')}}" />
									</div>
									<div id="order_item_options_ABCD{{$item->id}}" class="col-md-4">
										@if(count($item->product_info->colors) > 0 || count($item->product_info->sizes) > 0)
											<div class="row">
												@if(count($item->product_info->colors) > 0)
													<div class="col-md-6">
														<label class="d-none">Color</label>
														<select class="form-control item_color_selector" name="color[]"
														id="item_color_selectorABCD{{$item->id}}"
														data-itemid="ABCD{{$item->id}}" data-item="<?php echo $item->product; ?>"
														available-url="<?php echo url('product_available_units'); ?>">
															<option value="" disabled selected>Choose Item Color</option>
															@foreach ($item->product_info->colors as $color)
																<option value="{{$color->color_info->id}}" @if($item->color == $color->color_info->id) selected @endif>{{$color->color_info->title}}</option>
															@endforeach
															</select>
													</div>
												@else
													<input type="hidden" name="color[]" value="0" id="item_color_selectorABCD{{$item->id}}" />
												@endif
												@if(count($item->product_info->sizes) > 0)
													<div class="col-md-6">
														<select class="form-control item_size_selector" name="size[]"
														id="item_size_selectorABCD{{$item->id}}"
														data-itemid="ABCD{{$item->id}}" data-item="<?php echo $item->product; ?>"
														available-url="<?php echo url('product_available_units'); ?>">
															<option value="" disabled selected>Choose Item Size</option>
															@foreach ($item->product_info->sizes as $size)
																<option value="{{$size->size_info->id}}" @if($item->size == $size->size_info->id) selected @endif>{{$size->size_info->title}}</option>
																<!-- <option value="0">New Product</option> -->
															@endforeach
														</select>
													</div>
												@else
													<input type="hidden" name="size[]" value="0" />
												@endif
											</div>
										@else
											<input type="hidden" name="color[]" value="0" />
											<input type="hidden" name="size[]" value="0" />
										@endif
									</div>
									<div id="order_item_price_ABCD{{$item->id}}" class="col-md-2">
										<label class="d-none">Price (EGP)</label>
										<input class="form-control sell_order_price" type="text" placeholder="Price" name="price[]" value="{{$item->price}}" />
									</div>
									<div class="col-md-1">
										<button type="button" class="btn btn-danger btn-sm btn-block delete_order_item" box="ABCD{{$item->id}}"><i class="fas fa-trash-alt"></i></button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-11">
										<textarea rows="1" class="form-control" placeholder="Note" name="note[]">{{$item->note}}</textarea>
									</div>
									<div id="order_item_available_units_ABCD{{$item->id}}" class="col-md-1">

									</div>
								</div>
							</div>
						</li>
					@endforeach
				</ol>

				<div class="text-center selling_order_products_title">
					<button type="button" class="btn btn-brand btn-sm" id="add_order_item" item-type="sell"
					button-url="{{url('add_order_item')}}"><i class="fas fa-plus-square"></i> Add New Item</button>
				</div>

				<div class="row form-group">
					<div class="col-md-12">
						<label>Note</label>
						<textarea class="form-control" placeholder="Note" name="order_note">{{$order->note}}</textarea>
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
<script src="{{ asset('tagsinput/jquery.amsify.suggestags.js')}}"></script>
<script>
$(document).ready(function() {
	$('#order_tags').amsifySuggestags({
		suggestionsAction : {
			url: '{{url("order_tags_suggestions")}}',
		},
	});

});
</script>
<script>

	$(document).ready(function() {
		$('.js-example-basic-multiple').select2();
	});
</script>
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