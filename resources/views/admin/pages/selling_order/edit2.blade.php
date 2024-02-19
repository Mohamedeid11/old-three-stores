@extends('admin.layout.main')
@section('styles')
<link href="{{asset('assets/vendors/general/sweetalert2/dist/sweetalert2.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('assets/app/custom/wizard/wizard-v1.default.css')}}">
<style>
.kt-content {padding: 25px;}
#cient_finder_data {background: transparent; padding:0; border-radius:0;}
.select2-container {width: 100% !important;}
</style>
@endsection
@section('content')

<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="kt-portlet">
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid  kt-wizard-v1 kt-wizard-v1--white" id="kt_wizard_v1" data-ktwizard-state="step-first">
				<div class="kt-grid__item">
            
					<!--begin: Form Wizard Nav -->
					<div class="kt-wizard-v1__nav">
						<div class="kt-wizard-v1__nav-items">
							<a class="kt-wizard-v1__nav-item order_paging_btn" href="#" data-ktwizard-type="step" data-ktwizard-state="current">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-information"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										1) Order Information
									</div>
								</div>
							</a>
							<a class="kt-wizard-v1__nav-item order_paging_btn" href="#" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-list"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										2) Order Items
									</div>
								</div>
							</a>
							<a class="kt-wizard-v1__nav-item order_paging_btn" href="#" data-ktwizard-type="step">
								<div class="kt-wizard-v1__nav-body">
									<div class="kt-wizard-v1__nav-icon">
										<i class="flaticon-globe"></i>
									</div>
									<div class="kt-wizard-v1__nav-label">
										3) Review and Submit
									</div>
								</div>
							</a>
						</div>
					</div>

					<!--end: Form Wizard Nav -->
				</div>
				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v1__wrapper">

					<!--begin: Form Wizard Form-->
                    <form class="kt-form" method="post" action="{{url('selling_order/'.$order->id)}}" enctype="multipart/form-data" id="kt_form">
				        {{csrf_field()}}
				        
				        
                        <!--begin: Form Wizard Step 1-->
                        <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
						
							<div class="kt-heading kt-heading--md">Order Information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
                                    <div class="form-group">	
            							<label>Order Number</label>		
            							<input class="form-control" type="hidden" placeholder="Order Number" name="order_number" value="{{$order->order_number}}"  />
							            <input class="form-control" type="text" readonly disabled placeholder="Order Number" name="order_number_xx" value="{{$order->order_number}}"  />
            						</div>
            						<div class="form-group">	
            							<label>Order Date</label>												
            							<input class="form-control" type="date" placeholder="Order Date" name="shipping_date" value="{{date('Y-m-d', strtotime($order->created_at))}}"  />
            						</div>
            						<input type="hidden" name="client" value="{{$order->client_info->id}}" />
									<div class="form-group">
            							<label>Find Client</label>												
            							<input class="form-control" type="text" placeholder="Find Client" name="client_search" value="{{$order->client_info->phone}}" id="client_new_search" 
            							data-url="{{url('order_client_search')}}" />
            						</div>
            						<div class="form-group" id="order_client_info">
                                            <input type="hidden" name="client" value="{{$order->client_info->id}}" />
                                        <div class="form-group">
                                            <label>Name</label>	
                                            <input class="form-control" type="text"  placeholder="Name" value="{{$order->client_info->name}}" id="name" name="name" />
                                        </div>
                                        <div class="form-group">
                                            <label>Phone No.</label>	
                                            <input class="form-control" type="text"  placeholder="Phone No." value="{{$order->client_info->phone}}" id="phone" name="phone" />
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>	
                                            <input class="form-control" type="text"  placeholder="Email" value="{{$order->client_info->email}}" id="email" name="email" />
                                        </div>

            						</div>
            						<div class="form-group" id="order_delivery_address">
                                        <div class="form-group">
                                            <label>Address</label>	
                                            <input class="form-control" type="text"  placeholder="Address" value="{{$order->address}}" id="address" name="address" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>City</label>	
                                            <select class="form-control" name="city" id="client_city_selector" shipping-url="{{ url('shipping_price_info') }}">
                                                <option value="" disabled selected>Choose City</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{$city->id}}" @if($order->city == $city->id) selected @endif>{{$city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">	
                                            <label>Shipping Price (EGP)</label>	
                                            <input class="form-control" type="text"  placeholder="Shipping Price" value="{{$order->shipping_fees}}" id="order_ship_price" name="ship_price" />
                                        </div>
                                        
                                        <div class="form-group">	
                                            <label>Location</label>	
                                            <input class="form-control" type="text"  placeholder="Order Location" id="order_location" name="location" value="{{$order->location}}" />                                    </div>
                                        </div>
                                        
				                    </div>
				                    
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 1-->
						

						
						<!--begin: Form Wizard Step 3-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Enter the Details of Order <button type="button" class="btn btn-brand btn-sm pull-right" id="add_order_item" item-type="sell"
					button-url="{{url('add_order_item')}}"><i class="fas fa-plus-square"></i> Add New Item</button></div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<ol id="order_products">
                    					@foreach ($order->items as $item)
                    						<li class="single_order_item" id="single_order_item_box_ABCD{{$item->id}}">
                    							<input type="hidden" name="order_item[]" value="ABCD{{$item->id}}" />
                    							<div class="order_item_details">
                    								<div class="row form-group">
                    									<div class="col-md-5">
                    										<label>Product</label>
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
                    									<div class="col-md-5">
                    										<label>QTY</label>												
                    										<input class="form-control sell_order_qty" type="number" placeholder="QTY" name="qty[]" value="{{$item->qty}}" 
                    										id="sell_order_qty_ABCD{{$item->id}}" item-id="ABCD{{$item->id}}" price-url="{{url('product_price')}}" />
                    									</div>
                    									<div class="col-md-1">
                    										<label><br /></label>
                    										<button type="button" class="btn btn-danger btn-sm btn-block delete_order_item" box="ABCD{{$item->id}}"><i class="fas fa-trash-alt"></i></button>
                    									</div>
                    									<div class="col-md-1">
                    										<label><br /></label>
                    										<button type="button" class="btn btn-warning btn-sm btn-block collapse_details_box" box="ABCD{{$item->id}}"><i class="fas fa-minus"></i></button>
                    									</div>
                    								</div>
                    								<div id="order_item_price_ABCD{{$item->id}}">
                        								<div class="form-group row">
                                                            <div class="col-md-5">
                                                                <label>Price (EGP)</label>												
                                                                <input class="form-control sell_order_price" type="text" placeholder="Price" name="price[]" value="{{$item->price}}" />
                                                            </div>
                                                        </div>
                    								</div>
                    								<div id="order_item_options_ABCD{{$item->id}}">
                    									@if(count($item->product_info->colors) > 0 || count($item->product_info->sizes) > 0)
                    										<div class="row form-group">
                    											@if(count($item->product_info->colors) > 0)
                    												<div class="col-md-5">
                    													<label>Color</label>
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
                    												<div class="col-md-5">
                    													<label>Size</label>
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
                    								<div id="order_item_available_units_ABCD{{$item->id}}">
                    
                    								</div>
                    								<div class="row form-group">
                    									<div class="col-md-12">
                    										<label>Note</label>												
                    										<textarea class="form-control" placeholder="Note" name="note[]">{{$item->note}}</textarea>
                    									</div>
                    								</div>
                    							</div>
                    							<div class="order_item_collapse height_zero">
                    								<div class="row">
                    									<div class="col-md-11">
                    										<p>Order Item</p>
                    									</div>
                    									<div class="col-md-1">
                    										<button type="button" class="btn btn-warning btn-sm btn-block uncollapse_details_box" box="ABCD{{$item->id}}"><i class="fas fa-plus"></i></button>
                    									</div>
                    								</div>
                    							</div>
                    						</li>
                    					@endforeach
                    					
                    				</ol>
                    				<div class="row form-group">
                                    	<label>Note</label>												
                                    	<textarea class="form-control" placeholder="Note" name="order_note"></textarea>
                                    </div>
                                    
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 3-->


						<!--begin: Form Wizard Step 5-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Review your Details and Submit</div>
							<div class="kt-form__section kt-form__section--first">
							    <div id="ajsuform_yu"></div>
								<div class="kt-wizard-v1__review" id="order_details_review">
									
								</div>
							</div>
						</div>

						<!--end: Form Wizard Step 5-->

						<!--begin: Form Actions -->
						<div class="kt-form__actions">
							<div class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u order_paging_btn" data-ktwizard-type="action-prev">
								Previous
							</div>
							<div class="btn btn-success btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-submit" data-url="{{url('selling_order')}}">
								Submit
							</div>
							<div class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u order_paging_btn" data-ktwizard-type="action-next">
								Next Step
							</div>
						</div>

						<!--end: Form Actions -->
					</form>

					<!--end: Form Wizard Form-->
				</div>
			</div>
		</div>
	</div>
