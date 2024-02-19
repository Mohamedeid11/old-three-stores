@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user-edit"></i></span>
                <h3 class="kt-portlet__head-title">Edit Admin Password</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('admins/save_password/'.$admin->id)}}" enctype="multipart/form-data">
				{{csrf_field()}}
				@if($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</ul>
					</div>
				@endif
					
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Password</label>												
							<input class="form-control" type="password" placeholder="Password" name="password" value="" id="password" />
						</div>
						<div class="col-md-6">	
							<label>Password Confirmation</label>												
							<input class="form-control" type="password" placeholder="Password Confirmation" name="password_confirmation" value="" id="password_confirmation" />
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
@endsection