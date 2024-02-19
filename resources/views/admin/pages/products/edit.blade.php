@extends('admin.layout.main')
@section('styles')
<link rel="stylesheet" href="{{asset('tagsinput/amsify.suggestags.css')}}">
@endsection
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Edit Product</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('products/'.$product->id)}}" enctype="multipart/form-data">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" />
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Name</label>												
							<input class="form-control" type="text" placeholder="Name" name="title" value="{{$product->title}}" id="title" />
							@if ($errors->has('title'))<span class="form-text text-danger"><strong>{{ $errors->first('title') }}</strong></span>@endif
						</div>
						<div class="col-md-6">	
							<label>Price (EGP)</label>												
							<input class="form-control" type="text" placeholder="Price" name="price" value="{{$product->price}}" id="title" />
							@if ($errors->has('price'))<span class="form-text text-danger"><strong>{{ $errors->first('price') }}</strong></span>@endif
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">	
							<label>Main Category</label>												
							<select class="form-control" name="main_cat" id="main_cat_selector" data-url="{{url('get_subs')}}">
								<option value="" disabled selected>Choose Category</option>
								@foreach ($cats as $cat)
									<option value="{{$cat->id}}" @if($cat->id == $main_cat || $product->cat == $main_cat) selected @endif>{{$cat->title}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-6">	
							<label>Category</label>												
							<select class="form-control" name="cat" id="cat_selector">
								<option value="" disabled selected>Choose Category</option>
								@foreach ($sub_cats as $cat)
									<option value="{{$cat->id}}" @if($cat->id == $product->cat) selected @endif>{{$cat->title}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">	
							<label>Description</label>												
							<textarea class="ckeditor" placeholder="Description" name="text" id="text">{{$product->text}}</textarea>
							@if ($errors->has('text'))<span class="form-text text-danger"><strong>{{ $errors->first('text') }}</strong></span>@endif
						</div>
					</div>
				</div>
				<div class="form-group" id="order_number_gr">
					<label>Tags</label>												
					<input type="text" required class="form-control" name="tags" id="product_tags"  data-role="tagsinput" value="{{$all_tags}}" />
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">	
							<label>Colors</label>												
							<select class="form-control" name="colors[]" id="product_color_selector" multiple>
								@foreach ($colors as $color)
									<option value="{{$color->id}}" @if(in_array($color->id, $product_colors)) selected @endif>{{$color->title}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">	
							<label>Sizes</label>												
							<select class="form-control" name="size[]" id="product_size_selector" multiple>
								@foreach ($sizes as $color)
									<option value="{{$color->id}}" @if(in_array($color->id, $product_sizes)) selected @endif>{{$color->title}}</option>
								@endforeach
							</select>
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

@section('scripts')
<script src="{{ asset('tagsinput/jquery.amsify.suggestags.js')}}"></script>
<script>
$(document).ready(function() { 
	$('#product_tags').amsifySuggestags({
		suggestionsAction : {
			url: '{{url("tags_suggestions")}}',
		},
	});
	
});
</script>
@endsection