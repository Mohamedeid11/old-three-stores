@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-warehouse"></i></span>
                <h3 class="kt-portlet__head-title">Inventory</h3>
            </div>
			<div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">

						<a href="#" class="btn btn-info d-block d-md-inline-block mb-1" id="calculate_selected_orders_amount_ajax" task="CalculateTotalAmount" 
						url="{{url('inventory/task')}}" 
						data-toggle="modal" data-target="#myModalTotalAmount"><i class="fas fa-money-bill"></i> Total Amount</a>

    					<div class="modal fade" id="myModalTotalAmount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    						<div class="modal-dialog">
        						<div class="modal-content">
            						<div class="modal-header">
            							<h5 class="modal-title">Selected Items</h5>
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


                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
		<form action="#" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
						
						<div class="col-md-9 col-6">
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
						<div class="col-md-3">
							<label class="control-label"><br /></label>
							<button type="submit" class="btn btn-success btn-block">Search</button>
						</div>	
					</div>	
				</div>
			</form>


            <div id="home_reports">
                <div class="row">
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Qty</p>
                            <p class="report_number">{{number_format($total_items, 0)}}</p>
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Amount</p>
                            <p class="report_number" id="inventory_total_amounts">-</p>
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
            </div>
			<form action="#" method="get" class="kt-form">
				@for ($i = 0; $i < count($selected_product); $i++)
					<input type="hidden" name="product[]" value="{{$selected_product[$i]}}" />
				@endfor
				<div class="form-group row">
					<div class="col-md-1">
						<select name="perPage" class="form-control" onChange="this.form.submit()">
							<option value="50" @if($perPage == 50) selected @endif>50</option>
							<option value="75" @if($perPage == 75) selected @endif>75</option>
							<option value="100" @if($perPage == 100) selected @endif>100</option>
							<option value="150" @if($perPage == 150) selected @endif>150</option>
							<option value="all" @if($perPage == 'all') selected @endif>All</option>
						</select>
					</div>
				</div>	
			</form>
			<table class="table table-striped- table-bordered table-hover table-checkable">
				<thead>
					<tr>
					    <th class="disable_sort">
							<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
								<input type="checkbox" id="checkAll">
								<span></span>
							</label>
						</th>
						<th>#</th>
						<th>Product</th>
						<th>Sold</th>
						<th>Bought</th>
						<th>Qty</th>
						<th>Avg. Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@for ($i = 0; $i < count($dproducts); $i++)
						<tr>
						    <td>{!!$dproducts[$i]['CheckBox']!!}</td>
						    <td>{{ $i + 1 }}</td>
						    <td>{{$dproducts[$i]['Product']}}</td>
						    <td>{{$dproducts[$i]['Sold']}}</td>
						    <td>{{$dproducts[$i]['Bought']}}</td>
						    <td>{{$dproducts[$i]['Qty']}}</td>
						    <td>{{$dproducts[$i]['AvgPrice']}}</td>
						    <td>{!!$dproducts[$i]['Action']!!}</td>
						</tr>
					@endfor
				</tbody>
			</table>
			{{$products->appends($_GET)->links()}}
{{--
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_ajax_1">
				<thead>
					<tr>
						<th class="disable_sort">
							<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
								<input type="checkbox" id="checkAll">
								<span></span>
							</label>
						</th>
						<th>#</th>
						<th>Product</th>
						<th>Sold</th>
						<th>Bought</th>
						<th>Qty</th>
						<th>Avg. Price</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
--}}			
        </div>
	</div>
</div>					
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
$.ajax({
    type: 'POST',
    data: {selected_product: "{{implode(',', $selected_product)}}"},
    url: "{{url('inventory_total_amounts')}}",
    success: function(data) 
    {
        $('#inventory_total_amounts').html(data);	
    }
});  
/*
var table = $('#kt_table_ajax_1');
		table.DataTable({
		    ajax: {
				url: '{{url("inventory_data")}}',
				type: 'POST',
				data: {'selected_product': "{{implode(',', $selected_product)}}"}
			},
			responsive: true,
			pagingType: 'full_numbers',
			paging: true,
		    searchDelay: 500,
			processing: true,
			serverSide: false,
			responsive: true,
			serverPaging: false,
            serverFiltering: false,
            serverSorting: false,
			lengthMenu:[[25,50,100],[25,50,100]],
			columns: [
				{data: 'CheckBox'},
				{data: 'ID'},
				{data: 'Product'},
				{data: 'Sold'},
				{data: 'Bought'},
				{data: 'Qty'},
				{data: 'AvgPrice'},
				{data: 'Action', responsivePriority: -1},
			],
			columnDefs: [
				{
					targets: 0,
					title: '<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary"><input type="checkbox" id="checkAll"><span></span></label>',
					orderable: false
				}
			],
		});
*/
   
});
</script>
@endsection