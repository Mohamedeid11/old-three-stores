@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Edit Image
			</h3>
		</div>
	</div>

<!--begin::Form-->
<form class="m-form m-form--label-align-left" method="post" action="{{route('products_images.update', $image->id)}}" enctype="multipart/form-data">
		{{ csrf_field() }}
		<input type="hidden" name="_method" value="PUT" />
		
	<div class="kt-portlet__body">
		@if($errors->any())
			<div class="alert alert-danger">{{$errors->first()}}</div>
		@endif

			<div class="form-group m-form__group row">
				<label class="col-lg-2 col-form-label">Image  <small class="d-none">(1920 * 1280)</small></label>
				<div class="col-lg-7">
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="image" id="image" />
						<label class="custom-file-label" for="image">Choose file</label>
					</div>
				</div>
				<div class="col-lg-3">
					<img src="{{asset($image->image)}}">
				</div>
			</div>			
		</div>
        <div class="form-group m-form__group row">
			<label class="col-lg-2 col-form-label">Color</label>
			<div class="col-lg-10">
                <select name="color" class="form-control">
                    <option value="0">All Colors</option>
                    @foreach ($product->colors as $color)
                        <option value="{{$color->color_info->id}}" @if($color->color_info->id == $image->color) selected @endif>{{$color->color_info->title}}</option>
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


