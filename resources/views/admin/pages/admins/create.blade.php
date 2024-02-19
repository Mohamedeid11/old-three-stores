@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user-plus"></i></span>
                <h3 class="kt-portlet__head-title">Add Admin</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('admins')}}" enctype="multipart/form-data">
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
							<label>Name</label>												
							<input class="form-control" type="text" placeholder="Name" name="name" value="{{old('name')}}" id="name" />
						</div>
						<div class="col-md-6">	
							<label>User Name</label>												
							<input class="form-control" type="text" placeholder="User Name" name="user_name" value="{{old('user_name')}}" id="user_name" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>E-mail</label>	
							<input class="form-control" type="email"  placeholder="E-mail" value="{{old('email')}}" id="email" name="email" />
						</div>				
						<div class="col-md-6">	
							<label>Phone</label>										
							<input class="form-control" type="text"  placeholder="Phone" name="phone" value="{{old('phone')}}" id="phone" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Position</label>	
							<select class="form-control" id="position" name="position">
								<option value="" disabled selected>Choose Position</option>
								@foreach ($positions as $position)
									<option value="{{$position->id}}">{{$position->position}}</option>
								@endforeach
							</select>
						</div>				
						<div class="col-md-6">	
							<label>Image</label>	
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="image" name="image">
								<label class="custom-file-label" for="image">Choose file</label>
							</div>
						</div>

					</div>
				</div>

				
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
				    <div class="form-group">
				        <label>Permissions</label>												
					    <div class="row">
					         @foreach (get_all_permissions() as $title_name => $title)
					            <div class="col-md-4">
					            <p class="alert alert-dark">{{$title_name}}</p>
					            <div class="row">
					            @foreach ($title as $key => $value)
    					            <div class="col-md-12">
    					                <label class="kt-checkbox">
        									<input type="checkbox" class="check_single" name="permission[]" value="{{$key}}" />
        									<span></span>
        									{{$value}}
    								    </label>
    					            </div>
    					       @endforeach
    					       </div>
    					       </div>
					        @endforeach
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