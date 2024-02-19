@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">{{$product->title}} Timeline</h3>
            </div>
        </div>
        <div class="kt-portlet__body">

            <!--begin::Section-->
            <div class="kt-section">
                <form action="#" method="get" class="kt-form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From</label>												
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{$from_date}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To</label>				
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{$to_date}}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label"><br /></label>
                                <button type="submit" class="btn btn-success btn-block">Search</button>
                            </div>	
                        </div>
                    </div>
                </form>
                <div class="kt-section__content">
                    <div class="table-responsive">
                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th>By</th>
                                <th>Description</th>
                                <th>At</th>
                                <th>QTY At Inventory</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timelines as $event)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{optional($event->color_info)->title}}</td>
                                    <td>{{optional($event->size_info)->title}}</td>
                                    <td>{{optional($event->created_by)->name}}</td>
                                    <td>{{$event->text}}</td>

                                    <td>{{date('Y-m-d h:i A', strtotime($event->created_at))}}</td>
                                    <td>{{$event->qty}}</td>
                                    <td>
                                        @if($event->order_type == 2)
                                            <a href="{{route('buying_order.show', $event->order)}}" class="btn btn-info" target="_blank">Details</a>
                                        @elseif($event->order_type == 1)
                                            <a href="{{route('selling_order.show', $event->order)}}" class="btn btn-info" target="_blank">Details</a>
                                        @else

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
