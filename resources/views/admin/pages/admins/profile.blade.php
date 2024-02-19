@extends('admin.layout.profile')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user"></i></span>
                <h3 class="kt-portlet__head-title">Profile Information</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('profile')}}" enctype="multipart/form-data">
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
				@if(session('alert_message'))
				<div class="alert alert-success">{{session('alert_message')}}</div>
				@endif
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Name</label>												
							<input class="form-control" type="text" placeholder="Name" name="name" value="{{Auth::guard('admin')->user()->name}}" id="name" />
						</div>
						<div class="col-md-6">	
							<label>User Name</label>												
							<input class="form-control" type="text" placeholder="User Name" name="user_name" value="{{Auth::guard('admin')->user()->user_name}}" id="user_name" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>E-mail</label>	
							<input class="form-control" type="email"  placeholder="E-mail" value="{{Auth::guard('admin')->user()->email}}" id="email" name="email" />
						</div>				
						<div class="col-md-6">	
							<label>Phone</label>										
							<input class="form-control" type="text"  placeholder="Phone" name="phone" value="{{Auth::guard('admin')->user()->phone}}" id="phone" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Image</label>	
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="image" name="image">
								<label class="custom-file-label" for="image">Choose file</label>
							</div>
						</div>
						<div class="col-md-2">@if(Auth::guard('admin')->user()->image != '')<img src="{{asset(Auth::guard('admin')->user()->image)}}" />@endif</div>
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