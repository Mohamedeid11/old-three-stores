@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Fullfillment</h3>
				</div>
				<div class="kt-portlet__head-toolbar">
                	<div class="kt-portlet__head-wrapper">
                    	<div class="kt-portlet__head-actions">
                	        <a href="{{url('fulfillment/print?status='.$selected_status)}}" class="btn btn-info" target="_blank"><i class="fa fa-print"></i> Print</a>
                	    </div>
                	</div>
                </div>
			</div>
		<div style="display:none" id="check_image">

		</div>
			<div class="kt-portlet__body">
			   <form action="#" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Status</label>												
								<select class="form-control" name="status" id="status">
									<option value="All">All</option>
									<option value="Pending" @if($selected_status == "Pending") selected @endif>Pending</option>
									<option value="Partly-Available" @if($selected_status == "Partly-Available") selected @endif>Partly Available</option>
									<option value="Not-Available" @if($selected_status == "Not-Available") selected @endif>Not Available</option>
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
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_2">
				<thead>
					<tr>
						<th class="disable_sort">
							<!--<label class="kt-checkbox">-->
							<!--	<input type="checkbox" id="checkAll">-->
							<!--	<span></span>-->
							<!--</label>-->
						</th>
						<th>Order Num.</th>
						<th>Client</th>
						<th>City</th>
						<th>Item</th>
						<th>Items</th>
						<th>Date</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($orders as $order)
						@foreach ($order->itemsq as $item)
							@for ($i = 1; $i <= abs($item->qty); $i++)
							    @if (($selected_status != "Not-Available") || ($selected_status == "Not-Available" && !fulfillment_avilable_item($item->id, $i)))
    								<tr>
    									<td>
    										<label class="kt-checkbox kt-checkbox--success kt-checkbox--bold">
    											<input type="checkbox" class="fulfillment_checker" name="item[]" 
    											@if(fulfillment_avilable_item($item->id, $i)) checked value="0" @else value="{{$item->id}}"  @endif
    											data-url="{{url('fulfillment/avilable_items')}}" data-item="{{$item->id}}" data-index="{{$i}}" />
    											<span></span>
    										</label>
											<span id="loader_{{$item->id}}"></span>
    									</td>
    									<td>{{$order->order_number}}</td>
    									<td>{{optional($order->client_info)->name}}</td>
										<td>{{optional($order->city_info)->title??''}}</td>
										<td>
											{{optional($item->product_info)->title}}	@if($item->color > 0) <b> {{optional($item->color_info)->title}}</b> @endif  		@if($item->size > 0) <b> {{optional($item->size_info)->title}}</b> @endif
										</td>
    									<td>{{$item->qty / abs($item->qty)}}</td>
    									<td>{{$order->created_at}}</td>
    									<td>
											{{$item->note}}
											@foreach ($order->tags as $tag)
												<span class="badge badge-danger mb-1">{{ optional($tag->tag)->title }}</span>
											@endforeach
										</td>
    								</tr>
    							 @endif
							@endfor
						@endforeach
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>					
@endsection

@section('scripts')



	<script>
		var loader_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/loader2.gif">';
		var true_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/true.png">';
		var warning_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/warning.png">';
            $('#check_image').html(warning_img);
		$(document).on('change', '.fulfillment_checker', function() {
			var item = $(this).attr('data-item');
			var warn_img=warning_img;
			var load_img=loader_img;

			var item_index = $(this).attr('data-index');
			var action = $(this).attr('data-url');
			$.ajax({
				type: 'POST',
				beforeSend: function () {
					$(`#loader_${item}`).html(load_img);
				},
				data: { item: item, item_index: item_index },
				url: action,
				success: function(data) {
										$(`#loader_${item}`).html(true_img);

				},
				error: function(data) {
				$(`#loader_${item}`).html(warn_img);

				}
			});
		});
	</script>

@endsection
