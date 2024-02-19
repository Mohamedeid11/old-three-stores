@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Accounting</h3>
            </div>
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">

                </div>
            </div>
        </div>
        
        

        <div class="kt-portlet__body">
            
            <form action="#" method="get" class="kt-form">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">	
                        <div class="form-group">
                        <label class="control-label">Year</label>
                        <select class="form-control" name="year">
                        	@for ($i = 2020; $i <= date('Y'); $i++)
                        	<option value="{{$i}}" @if($selected_year == $i) selected @endif>{{$i}}</option>
                        	@endfor
                        </select>
                        </div>
                        </div>
                        
                        
                        
                        <div class="col-md-2"><label class="control-label"><br /></label><button type="submit" class="btn btn-success btn-block">Search</button></div>	
                    </div>	
                </div>
            </form>
        
		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="profit_loss_table">
					<thead>
						<tr>
							<th>#</th>
							@for ($i = 1; $i <= 12; $i++)
								@php
								    if($i == 1 && $i == 3 && $i == 5 && $i == 7 && $i == 8 && $i == 10 && $i == 12) {$days = 31;}
									else if($i == 2) {if($selected_year % 4 == 0){$days = 29;}else{$days = 28;}}
									else {$days = 30;}       
									$from_date = date('Y-m-d', strtotime($selected_year.'-'.$i.'-01'));
									$end_date = date('Y-m-d', strtotime($selected_year.'-'.$i.'-'.$days));
								@endphp
								<th><a href="{{url('accounting_report?from_date='.$from_date.'&to_date='.$end_date)}}">{{date('F', strtotime($i.'/05/2019'))}}</a></th>
							@endfor
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<tr class="profit_loss_income_title"><td colspan="15">Income</td></tr>
						<tr>
							<td>Revnue</td>
							@for ($i = 1; $i <= 12; $i++)
								<td>{{number_format(calculate_site_income(0, $i, $selected_year), 2)}}</td>
							@endfor
							<td>{{number_format(calculate_site_income(0, 0, $selected_year), 2)}}</td>
						</tr>
						<tr class="profit_loss_expanse_title"><td colspan="15">Expanses</td></tr>
						<tr>
							<td>Bought Orders</td>
							@for ($i = 1; $i <= 12; $i++)
								<td>{{number_format(calculate_bought_orders(0, $i, $selected_year), 2)}}</td>
							@endfor
							<td>{{number_format(calculate_bought_orders(0, 0, $selected_year), 2)}}</td>
						</tr>
						@foreach ($cats as $cat)
							<tr>
								<td>{{$cat->title}}</td>
								@for ($i = 1; $i <= 12; $i++)
									<td>{{number_format(calculate_site_expanse($cat->id, 0, $i, $selected_year), 2)}}</td>
								@endfor
								<td>{{number_format(calculate_site_expanse($cat->id, 0, 0, $selected_year), 2)}}</td>
							</tr>
						@endforeach
						<tr class="profit_loss_total_expanses">
							<td><b>Total Expenses</b></td>
							@for ($i = 1; $i <= 12; $i++)
								<td>{{number_format((calculate_site_expanse(0, 0, $i, $selected_year) + calculate_bought_orders(0, $i, $selected_year)), 2)}}</td>
							@endfor
							<td>{{number_format((calculate_site_expanse(0, 0, 0, $selected_year) + calculate_bought_orders(0, 0, $selected_year)), 2)}}</td>
						</tr>			        
						<tr class="profit_loss_total">
							<td><b>Total Profit (Loss)</b></td>
							@for ($i = 1; $i <= 12; $i++)
								<td 
								@if(calculate_site_income(0, $i, $selected_year) - (calculate_site_expanse(0, 0, $i, $selected_year) + calculate_bought_orders(0, $i, $selected_year)) < 0)
								class="total_loss_cell" @endif>
									{{number_format((calculate_site_income(0, $i, $selected_year) - (calculate_site_expanse(0, 0, $i, $selected_year) + calculate_bought_orders(0, $i, $selected_year))), 2)}}
								</td>
							@endfor
							<td 
								@if(calculate_site_income(0, 0, $selected_year) - (calculate_site_expanse(0, 0, 0, $selected_year) + calculate_bought_orders(0, 0, $selected_year)) < 0)
								class="total_loss_cell" @endif>
									{{number_format((calculate_site_income(0, 0, $selected_year) - (calculate_site_expanse(0, 0, 0, $selected_year) + calculate_bought_orders(0, 0, $selected_year))), 2)}}
								</td>
						</tr>
						<tr class="profit_loss_expanse_title"><td colspan="15">Partners</td></tr>
						@foreach ($partners as $cat)
							<tr>
								<td>{{$cat->title}}</td>
								@for ($i = 1; $i <= 12; $i++)
									<td>{{number_format(calculate_site_partner($cat->id, 0, $i, $selected_year), 2)}}</td>
								@endfor
								<td>{{number_format(calculate_site_partner($cat->id, 0, 0, $selected_year), 2)}}</td>
							</tr>
						@endforeach
						
						<tr class="profit_loss_total_expanses">
							<td><b>Total Partners</b></td>
							@for ($i = 1; $i <= 12; $i++)
								<td>{{number_format(calculate_site_partner(0, 0, $i, $selected_year), 2)}}</td>
							@endfor
							<td>{{number_format(calculate_site_partner(0, 0, 0, $selected_year), 2)}}</td>
						</tr>
						<tr class="profit_loss_total">
							<td><b>Total </b></td>
							@for ($i = 1; $i <= 12; $i++)
								<td @if((calculate_site_income(0, $i, $selected_year) - (calculate_site_partner(0, 0, $i, $selected_year) + calculate_bought_orders(0, $i, $selected_year) + calculate_site_expanse(0, 0, $i, $selected_year))) < 0) class="total_loss_cell" @endif>
									{{number_format(calculate_site_income(0, $i, $selected_year) - (calculate_site_partner(0, 0, $i, $selected_year) + calculate_bought_orders(0, $i, $selected_year) + calculate_site_expanse(0, 0, $i, $selected_year)), 2)}}
								</td>
							@endfor
							<td  @if((calculate_site_income(0, 0, $selected_year) - (calculate_site_partner(0, 0, 0, $selected_year) + calculate_bought_orders(0, 0, $selected_year) + calculate_site_expanse(0, 0, 0, $selected_year))) < 0) class="total_loss_cell" @endif>
								{{number_format(calculate_site_income(0, 0, $selected_year) - (calculate_site_partner(0, 0, 0, $selected_year) + calculate_bought_orders(0, 0, $selected_year) + calculate_site_expanse(0, 0, 0, $selected_year)), 2)}}
							</td>
						</tr>
					</tbody>
				</table>
			</div>	
        </div>
	</div>
</div>					
@endsection