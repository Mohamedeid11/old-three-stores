@extends('admin.layout.main')
@section('styles')
<style>
    table thead {background: #001587; color: white;}
    .dataTables_wrapper .dataTable th {border: solid 1px white; color: white; font-size: 12px; width: auto !important;}
    table th hr {border-color: white;}
    .dataTables_wrapper .dataTable tbody tr.odd {background: #a3d1ff;}
    table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before, 
    table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {content: "";}
</style>
<link rel="stylesheet" href="{{asset('tagsinput/tagsinput.css')}}">
@endsection

@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Cities Reports</h3>
            </div>

        </div>
        <div class="kt-portlet__body">
			<form action="#" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
						<div class="col-md-2 col-6">
							<div class="form-group">
								<label>From</label>												
								<input type="date" class="form-control" name="from_date" id="from_date" value="{{$from_date}}" />
							</div>
						</div>
						<div class="col-md-2 col-6">
							<div class="form-group">
								<label>To</label>				
								<input type="date" class="form-control" name="to_date" id="to_date" value="{{$to_date}}" />
							</div>
						</div>

						<div class="col-md-4 col-6">
							<div class="form-group">
								<label>City</label>												
								<select class="form-control orders_selector_mul_city" name="city[]" id="city" multiple>
									<option value="All" @if(in_array("All", $selected_city)) selected @endif>All Cities</option>
									@foreach ($cities as $city)
										<option value="{{$city->id}}" @if(in_array($city->id, $selected_city)) selected @endif>{{$city->title}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-4 col-6">
							<div class="form-group">
								<label>REP</label>												
								<select class="form-control orders_selector_mul_reps" name="rep[]" id="rep" multiple>
									<option value="All" @if(in_array("All", $selected_rep)) selected @endif>All REPs</option>
									@foreach ($reps as $admin)
										<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_rep)) selected @endif>{{$admin->name}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-4 col-6">
							<div class="form-group">
								<label>Moderator</label>												
								<select class="form-control orders_selector_mul_moderator" name="moderator[]" id="moderator" multiple>
									<option value="All" @if(in_array("All", $selected_moderator)) selected @endif>All Moderators</option>
									@foreach ($admins as $admin)
										<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_moderator)) selected @endif>{{$admin->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4 col-6">
							<div class="form-group">
								<label>Order Status</label>												
								<select class="form-control orders_selector_mul_status" name="status[]" id="status" multiple>
									<option value="All" @if(in_array("All", $selected_status)) selected @endif>All Status</option>
									@foreach ($statuss as $status)
										<option value="{{$status->id}}" @if(in_array($status->id, $selected_status)) selected @endif>{{$status->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						
						<div class="col-md-4 col-6">
							<div class="form-group">
								<label>Client Type</label>												
								<select class="form-control" name="client_type" id="client_type">
									<option value="All">All Clients</option>
									<option value="0" @if($selected_client_type == '0') selected @endif>New Clients</option>
									<option value="1" @if($selected_client_type == '1') selected @endif>Recurring Clients</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12" id="order_number_gr">
							<label>Order Tags</label>
							<select class="form-control" name="tags[]" id="dashboard_order_tag" multiple>
								@foreach ($all_tags as $tag)
									<option value="{{$tag->id}}" @if(in_array($tag->id, $selected_tags)) selected @endif>{{$tag->title}}</option>
								@endforeach
							</select>
						</div>
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
			

			<div class="row">
			    <div class="col-12 col-md-7">
			        <div id="kt_morris_1" style="height: 600px;"></div>
			        <div class="row">
			            <div class="col-6 col-sm-3">
			                <i class="fas fa-square" style="color:#0355d6;"></i> Won
			            </div>
			            <div class="col-6 col-sm-3">
			                <i class="fas fa-square" style="color:#dc0101;"></i> Lost
			            </div>
			            <div class="col-6 col-sm-3">
			                <i class="fas fa-square" style="color:#ffc107;"></i> Open
			            </div>
			            <div class="col-6 col-sm-3">
			                <i class="fas fa-square" style="color:#4caf50;"></i> Total
			            </div>
			        </div>
			    </div>
			    <div class="col-12 col-md-5">
			        <div class="table-responsive">
			            <table class="table table-striped table-bordered">
			            <thead>
			                <tr>
			                    <th>Metrics</th>
			                    <th colspan="2">Percentage</th>
			                </tr>
			            </thead>
			            <tbody>
			                <tr>
			                    <td>Num. of orders</td>
			                    <td colspan="2">{{number_format($orders->count())}}</td>
			                </tr>
			                <tr>
			                    <td>Orders Total</td>
			                    <td colspan="2">{{number_format(($orders->sum('total_price') + $orders->sum('shipping_fees')), 2)}} EGP</td>
			                </tr>
			                <tr>
			                    <td>Total Revnue</td>
			                    <td colspan="2" id="total_order_revnue"><div class="text-center"><i class="fas fa-sync fa-spin"></i></div></td>
			                </tr>
			                <tr>
			                    <td>AVG order price</td>
			                    <td colspan="2">@if($orders->count() > 0) {{number_format((($orders->sum('total_price') + $orders->sum('shipping_fees')) / $orders->count()), 2)}} @else 0 @endif EGP</td>
			                </tr>
			                <tr>
			                    <td>AVG item's per order</td>
			                    <td colspan="2">@if($orders->count() > 0) {{number_format($total_items / $orders->count(), 2)}} @else 0 @endif</td>
			                </tr>
			                <tr>
			                    <td>AVG order cost</td>
			                    <td colspan="2" id="avg_orders_cost"><div class="text-center"><i class="fas fa-sync fa-spin"></i></div></td>
			                </tr>
			                <tr>
			                    <td>New Clients</td>
			                    <td>{{$new_clients_orders}}</td>
			                    <td>@if($orders->count() > 0) {{number_format(($new_clients_orders / $orders->count()) * 100, 2)}} @else 0 @endif %</td>
			                </tr>
			                <tr>
			                    <td>Recurring Clients</td>
			                    <td>{{$recurring_clients_orders}}</td>
			                    <td>@if($orders->count() > 0) {{number_format(($recurring_clients_orders / $orders->count()) * 100, 2)}} @else 0 @endif %</td>
			                </tr>
			            </tbody>
			        </table>
			        </div>
			        
			        <div class="table-responsive">
			            <table class="table table-striped table-bordered">
    			            <thead>
    			                <tr>
    			                    <th>Moderator</th>
    			                    <th>Orders Count</th>
    			                    <th>Percent % Of Total</th>
    			                </tr>
    			            </thead>
    			            <tbody>
    			                @foreach ($admins as $admin)
        			                @if ($orders->where('added_by', $admin->id)->count() > 0 && $orders->count() > 0)
            			                <tr>
            			                    <td>{{$admin->name}}</td>
            			                    <td>{{number_format(($orders->where('added_by', $admin->id)->count()), 2)}}</td>
            			                    <td>{{number_format((($orders->where('added_by', $admin->id)->count() / $orders->count()) * 100), 2)}} %</td>
            			                </tr>
            			            @endif
    			                @endforeach
    			            </tbody>
    			        </table>
    			    </div>
			    </div>
			</div>
			
			<hr />
            
            <div class="row" id="dashboard_reports_page_boxes">
                
                <div class="col-12 col-md-4">
                    <div class="repo_pox blue">
                        <h3>Won</h3>
                        <p><span>Number</span> <span>{{$orders->whereIn('status', array(5, 6))->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->whereIn('status', array(5, 6))->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->count() > 0) {{number_format(($orders->whereIn('status', array(5, 6))->count() / $orders->count()) * 100, 2)}} @else 0 @endif  %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox blue">
                        <h3>Delivered</h3>
                        <p><span>Number</span> <span>{{$orders->where('status', 5)->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->where('status', 5)->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(5, 6))->count() > 0) {{number_format(($orders->where('status', 5)->count() / $orders->whereIn('status', array(5, 6))->count()) * 100, 2)}} @else 0 @endif %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox blue">
                        <h3>Collected</h3>
                        <p><span>Number</span> <span>{{$orders->where('status', 6)->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->where('status', 6)->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(5, 6))->count() > 0) {{number_format(($orders->where('status', 6)->count() / $orders->whereIn('status', array(5, 6))->count()) * 100, 2)}} @else 0 @endif  %</span></p>
                    </div>
                </div>
                
                <div class="col-12 col-md-4">
                    <div class="repo_pox yellow">
                        <h3>Open</h3>
                        <p><span>Number</span> <span>{{$orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->count() > 0) {{number_format(($orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count() / $orders->count()) * 100, 2)}} @else 0 @endif  %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox yellow">
                        <h3>Pending</h3>
                        <p><span>Number</span> <span>{{$orders->whereIn('status', array(0, 1, 10, 11, 12, 13))->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->whereIn('status', array(0, 1, 10, 11, 12, 13))->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count() > 0) {{number_format(($orders->whereIn('status', array(0, 1, 10, 11, 12, 13))->count() / $orders->whereIn('status', array(0, 1, 2, 3, 4, 10, 11, 12, 13))->count()) * 100, 2)}} @else 0 @endif %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox yellow">
                        <h3>Shipped</h3>
                        <p><span>Number</span> <span>{{$orders->whereIn('status', array(2, 3, 4))->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->whereIn('status', array(2, 3, 4))->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count() > 0) {{number_format(($orders->whereIn('status', array(2, 3, 4))->count() / $orders->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count()) * 100, 2)}} @else 0 @endif %</span></p>
                    </div>
                </div>
                
                
                <div class="col-12 col-md-4">
                    <div class="repo_pox red">
                        <h3>Lost</h3>
                        <p><span>Number</span> <span>{{$orders->whereIn('status', array(7, 8))->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->whereIn('status', array(7, 8))->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->count() > 0) {{number_format(($orders->whereIn('status', array(7, 8))->count() / $orders->count()) * 100, 2)}} @else 0 @endif  %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox red">
                        <h3>Rejected</h3>
                        <p><span>Number</span> <span>{{$orders->where('status', 7)->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->where('status', 7)->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(7, 8))->count() > 0) {{number_format(($orders->where('status', 7)->count() / $orders->whereIn('status', array(7, 8))->count()) * 100, 2)}} @else 0 @endif %</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="repo_pox red">
                        <h3>Returned</h3>
                        <p><span>Number</span> <span>{{$orders->where('status', 8)->count()}}</span></p>
                        <p><span>Total</span> <span>{{$orders->where('status', 8)->sum('total_price')}}</span></p>
                        <p><span>Percent</span> <span>@if($orders->whereIn('status', array(7, 8))->count() > 0) {{number_format(($orders->where('status', 8)->count() / $orders->whereIn('status', array(7, 8))->count()) * 100, 2)}} @else 0 @endif %</span></p>
                    </div>
                </div>
                
            </div>
            
            
            <hr />

            <h3 class="text-info">Cities</h3>
            <div id="all_report_products" class="notloaded dashboard_results" data-url="{{url('report_city_table')}}"></div>
           

        </div>
	</div>
</div>					
@endsection

@section('scripts')
<script>
$(document).ready(function() { 
    get_product_table();
    
    $('body').on('click', '#products_table_btn', function(){
        if($(this).hasClass('btn_max')) {$(this).html('View More'); $(this).removeClass('btn_max');}
        else {$(this).html('View Less'); $(this).addClass('btn_max');}
        $('#product_reports_table').toggleClass('minimized_table');
    });
    
        
    // Class definition
    var KTMorrisChartsDemo = function() {
        var demo1 = function() {
            // LINE CHART
            new Morris.Line({
                // ID of the element in which to draw the chart.
                element: 'kt_morris_1',
                // Chart data records -- each entry in this array corresponds to a point on
                // the chart.
                data: [
                    @for ($i = strtotime($from_date); $i <= strtotime($to_date); $i)
                    {
                        days: '{{date("Y-m-d", $i)}}',
                        a: {{$orders->where('created_at', '>=', date("Y-m-d", $i)." 00:00:00")->where('created_at', '<=', date("Y-m-d", $i)." 23:59:59")->whereIn('status', array(5, 6))->count()}},
                        b: {{$orders->where('created_at', '>=', date("Y-m-d", $i)." 00:00:00")->where('created_at', '<=', date("Y-m-d", $i)." 23:59:59")->whereIn('status', array(7, 8))->count()}},
                        c: {{$orders->where('created_at', '>=', date("Y-m-d", $i)." 00:00:00")->where('created_at', '<=', date("Y-m-d", $i)." 23:59:59")->whereIn('status', array(0, 1, 2, 3, 4,  10, 11, 12, 13))->count()}},
                        d: {{$orders->where('created_at', '>=', date("Y-m-d", $i)." 00:00:00")->where('created_at', '<=', date("Y-m-d", $i)." 23:59:59")->count()}}
                    }@if($i != strtotime($to_date)), @endif
                    @php $i = $i + (24*3600); @endphp
                    @endfor
                    
                ],
                // The name of the data record attribute that contains x-values.
                xkey: 'days',
                // A list of names of data record attributes that contain y-values.
                ykeys: ['a', 'b', 'c', 'd'],
                // Labels for the ykeys -- will be displayed when you hover over the
                // chart.
                labels: ['Won', 'Lost', 'Open', 'Total'],
                xLabels: 'day',
              fillOpacity: 0.4,
              hideHover: 'auto',
              behaveLikeLine: true,
              resize: true,
              pointFillColors: ['#ffffff'],
              pointStrokeColors: ['black'],
              ymin:0,
              ymax:'auto',
              numLines: 9,
              lineColors: ['#0355d6', '#dc0101', '#ffc107', '#4caf50'],
            });
        }
        return {
            // public functions
            init: function() {
                demo1();
            }
        };
    }();
    
    jQuery(document).ready(function() {
        KTMorrisChartsDemo.init();
    });

});
    
function get_product_table()
{
    $("#all_report_products").html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');
    var action = $("#all_report_products").attr('data-url');
    $.ajax({
        type: 'POST',
        data: {from_date: '{{$from_date}}', to_date: '{{$to_date}}', city: '{{implode(',', $selected_city)}}', rep:'{{implode(',', $selected_rep)}}', moderator:'{{implode(',', $selected_moderator)}}', 
            client_type: '{{$selected_client_type}}', status: '{{implode(',', $selected_status)}}', product: '{{implode(',', $selected_product)}}',
			tags: '{{ implode(',', $selected_tags) }}'
        },
        url: action,
        success: function(data) 
        {
            $("#all_report_products").html(data);
            $("#all_report_products").removeClass('notloaded');
            $("#all_report_products").removeAttr('data-url');
        }
    });
}
</script>
@endsection