@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Orders Status</h3>
            </div>
            @if(permission_checker(Auth::guard('admin')->user()->id, 'add_status'))
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{route('order_status.create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i> New Status</a>

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
					@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_status') || permission_checker(Auth::guard('admin')->user()->id, 'delete_status'))
							<th>Is Counted</th>

							<th>Action</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@foreach ($statuses as $status)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$status->title}}</td>
							@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_status') || permission_checker(Auth::guard('admin')->user()->id, 'delete_status'))


								<td>

									<div class="form-check form-switch">
										<input class="form-check-input activeBtn"   @if($status->is_counted==1)  checked     @endif data-id="{{$status->id}}" type="checkbox" role="switch"    >
									</div>

								</td>


								<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									    @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_status'))
										<a class="dropdown-item" href="{{route('order_status.edit', $status->id)}}">Edit</a>
										@endif
										 @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_status'))
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $status->id }}">Delete</a>
                                        @endif
									</div>
								</div>
                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_status'))
								<div class="modal fade" id="myModal-{{ $status->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete Status</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('order_status/'.$status->id) }}" class="" method="POST">
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

@section('scripts')

<script src="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<link href="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">

<script>
	$(document).on('change','.activeBtn',function (){
		var status_id=$(this).attr('data-id');
		var is_counted=0;
		if( $(this).is(':checked') ){
			is_counted=1;
		}
        $.ajax({
        	type: 'GET',
        	url: "{{route('changeIsCounted')}}",
        	data: {
        		is_counted: is_counted,
        		status_id:status_id,

        	},

        	success: function (res) {
        		if (res['status'] == true) {
        			toastr.success("تمت العملية بنجاح")
        		} else {
        			// location.reload();

        		}
        	},
        	error: function (data) {
        		// location.reload();
        	}
        });



	})
</script>


@endsection