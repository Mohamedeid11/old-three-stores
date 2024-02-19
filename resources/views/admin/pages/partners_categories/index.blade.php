@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Partners Categories</h3>
            </div>
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                	    @if(permission_checker(Auth::guard('admin')->user()->id, 'add_partners_category'))
                    	<a href="#" class="btn btn-brand btn-elevate btn-icon-sm" data-toggle="modal" data-target="#NewExpanseCategory">
                    	    <i class="la la-plus"></i> New Category
                    	</a>
                    	<div class="modal fade" id="NewExpanseCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">New Partner Category</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                        	<form class="kt-form" method="post" action="{{url('partners_categories')}}" enctype="multipart/form-data">
                				{{csrf_field()}}
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Name</label>												
                							<input class="form-control" type="text" required placeholder="Name" name="title" value="{{old('title')}}" id="title" />
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
                    	@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
                    	<a href="#" class="btn btn-danger btn-elevate btn-icon-sm" data-toggle="modal" data-target="#myModalDALL"><i class="fas fa-trash"></i> Delete</a>
                    	<div class="modal fade" id="myModalDALL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">Delete Selected Categories</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                    	<button type="button" class="btn btn-danger" id='delete_selected_orders' task="Delete" url="{{url('partners_categories/partners_categories_task')}}">Delete</button>
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
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_2">
				<thead>
					<tr>
					    @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
						<th class="disable_sort">
                			<label class="kt-checkbox">
                			<input type="checkbox" id="checkAll">
                			<span></span>
                			</label>
            			</th>
            			@else
            			<th>#</th>
            			@endif
				<th>Name</th>
				@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners_category') || permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
				<th>Action</th>
				@endif
			</tr>
			</thead>
			<tbody>
			    @foreach ($cats as $cat)
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
					<td>{{$cat->title}}</td>
					@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners_category') || permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
					<td>
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
							aria-expanded="false">
								Action
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners_category'))
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#ExpanseCategory{{ $cat->id }}">Edit</a>
								@endif
								@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
								<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $cat->id }}">Delete</a>
								@endif
							</div>
						</div>
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_partners_category'))
                    	<div class="modal fade" id="ExpanseCategory{{ $cat->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    	<div class="modal-dialog">
                    	<div class="modal-content">
                    	<div class="modal-header">
                    	<h5 class="modal-title">Edit Partner Category</h5>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    	<span aria-hidden="true">&times;</span>
                    	</button>
                    	</div>
                    	<div class="modal-body">
                        	<form class="kt-form" method="post" action="{{url('partners_categories/'.$cat->id)}}" enctype="multipart/form-data">
                				{{csrf_field()}}
                				<input type="hidden" name="_method" value="PUT" />
                				<div class="form-group">
                					<div class="row">
                						<div class="col-md-12">	
                							<label>Name</label>												
                							<input class="form-control" type="text" required placeholder="Name" name="title" value="{{$cat->title}}" id="title" />
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
                    	@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_partners_category'))
						<div class="modal fade" id="myModal-{{ $cat->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Delete Category</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						<form role="form" action="{{ url('/partners_categories/'.$cat->id) }}" class="" method="POST">
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
        </div>
	</div>
</div>					
@endsection