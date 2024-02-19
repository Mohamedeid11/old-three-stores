@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user"></i></span>
                <h3 class="kt-portlet__head-title">Agents</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_agent'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{route('agents.create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New Agent</a>
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
						<th>Mobile</th>
						<th>Balance</th>
						@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_agent') || permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
						<th>Action</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@foreach ($clients as $admin)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$admin->name}}</td>
							<td>{{$admin->phone}}</td>
							<td>{{$admin->agent_balances->sum('differance')??0}}</td>

						@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_agent') || permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_agent'))
										<a class="dropdown-item" href="{{route('agents.edit', $admin->id)}}">Edit</a>
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_agent'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $admin->id }}">Delete</a>
										@endif
									</div>
								</div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_agent'))
								<div class="modal fade" id="myModal-{{ $admin->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    								<div class="modal-dialog">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title">Delete Admin</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    											<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    											<form role="form" action="{{ url('agents/'.$admin->id) }}" class="" method="POST">
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