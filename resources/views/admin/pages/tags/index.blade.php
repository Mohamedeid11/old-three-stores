@extends('admin.layout.main')

@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Tags</h3>
            </div>
			<div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
						<a href="{{url('products_tags/create')}}" class="btn btn-info"><i class="fas fa-plus"></i> New Tag</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tags as $tag)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$tag->title}}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
									aria-expanded="false">
										Action
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a class="dropdown-item" href="{{route('products_tags.edit', $tag->id)}}">Edit</a>
										<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $tag->id }}">Delete</a>
									</div>
								</div>
								<div class="modal fade" id="myModal-{{ $tag->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Delete Tag</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
								<form role="form" action="{{ url('products_tags/'.$tag->id) }}" class="" method="POST">
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
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
        </div>
	</div>
</div>					
@endsection


@section('scripts')
<script src="{{ asset('tagsinput/tagsinput.js')}}"></script>
<script>
$(document).ready(function() { 

    $('body').on('keyup', function(event){
        if(event.ctrlKey && event.key === 'i')
        {
            $('#order_number_gr .bootstrap-tagsinput input').focus();
        }
    });
});
</script>
@endsection