@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user"></i></span>
                <h3 class="kt-portlet__head-title">Clients</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_client'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{route('clients.create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New Client</a>
                    </div>
                </div>
            </div>


            @endif
        </div>
		<div class="d-flex flex-column mb-7 fv-row col-sm-12">
			<!--begin::Label-->
			<label for="client_id"  class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
				<span class="required mr-1">   Clients</span>
			</label>
			<select id='client_id'  name="client_id"  style='width: 500px;'>
				<option selected disabled>Select For Clients    </option>
				@if(isset($request['client_id']))
				<option value="{{$request['client_id']}}" >{{\App\Client::find($request['client_id'])->name??''}}</option>
				@endif
			</select>
		</div>
        <div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Mobile</th>
						<th>Orders</th>
						<th>Orders Amount</th>
						@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_client') || permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
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
							<td>{{$admin->orders->count()}}</td>
							<td>{{$admin->orders->sum('total_price')}}</td>
							@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_client') || permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_client'))
										<a class="dropdown-item" href="{{route('clients.edit', $admin->id)}}">Edit</a>
                                        @endif
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $admin->id }}">Delete</a>
										@endif
									</div>
								</div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_client'))
								<div class="modal fade" id="myModal-{{ $admin->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    								<div class="modal-dialog">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title">Delete Client</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    											<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    											<form role="form" action="{{ url('clients/'.$admin->id) }}" class="" method="POST">
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
		{{$clients->appends($queryParameters)->links()}}
	</div>
</div>					
@endsection

@section('scripts')

	<script>

		(function () {

			$("#client_id").select2({
				placeholder: 'Channel...',
				// width: '350px',
				allowClear: true,
				ajax: {
					url: '{{route('admin.getClients')}}',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							term: params.term || '',
							page: params.page || 1
						}
					},
					cache: true
				}
			});
		})();

	</script>

	<script>
		$(document).on('change','#client_id',function (){
			var client_id=$(this).val();
			var route="{{route('clients.index')}}?client_id="+client_id;
			window.location.href=route;
		})
	</script>


	@endsection