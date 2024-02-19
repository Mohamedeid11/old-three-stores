@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">{{$product->title}} Images</h3>
            </div>
            
            <div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
                    	<a href="{{url('products_images/'.$product->id.'/create')}}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="fas fa-plus"></i> New Image</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="kt-portlet__body">

            <!--begin::Section-->
            <div class="kt-section">
                
                <div class="kt-section__content">
                        <div class="row">

                        @foreach ($product->images as $slider)
                        <div class="col-sm-4">
                        <div class="slider_solo">
                        <img src="{{asset($slider->image)}}" style="height:300px; width: 100%;" />
                        <div class="action_btns" style="background: black; text-align: center; padding: 5px;">
                        <a href="{{route('products_images.edit', $slider->id)}}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <a data-toggle="modal" href="#myModal-{{ $slider->id }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                        
                        <div class="modal fade" id="myModal-{{ $slider->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Image</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                        <form role="form" action="{{ url('/products_images/'.$slider->id) }}" class="" method="POST">
                        <input name="_method" type="hidden" value="DELETE">
                        {{ csrf_field() }}
                        <p>Are You Sure?</p>
                        <button type="submit" class="btn btn-danger" name='delete_modal'><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        </form>
                        </div>
                        </div>
                        </div>
                        </div>
                                            
                        </div>
                        </div>
                        @endforeach
                        </div>
                        
                </div>
            </div>

            <!--end::Section-->
        </div>

        <!--end::Form-->
    </div>

    <!--end::Portlet-->
@endsection
