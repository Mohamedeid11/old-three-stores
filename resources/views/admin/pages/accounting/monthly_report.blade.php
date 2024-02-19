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
								<label class="control-label">From</label>
								<input type="date" class="form-control" name="from_date" value="{{ $from_date }}" />
							</div>
                        </div>
                        
                        <div class="col-md-2">	
    	                    <div class="form-group">
								<label class="control-label">To</label>
								<input type="date" class="form-control" name="to_date" value="{{ $to_date }}" />
							</div>
                        </div>
                        
                        <div class="col-md-2"><label class="control-label"><br /></label><button type="submit" class="btn btn-success btn-block">Search</button></div>	
                    </div>	
                </div>
            </form>
        
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="income-tab" data-toggle="tab" href="#income" role="tab" aria-controls="income" aria-selected="true">Income</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="bought-tab" data-toggle="tab" href="#bought" role="tab" aria-controls="bought" aria-selected="false">Bought Orders</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="expanses-tab" data-toggle="tab" href="#expanses" role="tab" aria-controls="expanses" aria-selected="false">Expanses</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="partners-tab" data-toggle="tab" href="#partners" role="tab" aria-controls="partners" aria-selected="false">Partners</a>
              </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="income" role="tabpanel" aria-labelledby="income-tab">
                    <table class="table table-bordered table-striped">
        			    <thead>
        			        <tr>
        			            <th>Date</th>
        			            <th>Order ID</th>
        			            <th>REP</th>
        			            <th>Status</th>
        			            <th>Amount</th>
        			        </tr>
        			    </thead>
        			    <tbody>
						@php
						$selected_rep = -1;
						$rep_amount = 0;
						$rep_orders = 0;
						$total_orders = 0;
						@endphp
        			    @foreach ($incomes as $income)
						@php
							if($selected_rep != $income->delivered_by)
							{
								if($selected_rep > -1)
								{
									?>
									<tr>
										<td colspan="2" style="background: crimson; color: white; font-weight: bold;">Orders</td>
										<td style="background: crimson; color: white; font-weight: bold;"><?= $rep_orders; ?></td>
										<td style="background: crimson; color: white; font-weight: bold;">Total</td>
										<td style="background: crimson; color: white; font-weight: bold;"><?= number_format($rep_amount, 2); ?> EGP</td>
									</tr>
									<?php
									$rep_amount = 0;
									$rep_orders = 0;
								}

								$selected_rep = $income->delivered_by;
							}
							if($income->status == 6)
							{
								$total_income = $total_income + $income->total_price + $income->shipping_fees;
								$rep_amount = $rep_amount + $income->total_price + $income->shipping_fees;
							}

							$rep_orders++;
							$total_orders++;
							@endphp
						<tr>
        			        <td>{{date('d F Y', strtotime($income->collected_date))}}</td>
        			        <td>{{$income->order_number}}</td>
        			        <td>{{optional($income->delivery_info)->name}}</td>
        			        <td>@if($income->status > 0) {{$income->status_info->title}} @else Pending @endif</td>
        			        <td>{{number_format($income->total_price + $income->shipping_fees, 2)}} EGP</td>
        			    </tr>
        			    @endforeach
						@php
						?>
						<tr>
							<td colspan="2" style="background: crimson; color: white; font-weight: bold;">Orders</td>
							<td style="background: crimson; color: white; font-weight: bold;"><?= $rep_orders; ?></td>
							<td style="background: crimson; color: white; font-weight: bold;">Total</td>
							<td style="background: crimson; color: white; font-weight: bold;"><?= number_format($rep_amount, 2); ?> EGP</td>
						</tr>
						<?php
						@endphp
        			    <tr>
        			        <td colspan="2" style="background: black; color: white; font-weight: bold;">Orders</td>
							<td style="background: black; color: white; font-weight: bold;">{{ $total_orders }}</td>
							<td style="background: black; color: white; font-weight: bold;">Total</td>
        			        <td style="background: black; color: white; font-weight: bold;">{{number_format($total_income, 2)}} EGP</td>
        			    </tr>
        			    </tbody>
        			</table> 
        		</div>
        		
        		<div class="tab-pane fade show" id="bought" role="tabpanel" aria-labelledby="bought-tab">
                    <table class="table table-bordered table-striped">
        			    <thead>
        			        <tr>
        			            <th>Date</th>
        			            <th>Order ID</th>
        			            <th>Agent</th>
        			            <th>Amount</th>
        			        </tr>
        			    </thead>
        			    <tbody>
        			    @foreach ($boughts as $expanse)
						@php
							$total_outcome = $total_outcome + $expanse->total_price;
						@endphp
        			    <tr>
        			        <td>{{date('d F Y', strtotime($expanse->created_at))}}</td>
        			        <td>{{$expanse->order_number}}</td>
        			        <td>{{optional($expanse->agent_info)->name}}</td>
        			        <td>{{number_format($expanse->total_price, 2)}} EGP</td>
        			    </tr>
        			    @endforeach
        			    <tr>
        			        <td colspan="4" style="background: black; color: white; font-weight: bold;">Total</td>
        			        <td style="background: black; color: white; font-weight: bold;">{{number_format($total_outcome, 2)}} EGP</td>
        			    </tr>
        			    </tbody>
        			</table> 
        		</div>
                
                <div class="tab-pane fade show" id="expanses" role="tabpanel" aria-labelledby="expanses-tab">
                   <table class="table table-bordered table-striped">
        			    <thead>
        			        <tr>
        			            <th>Date</th>
        			            <th>Category</th>
        			            <th>Description</th>
        			            <th>Amount</th>
        			        </tr>
        			    </thead>
        			    <tbody>
        			    @foreach ($expanses as $expanse)
						@php
						$total_expanses = $total_expanses + $expanse->amount;
						@endphp
						<tr>
        			        <td>{{date('d F Y', strtotime($expanse->added_at))}}</td>
        			        <td>{{$expanse->cat_info->title}}</td>
        			        <td>{{$expanse->title}}</td>
        			        <td>{{number_format($expanse->amount, 2)}} EGP</td>
        			    </tr>
        			    @endforeach
        			    <tr>
        			        <td colspan="3" style="background: black; color: white; font-weight: bold;">Total</td>
        			        <td style="background: black; color: white; font-weight: bold;">{{number_format($total_expanses, 2)}} EGP</td>
        			    </tr>
        			    </tbody>
        			</table> 
        		</div>
        		<div class="tab-pane fade show" id="partners" role="tabpanel" aria-labelledby="partners-tab">
        		     <table class="table table-bordered table-striped">
        			    <thead>
        			        <tr>
        			            <th>Date</th>
        			            <th>Category</th>
        			            <th>Description</th>
        			            <th>Amount</th>
        			        </tr>
        			    </thead>
        			    <tbody>
        			    @foreach ($partners as $expanse)
						@php
						$total_partners = $total_partners + $expanse->amount;
						@endphp
						<tr>
        			        <td>{{date('d F Y', strtotime($expanse->added_at))}}</td>
        			        <td>{{$expanse->cat_info->title}}</td>
        			        <td>{{$expanse->title}}</td>
        			        <td>{{number_format($expanse->amount, 2)}} EGP</td>
        			    </tr>
        			    @endforeach
        			    <tr>
        			        <td colspan="3" style="background: black; color: white; font-weight: bold;">Total</td>
        			        <td style="background: black; color: white; font-weight: bold;">{{number_format($total_partners, 2)}} EGP</td>
        			    </tr>
        			    </tbody>
        			</table> 
        		</div>


        	</div>
        </div>
	</div>
</div>					
@endsection