@extends('admin.layout.main')
@section('styles')
	<link rel="stylesheet" href="{{asset('tagsinput/amsify.suggestags.css')}}">
@endsection

@section('content')
	<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
		<div class="kt-portlet kt-portlet--mobile">
			<div class="kt-portlet__head kt-portlet__head--lg">
				<div class="kt-portlet__head-label">
					<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
					<h3 class="kt-portlet__head-title">Products</h3>
				</div>

				<div class="kt-portlet__head-toolbar">
					<div class="kt-portlet__head-wrapper">
						<div class="kt-portlet__head-actions">
							<a href="#" class="btn btn-warning export_selected_products" url="{{url('export_products')}}"><i class="fas fa-download"></i> Export Selected Products</a>
							<a href="#" class="btn btn-dark" data-toggle="modal" data-target="#myModalTAG"><i class="fas fa-tag"></i> Product Tags</a>
							<a href="{{url('export_products')}}" class="btn btn-info"><i class="fas fa-download"></i> Export Active Products</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="myModalTAG" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">New Tags</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div id="change_selected_orders_status_res"></div>
							<div class="form-group" id="order_number_gr">
								<label>Tags</label>
								<input type="text" class="form-control" name="tags" id="product_tags"  data-role="tagsinput" />
							</div>
							<button type="button" class="btn btn-info" id='add_tags_to_products' task="Change_Status" url="{{url('products/new_tags')}}">Save</button>
							<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet__body">
				<form action="#" method="get" class="kt-form">
					<div class="form-group">
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<label>Category</label>
									<select class="form-control" name="cat[]" id="dashboard_product_item" multiple>
										@foreach ($cats as $cat)
											<option value="{{$cat->id}}" @if(in_array($cat->id, $selected_cat)) selected @endif>{{$cat->title}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3 col-4">
								<div class="form-group">
									<label>Type</label>
									<select class="form-control" name="type">
										<option value="all" @if($selected_type == 'alll') selected @endif>All</option>
										<option value="continue" @if($selected_type == 'continue') selected @endif>Continue</option>
										<option value="discontinue" @if($selected_type == 'discontinue') selected @endif>Discontinue</option>
									</select>
								</div>
							</div>
							<div class="col-md-5 col-4">
								<div class="form-group">
									<label>Product Tags</label>
									<input type="text" class="form-control" name="tags" id="product_tags_filter" value="{{implode(',', $selected_tags)}}" />
								</div>
							</div>

							<div class="d-flex flex-column mb-7 fv-row col-sm-8">
								<!--begin::Label-->
								<label for="product_id"  class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
									<span class="required mr-1">   Products</span>
								</label>
								<select id='product_id'  name="product_id[]"  multiple style='width: 100%;'>
								</select>
							</div>


							<div class="col-md-2 col-4">
								<label class="control-label"><br /></label>
								<button type="submit" class="btn btn-success btn-block">Search</button>
							</div>
						</div>
					</div>
				</form>
				<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
					<thead>
					<tr>
						<th>#</th>
						<th><input type="checkbox" id="checkAll" /></th>
						<th>Name</th>
						<th>Category</th>
						<th>Discontinue</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
					@foreach ($products as $product)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td><input type="checkbox" name="export[]" class="check_single" value="{{$product->id}}" /></td>
							<td>{{$product->title}}</td>
							<td>{{optional($product->cat_info)->title}}</td>
							<td>
								<input type="checkbox" data-url="{{url('product_discontinue/'.$product->id)}}"
									   @if($product->discontinue == 1) checked @endif
									   class="discountine_product_checker" value="1" />
								<div class="d-none">{{$product->discontinue}}</div>
							</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
											aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_product'))
											<a class="dropdown-item" href="{{route('products.edit', $product->id)}}">Edit</a>
											<a class="dropdown-item" data-toggle="modal" href="#SKU{{ $product->id }}">SKU</a>
											<a class="dropdown-item" href="{{url('products/timeline/'.$product->id)}}">Timeline</a>
											{{-- <a class="dropdown-item" href="{{route('products.show', $product->id)}}">Images</a> --}}
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'copy_product'))
											<a class="dropdown-item" href="{{url('product_copy/'.$product->id)}}">Copy</a>
										@endif
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_product'))
											<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $product->id }}">Delete</a>
										@endif
									</div>
								</div>
								<div class="modal fade" id="SKU{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">SKU Product</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<p><b>{{$product->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}</span></p>
												@if (count($product->colors) > 0 && count($product->sizes) > 0)
													@foreach ($product->colors as $color)
														@foreach ($product->sizes as $size)
															<p><b>{{$product->title}} - {{optional($color->color_info)->title}} - {{optional($size->size_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-00{{$color->color}}-00{{$size->size}}</span></p>
														@endforeach
													@endforeach
												@elseif (count($product->colors) > 0 && count($product->sizes) == 0)
													@foreach ($product->colors as $color)
														<p><b>{{$product->title}} - {{optional($color->color_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-00{{$color->color}}-000</span></p>
													@endforeach
												@elseif (count($product->colors) == 0 && count($product->sizes) > 0)
													@foreach ($product->sizes as $size)
														<p><b>{{$product->title}} - {{optional($size->size_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-000-00{{$size->size}}</span></p>
													@endforeach
												@endif
											</div>
										</div>
									</div>
								</div>


								@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_product'))
									<div class="modal fade" id="myModal-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title">Delete Product</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<form role="form" action="{{ url('products/'.$product->id) }}" class="" method="POST">
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
						</tr>
					@endforeach
					</tbody>
				</table>
				{!! $products->appends($queryParameters)->links() !!}
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
			$('#product_tags_filter').amsifySuggestags({
				suggestionsAction : {
					url: '{{url("tags_suggestions")}}',
				},
			});
		});
	</script>


	<script>

		(function () {

			$("#product_id").select2({
				closeOnSelect: false,
				placeholder: 'Search...',
				// width: '350px',
				allowClear: true,
				ajax: {
					url: '{{route('admin.getProducts')}}',
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

@endsection