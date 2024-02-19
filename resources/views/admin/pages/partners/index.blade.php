@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Partners</h3>
            </div>
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                	    @if(permission_checker(Auth::guard('admin')->user()->id, 'add_partners'))
                    	<a href="#" class="btn btn-brand btn-elevate btn-icon-sm" data-toggle="modal" data-target="#NewExpanse">
                    	    <i class="la la-plus"></i> New Partner Expanse
                    	</a>
                    	<div class="modal fade" id="NewExpanse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">New Partner Expanse</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                        	<form class="kt-form" method="post" action="{{url('partners')}}" enctype="multipart/form-data" id="new_expanse">
                				{{csrf_field()}}
                				<div id="new_expanse_res"></div>
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Category</label>												
                							<select class="form-control" required name="cat">
                							    <option value="" disabled selected>Choose Category</option>   
                							    @foreach ($cats as $cat)
                							        <option value="{{$cat->id}}">{{$cat->title}}</option>
                							    @endforeach
                							</select>
                						</div>
                					</div>
                				</div>
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Description</label>												
                							<input class="form-control" type="text" required placeholder="Description" name="title" value="{{old('title')}}" />
                						</div>
                					</div>
                				</div>
                				<div id="new_expanses_teachers"></div>
                				<div id="teacher_expanses_salary_amount">
                    				<div class="form-group">
                    					<div class="row">
                    						<div class="col-md-12">	
                    							<label>Amount (EGP)</label>												
                    							<input class="form-control" type="text" required placeholder="Amount (egp)" name="amount" value="{{old('amount')}}" />
                    						</div>
                    					</div>
                    				</div>

                    				<div class="form-group">
                    					<div class="row">
                    						<div class="col-md-12">	
                    							<label>Date</label>												
                    							<input class="form-control" type="date" required name="date" value="{{old('date')}}" />
                    						</div>
                    					</div>
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
                    	</div>
                    	@endif
                    	@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
                    	<a href="#" class="btn btn-danger btn-elevate btn-icon-sm" data-toggle="modal" data-target="#myModalDALL"><i class="fas fa-trash"></i> Delete</a>
                    	<div class="modal fade" id="myModalDALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">Delete Selected Expanses</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                    	<button type="button" class="btn btn-danger" id='delete_selected_orders' task="Delete" url="{{url('partners/partners_task')}}">Delete</button>
                    	<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                    	</div>
                    	</div>
                    	</div>
                    	</div>
                    	@endif

                    </div>
                </div>
            </div>
        </div>
        
        

        <div class="kt-portlet__body">
            
            <form action="#" method="get" class="kt-form">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">	
                        <div class="form-group">
                        <label class="control-label">Day</label>
                        <select class="form-control" name="day">
                        	<option value="All">All Days</option>
                        	@for ($i = 1; $i <= 31; $i++)
                        	<option value="{{$i}}" @if($filter_day == $i) selected @endif>{{$i}}</option>
                        	@endfor
                        </select>
                        </div>
                        </div>
                        
                        <div class="col-md-2">	
                        <div class="form-group">
                        <label class="control-label">Month</label>
                        <select class="form-control" name="month">
                        	<option value="All">All Months</option>
                        	@for ($i = 1; $i <= 12; $i++)
                        	<option value="{{$i}}" @if($filter_month == $i) selected @endif>{{date('F', strtotime('2019-'.$i.'-01'))}}</option>
                        	@endfor
                        </select>
                        </div>
                        </div>
                        
                        <div class="col-md-2">	
                        <div class="form-group">
                        <label class="control-label">Year</label>
                        <select class="form-control" name="year">
                        	<option value="All">All Years</option>
                        	@for ($i = 2019; $i <= date('Y') + 1; $i++)
                        	<option value="{{$i}}" @if($filter_year == $i) selected @endif>{{$i}}</option>
                        	@endfor
                        </select>
                        </div>
                        </div>
                        
                        <div class="col-md-3">
                        <div class="form-group">
                        <label>Category</label>												
                        <select class="form-control" name="cat" id="cat">
                        	<option value="0">All Categories</option>
                        	@foreach ($cats as $cat)
                        	<option value="{{$cat->id}}" @if($filter_cat == $cat->id) selected @endif>{{$cat->title}}</option>
                        	@endforeach
                        </select>
                        </div>
                        </div>
                        
                        
                        <div class="col-md-3"><label class="control-label"><br /></label><button type="submit" class="btn btn-success btn-block">Search</button></div>	
                    </div>	
                </div>
            </form>
        
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_2">
				<thead>
					<tr>
						@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
						<th class="disable_sort">
                			<label class="kt-checkbox">
                			<input type="checkbox" id="checkAll">
                			<span></span>
                			</label>
            			</th>
            			@else
            			<th>#</th>
            			@endif
			    <th>Date</th>
			    <th>Category</th>
				<th>Description</th>
				<th>Amount (EGP)</th>
				@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners') || permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
				<th>Action</th>
				@endif
			</tr>
			</thead>
			<tbody>
			    @foreach ($expanses as $exp)
    				<tr>
    				@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
    				<td>
        			<label class="kt-checkbox">
        			<input type="checkbox" class="check_single" name="category[]" value="{{$cat->id}}" />
        			<span></span>
        			</label>
        			</td>
					@else
					<td>{{$loop->iteration}}</td>
					@endif
        			<td>{{date('Y-m-d', strtotime($exp->added_at))}}</td>
        			<td>{{$exp->cat_info->title}}</td>
					<td>{{$exp->title}}</td>
					<td>{{$exp->amount}} EGP @php $total_expanses = $total_expanses + $exp->amount @endphp</td>
					@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners') || permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
					<td>
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
							aria-expanded="false">
								Action
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners'))
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#Expanse{{ $exp->id }}">Edit</a>
								@endif
								@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
								<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $exp->id }}">Delete</a>
								@endif
							</div>
						</div>
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners'))
                    	<div class="modal fade" id="Expanse{{ $exp->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">Edit Partner Expanse</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                        	<form class="kt-form expanse_update_from" data-expanse="{{$exp->id}}" method="post" action="{{url('partners/'.$exp->id)}}" enctype="multipart/form-data">
                				{{csrf_field()}}
                				<div id="update_expanse_res{{$exp->id}}"></div>
                				<input type="hidden" name="_method" value="PUT" />
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Category</label>												
                							<select class="form-control" required name="cat">
                							    <option value="" disabled selected>Choose Category</option>   
                							    @foreach ($cats as $cat)
                							        <option value="{{$cat->id}}" @if($exp->cat == $cat->id) selected @endif>{{$cat->title}}</option>
                							    @endforeach
                							</select>
                						</div>
                					</div>
                				</div>                				
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Description</label>												
                							<input class="form-control" type="text" required placeholder="Description" name="title" value="{{$exp->title}}" />
                						</div>
                					</div>
                				</div>
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Amount (EGP)</label>												
                							<input class="form-control" type="text" required placeholder="Amount (EGP)" name="amount" value="{{$exp->amount}}" />
                						</div>
                					</div>
                				</div>
                				
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Date</label>												
                							<input class="form-control" type="date" required name="date" value="{{date('Y-m-d', strtotime($exp->added_at))}}" />
                						</div>
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
                    	</div>
                    	@endif
                    	@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners'))
						<div class="modal fade" id="myModal-{{ $exp->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Delete Expanse</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<form role="form" action="{{ url('/partners/'.$exp->id) }}" class="" method="POST">
						<input name="_method" type="hidden" value="DELETE">
						{{ csrf_field() }}
						<p>Are You Sure?</p>
						<button type="submit" class="btn btn-danger" name='delete_modal'>Delete</button>
						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</form>
						</div>
						</div>
						</div>
						</div>
                        @endif
					</td>
					@endif
				    </tr>
				@endforeach
				</tbody>
			</table>
			<p><b>Total Expanses : </b>{{$total_expanses}} EGP</p>
        </div>
	</div>
</div>					
@endsection