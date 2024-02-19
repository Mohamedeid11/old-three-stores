@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Categories</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_category'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a data-toggle="modal" href="#CreateCategry" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New Category</a>
						<div class="modal fade" id="CreateCategry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">New Category</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<form class="kt-form" method="post" action="{{url('categories')}}" enctype="multipart/form-data" id="ajsuformreload">
									{{csrf_field()}}
									<div id="ajsuform_yu"></div>
									<input type="hidden" name="cat" value="0" />
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">	
												<label>Name</label>												
												<input class="form-control" type="text" placeholder="Name" name="title" value="{{old('title')}}" id="title" />
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-success">Save</button>
								</form>

							</div>
						</div>
						</div>
						</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($categories as $categ)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$categ->title}}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_category'))
									    <a class="dropdown-item"  data-toggle="modal" href="#EditCategory-{{ $categ->id }}">Edit</a>
									    @endif
										<a class="dropdown-item" href="{{route('categories.show', $categ->id)}}">Categories</a>
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_category'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $categ->id }}">Delete</a>
										@endif
									</div>
								</div>
								@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_category'))
								<div class="modal fade" id="EditCategory-{{ $categ->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Edit Category</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form class="kt-form ajsuformreloadedit" method="post" action="{{url('categories/'.$categ->id)}}" enctype="multipart/form-data" 
										data-num="{{$categ->id}}">
											{{csrf_field()}}
											<input type="hidden" name="_method" value="PUT" />
											<input type="hidden" name="cat" value="0" />
											<div id="ajsuform_yu_{{$categ->id}}"></div>
											<div class="form-group">
												<div class="row">
													<div class="col-md-12">	
														<label>Name</label>												
														<input class="form-control" type="text" placeholder="Name" name="title" value="{{$categ->title}}" id="title" />
													</div>
												</div>
											</div>
											<button type="submit" class="btn btn-success">Save</button>
										</form>

									</div>
								</div>
                                </div>
                                </div>
                                @endif
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_category'))
								<div class="modal fade" id="myModal-{{ $categ->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete Category</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('categories/'.$categ->id) }}" class="" method="POST">
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
						</tr>
					@endforeach
				</tbody>
			</table>
        </div>
	</div>
</div>					
@endsection