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
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-plus-square"></i></span>
                <h3 class="kt-portlet__head-title">Create Selling Order</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('selling_order')}}" enctype="multipart/form-data" id="ajsuform">
				{{csrf_field()}}
				<input type="hidden" name="order_num_id" value="{{$order->id}}" />
				<div id="ajsuform_yu"></div>
				<div class="form-group" id="client_finder">
					<div class="row">
						<div class="col-md-12">
							<label>Find Client</label>
							<input class="form-control" type="text" placeholder="Find Client" name="client_search" value="{{old('client_search')}}" id="client_search"
							data-url="{{url('find_client')}}" />
						</div>
					</div>
				</div>
				<div class="form-group" id="order_client_info">

				</div>
				<hr />
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<label>Order Number</label>
							<input class="form-control" type="text" placeholder="Order Number" name="order_number" value="{{$order_number}}" id="order_number"  />
							<input class="form-control" type="hidden" readonly placeholder="Order Number" name="order_number_xx" id="order_number_xx" value="{{$order_number}}"  />
						</div>
						<div class="col-md-3">
							<label>Shipping Number</label>
							<input class="form-control" type="text" placeholder="Shipping Number" name="shipping_number" id="shipping_number" value=""  />

						</div>
						<div class="col-md-3">
							<label>Order Category</label>
							<select name="order_category" class="form-control" id="order_category_selector">
							    @foreach ($cats as $cat)
							    <option value="{{$cat->id}}" data-symbol="{{$cat->order_symbol}}">{{$cat->title}}</option>
							    @endforeach
							</select>
						</div>



						<div class="col-md-3">
							<!--begin::Label-->
							<label for="moderator"  class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
								<span class="required mr-1">   Moderator</span>
							</label>
							<select id='moderator'  name="moderator"  style='width: 100%;'>
								<option selected value="{{Auth::guard('admin')->user()->id}}" >{{Auth::guard('admin')->user()->name}}</option>

							</select>
						</div>



						<div class="col-md-3 mt-3">
							<label>Order Date</label>
							<input class="form-control" type="date" placeholder="Order Date" name="shipping_date" value="{{date('Y-m-d')}}"  />
						</div>
{{--						<div class="form-group col-md-9 mt-3" id="order_number_gr">--}}
{{--							<label>Tags</label>--}}
{{--							<input type="text" class="form-control" name="tags" id="order_tags"  data-role="tagsinput" />--}}
{{--						</div>--}}

                        <div class="form-group col-md-3 mt-3">
                                 <label>Tags</label>

							<select class="js-example-basic-multiple" id='tag_id' name="tag_id[]" multiple style='width:100%;'>
							  @foreach($tags as $tag)
								<option value="{{$tag->id}}">{{$tag->title??''}}</option>
								  @endforeach


							</select>
						</div>



                        <div class="form-group col-md-3 mt-3">
                            <label>Payment Status</label>

                            <select class="form-control" id='payment_status' name="payment_status"  >
                                 <option value="not_paid">Not Paid</option>
                                <option value="paid"> Paid</option>
                                <option value="partly_paid">Partly Paid </option>


                            </select>
                        </div>

                        <div class="form-group col-md-3 mt-3" id="payment_amount_container">


                        </div>



                        </div>
				</div>
				<h3 class="selling_order_products_title">
					<i class="fas fa-list"></i> Order Items
				</h3>
				<ol id="order_products">
					<li class="single_order_item_x" id="single_order_item_box_{{$first_box_id}}">
						<input type="hidden" name="order_item[]" value="{{$first_box_id}}" />
						<div class="container-fluid">
							<div class="row mb-1">
								<div class="col-md-3">
									<label class="d-none">Product</label>
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
								<div class="col-md-2">
									<label class="d-none">QTY</label>
									<input class="form-control sell_order_qty" type="number" placeholder="QTY" name="qty[]" id="sell_order_qty_{{$first_box_id}}" item-id="{{$first_box_id}}"
									price-url="{{url('product_price')}}" />
								</div>
								<div id="order_item_options_{{$first_box_id}}" class="col-md-4">

								</div>
								<div id="order_item_price_{{$first_box_id}}" class="col-md-2">

								</div>
							</div>
							<div class="row">
								<div class="col-md-11">
									<textarea rows="1" class="form-control" placeholder="Note" name="note[]"></textarea>
								</div>
								<div class="col-md-1">
								<div id="order_item_available_units_{{$first_box_id}}"></div>
								</div>
							</div>
						</div>
					</li>
				</ol>
				<div class="selling_order_products_title text-center">
					<button type="button" class="btn btn-brand btn-sm" id="add_order_item" item-type="sell"
					button-url="{{url('add_order_item')}}"><i class="fas fa-plus-square"></i> Add New Item</button>
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
    import Options from "../../../../../public/assets/vendors/general/bootstrap-switch/docs/options.html";
    export default {
        components: {Options}
    }
</script>
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
    $('body').on('change', '#order_category_selector', function(){
        var order_number = $('#order_number').val();
        var x = $('#order_category_selector').find('option:selected').attr('data-symbol');
        var abc = order_number+x;
        $('#order_number_xx').val(abc);
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
<script>

	(function () {

		$("#moderator").select2({
			placeholder: 'Channel...',
			// width: '350px',
			allowClear: true,
			ajax: {
				url: '{{route('admin.getAdmins')}}',
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