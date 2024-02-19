@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Cities</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_city'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{route('cities.create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New City</a>

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
						<th>Shipping</th>
						<th>Mylerz Shipping</th>
						@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_city') || permission_checker(Auth::guard('admin')->user()->id, 'delete_city'))
						<th>Action</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@foreach ($cities as $city)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$city->title}}</td>
							<td>{{$city->shipment}} EGP</td>
							<td>@if($city->mylerz_shipping == 1) <span class="badge badge-success">Active</span> @else <span class="badge badge-danger">Not Active</span> @endif</td>
							@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_city') || permission_checker(Auth::guard('admin')->user()->id, 'delete_city'))
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_city'))
										<a class="dropdown-item" href="{{route('cities.edit', $city->id)}}">Edit</a>
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_city'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $city->id }}">Delete</a>
										@endif
									</div>
								</div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_city'))
								<div class="modal fade" id="myModal-{{ $city->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete City</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('cities/'.$city->id) }}" class="" method="POST">
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