</div>

<!-- end:: Content -->

@endsection


@section('script-files')
<script src="{{asset('assets/vendors/general/sweetalert2/dist/sweetalert2.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/components/vendors/sweetalert2/init.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/vendors/general/jquery-validation/dist/jquery.validate.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery-validation/dist/additional-methods.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/components/vendors/jquery-validation/init.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/app/custom/wizard/selling-order-wizard.js')}}" type="text/javascript"></script>
@endsection

@section('scripts')
<script>

    $('body').on('keyup', '#client_new_search', function(e) {
        var action = $(this).attr('data-url');
        var search = $(this).val();
        $.ajax({
            type: 'POST',
            data: {search: search},
            url: action,
            success: function(data) 
            {
               $('#order_client_info').html(data.client_info);
               $('#order_delivery_address').html(data.delivery_info);
               if($('#client_city_selector').length)
               {
                    $('#client_city_selector').select2();
               }
            }
        }); 
        return false;
    });
    $('body').on('change', '#order_category_selector', function(){
        var order_number = $('#order_number').val(); 
        var x = $('#order_category_selector').find('option:selected').attr('data-symbol');
        var abc = order_number+x;
        $('#order_number_xx').val(abc);
    });
    
    $('body').on('click', '.order_paging_btn', function(){
        $('#order_details_review').html('<div class="fa-3x text-center"><i class="fas fa-circle-notch fa-spin"></i></div>');
        var action = '{{url('selling_order_form_data')}}';
        var formData = new FormData($("#kt_form")[0]);
        $.ajax({
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            url: action,
            success: function(data) 
            {
                $('#order_details_review').html(data);
            }
        });
    });
</script>
@endsection