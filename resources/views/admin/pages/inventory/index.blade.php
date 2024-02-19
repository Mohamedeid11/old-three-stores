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

						<a href="#" class="btn btn-info d-block d-md-inline-block mb-1" id="calculate_selected_orders_amount" task="CalculateTotalAmount" 
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
            <div id="home_reports">
                <div class="row">
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Qty</p>
                            <p class="report_number">{{$total_items}}</p>
                            <i class="fas fa-list"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Amount</p>
                            <p class="report_number">{{number_format($total_prices, 2)}} EGP</p>
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
            </div>

			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
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
				    @php $ix = 0; @endphp
					@for ($i = 0; $i < count($products);  $i++)
					    @php $ix++; @endphp
						<tr>
						    <td>
								<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
									<input type="checkbox" class="check_single" name="item[]" value="{{$products[$i][6]}}-{{$products[$i][1]}}-{{$products[$i][2]}}" />
									<span></span>
								</label>
							</td>
							<td>{{$ix}}</td>
							<td>{{$products[$i][0]}}</td>
							<td>{{$products[$i][3]}}</td>
							<td>{{$products[$i][4]}}</td>
							<td>{{$products[$i][4] - $products[$i][3] - $products[$i][7]}}</td>
							<td>@if($products[$i][4] > 0) {{number_format($products[$i][5] / $products[$i][4], 2)}} EGP @else 0.00 EGP @endif</td>
							
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $i }}">Ruined Items</a>
									</div>
								</div>

								<div class="modal fade" id="myModal-{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Ruined Items</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('inventory_ruined_items') }}" class="inventory_ruined_items_form" method="POST" 
								data-num="{{$products[$i][6]}}_{{$products[$i][1]}}_{{$products[$i][2]}}">
								{{ csrf_field() }}
								<div id="inventory_ruined_items_form_res{{$products[$i][6]}}_{{$products[$i][1]}}_{{$products[$i][2]}}"></div>
								<input type="hidden" name="color" value="{{$products[$i][1]}}" />
								<input type="hidden" name="size" value="{{$products[$i][2]}}" />
								<input type="hidden" name="product" value="{{$products[$i][6]}}" />
								<div class="form-group">
								    <input type="number" name="ruined_item" class="form-control" value="{{$products[$i][7]}}" />
								</div>
								{{ruinded_items_admn($products[$i][6], $products[$i][1], $products[$i][2])}}
								<button type="submit" class="btn btn-danger" name='save'>Save</button>
								<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
								</form>
								</div>
								</div>
								</div>
								</div>
							</td>
						</tr>
					@endfor
				</tbody>
			</table>
        </div>
	</div>
</div>					
@endsection