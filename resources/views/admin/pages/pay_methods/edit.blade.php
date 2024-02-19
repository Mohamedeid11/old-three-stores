@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-credit-card"></i></span>
                <h3 class="kt-portlet__head-title">Edit Payment Method</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('pay_methods/'.$method->id)}}" enctype="multipart/form-data">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" />
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">	
							<label>Name</label>												
							<input class="form-control" type="text" placeholder="Name" name="title" value="{{$method->title}}" id="title" />
							@if ($errors->has('title'))<span class="form-text text-danger"><strong>{{ $errors->first('title') }}</strong></span>@endif
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