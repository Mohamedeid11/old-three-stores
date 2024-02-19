@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Add Images</h3>
		</div>
	</div>

	<!--begin::Form-->
	<form class="kt-form kt-form--label-left" id="kt_form_1" method="post"  action="{{ route('products_images.store') }}" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="kt-portlet__body">
		
			@if($errors->any())
				<div class="alert alert-danger">{{$errors->first()}}</div>
			@endif
			<input type="hidden" name="product" value="{{$product->id}}" />
			<div class="form-group m-form__group row">
				<label class="col-lg-2 col-form-label">Images <small class="d-none">(1920 * 1280)</small></label>
				<div class="col-lg-10">
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="image[]" multiple id="image" />
						<label class="custom-file-label" for="image">Choose files</label>
					</div>
				</div>
			</div>
            <div class="form-group m-form__group row">
				<label class="col-lg-2 col-form-label">Color</label>
				<div class="col-lg-10">
                    <select name="color" class="form-control">
                        <option value="0">All Colors</option>
                        @foreach ($product->colors as $color)
                            <option value="{{$color->color_info->id}}">{{$color->color_info->title}}</option>
                        @endforeach
                    </select>
				</div>
			</div>
			
			<div class="kt-portlet__foot">
				<div class="kt-form__actions">
					<div class="row">
						<div class="col-lg-12 text-right">
							<button type="submit" class="btn btn-success">Save</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		<!--end::Form-->
	</div>

@endsection