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
                    <form class="kt-form" method="post" action="{{url('selling_order')}}" enctype="multipart/form-data" id="kt_form">
				        {{csrf_field()}}
				        
				        
                        <!--begin: Form Wizard Step 1-->
                        <div class="kt-wizard-v1__content" data-ktwizard-type="step-content" data-ktwizard-state="current">
						
							<div class="kt-heading kt-heading--md">Order Information</div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
                                    <div class="form-group">	
            							<label>Order Number</label>		
            							<input class="form-control" type="hidden" placeholder="Order Number" name="order_number" value="{{$order_number}}" id="order_number"  />
            							<input class="form-control" type="text" readonly placeholder="Order Number" name="order_number_xx" id="order_number_xx" value="{{$order_number}}"  />
            						</div>
            						<div class="form-group">	
            							<label>Order Category</label>		
            							<select name="order_category" class="form-control" id="order_category_selector">
            							    @foreach ($cats as $cat)
            							    <option value="{{$cat->id}}" data-symbol="{{$cat->order_symbol}}">{{$cat->title}}</option>
            							    @endforeach
            							</select>
            						</div>
            						<div class="form-group">	
            							<label>Order Date</label>												
            							<input class="form-control" type="date" placeholder="Order Date" name="shipping_date" value="{{date('Y-m-d')}}"  />
            						</div>
            						<input type="hidden" name="order_num_id" value="{{$order->id}}" />
									<div class="form-group">
            							<label>Find Client</label>												
            							<input class="form-control" type="text" placeholder="Find Client" name="client_search" value="{{old('client_search')}}" id="client_new_search" 
            							data-url="{{url('order_client_search')}}" />
            						</div>
            						<div class="form-group" id="order_client_info"></div>
            						 <div class="form-group" id="order_delivery_address"></div>
								</div>
							</div>
						</div>



						
						<!--begin: Form Wizard Step 3-->
						<div class="kt-wizard-v1__content" data-ktwizard-type="step-content">
							<div class="kt-heading kt-heading--md">Enter the Details of Order <button type="button" class="btn btn-brand btn-sm pull-right" id="add_order_item" item-type="sell"
					button-url="{{url('add_order_item')}}"><i class="fas fa-plus-square"></i> Add New Item</button></div>
							<div class="kt-form__section kt-form__section--first">
								<div class="kt-wizard-v1__form">
									<ol id="order_products">
                    					<li class="single_order_item" id="single_order_item_box_{{$first_box_id}}">
                    						<input type="hidden" name="order_item[]" value="{{$first_box_id}}" />
                    						<div class="order_item_details">
                    							<div class="row form-group">
                    								<div class="col-md-5">
                    									<label>Product</label>
                    									<select class="order_product_item form-control" name="product[]" id="order_item_{{$first_box_id}}"
                    									options-url="{{url('product_options')}}" item-id="{{$first_box_id}}" price-url="{{url('product_price')}}"
                    									available-url="{{url('product_available_units')}}">
                    										<option value="" disabled selected>Choose Order Item</option>
                    										@foreach ($products as $product)
                    											<option value="{{$product->id}}">{{$product->title}}</option>
                    										@endforeach
                    										<!-- <option value="0">New Product</option> -->
                    									</select>
                    								</div>
                    								<div class="col-md-5">
                    									<label>QTY</label>												
                    									<input class="form-control sell_order_qty" type="number" placeholder="QTY" name="qty[]" id="sell_order_qty_{{$first_box_id}}" item-id="{{$first_box_id}}"
                    									price-url="{{url('product_price')}}" />
                    								</div>
                    							<div class="col-md-1">
                                                        <label><br></label>
                                                        <button type="button" class="btn btn-danger btn-sm btn-block delete_order_item" box="{{$first_box_id}}"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                    								
                    								<div class="col-md-1">
                    									<label><br /></label>
                    									<button type="button" class="btn btn-warning btn-sm btn-block collapse_details_box" box="{{$first_box_id}}"><i class="fas fa-minus"></i></button>
                    								</div>
                    							</div>
                    							<div id="order_item_price_{{$first_box_id}}">
                    
                    							</div>
                    							<div id="order_item_options_{{$first_box_id}}">
                    
                    							</div>
                    							<div id="order_item_available_units_{{$first_box_id}}">
                    
                    							</div>
                    							<div class="row form-group">
                    								<div class="col-md-12">
                    									<label>Note</label>												
                    									<textarea class="form-control" placeholder="Note" name="note[]"></textarea>
                    								</div>
                    							</div>
                    						</div>
                    						<div class="order_item_collapse height_zero">
                    							<div class="row">
                    								<div class="col-md-11">
                    									<p>Order Item</p>
                    								</div>
                    								<div class="col-md-1">
                    									<button type="button" class="btn btn-warning btn-sm btn-block uncollapse_details_box" box="{{$first_box_id}}"><i class="fas fa-plus"></i></button>
                    								</div>
                    							</div>
                    						</div>
                    					</li>
                    					
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
							<div class="kt-heading kt-heading--md">Order Confirmation Message</div>
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
$("#order_details_review").on('mouseup', function() { 
	var sel, range;
	var el = $(this)[0];
	if (window.getSelection && document.createRange) { //Browser compatibility
	  sel = window.getSelection();
	  if(sel.toString() == ''){ //no text selection
		 window.setTimeout(function(){
			range = document.createRange(); //range object
			range.selectNodeContents(el); //sets Range
			sel.removeAllRanges(); //remove all ranges from selection
			sel.addRange(range);//add Range to a Selection.
		},1);
	  }
	}else if (document.selection) { //older ie
		sel = document.selection.createRange();
		if(sel.text == ''){ //no text selection
			range = document.body.createTextRange();//Creates TextRange object
			range.moveToElementText(el);//sets Range
			range.select(); //make selection.
			document.execCommand('Copy');
		}
	}
});

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