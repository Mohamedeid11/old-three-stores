@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Colors</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_color'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{route('colors.create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New Color</a>

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
						@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_color') || permission_checker(Auth::guard('admin')->user()->id, 'delete_color'))
						<th>Action</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@foreach ($colors as $categ)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td><div style="background: {{$categ->color}}; width: 12px; display: inline-block; height: 12px; border-radius: 50%; 
							border: 1px solid black; margin-right: 10px;"></div> {{$categ->title}}</td>
							@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_color') || permission_checker(Auth::guard('admin')->user()->id, 'delete_color'))
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_color'))
										<a class="dropdown-item" href="{{route('colors.edit', $categ->id)}}">Edit</a>
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_color'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $categ->id }}">Delete</a>
										@endif
									</div>
								</div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_color'))
								<div class="modal fade" id="myModal-{{ $categ->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete Colors</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('colors/'.$categ->id) }}" class="" method="POST">
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