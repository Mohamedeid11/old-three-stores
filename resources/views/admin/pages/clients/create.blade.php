@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-user-plus"></i></span>
                <h3 class="kt-portlet__head-title">Add Client</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('clients')}}" enctype="multipart/form-data">
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

					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Address</label>	
							<input class="form-control" type="text"  placeholder="Address" value="{{old('address')}}" id="address" name="address" />
						</div>				
						<div class="col-md-6">	
							<label>City</label>										
							<select class="form-control" name="city" id="city">
								<option value="" disabled selected>Choose City</option>
								@foreach ($cities as $city)
									<option value="{{$city->id}}" @if(old('city') == $city->id) selected @endif>{{$city->title}}</option>
								@endforeach
							</select>
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
							<label>Phone 2</label>										
							<input class="form-control" type="text"  placeholder="Phone 2" name="phone_2" value="{{old('phone_2')}}" id="phone_2" />
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