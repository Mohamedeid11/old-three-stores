@extends('admin.layout.main')

@section('content')

<!-- begin:: Content -->

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">

    <div class="kt-portlet kt-portlet--mobile">

        <div class="kt-portlet__head kt-portlet__head--lg">

			<div class="kt-portlet__head-label">

				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>

                <h3 class="kt-portlet__head-title">Selling Order Details</h3>

            </div>

        </div>

        <div class="kt-portlet__body">

				<div class="form-group">

					<div class='row'>

						<div class='col-md-4'><p><b>Order No. : </b>{{$order->order_number}}</p></div>

						<div class='col-md-4'><p><b>Name : </b>{{$order->client_info->name}}</p></div>

						<div class='col-md-4'><p><b>Phone No. : </b>{{$order->client_info->phone}}</p></div>

						<div class='col-md-4'><p><b>Email : </b>{{$order->client_info->email}}</p></div>

						<div class='col-md-8'><p><b>Address : </b>{{$order->client_info->address}} - {{$order->client_info->city_info->title}}</p></div>


						<div class='col-md-4'>
							<input type="date" name="collect_date" class="form-control selling_order_status" num="{{$order->id}}" url="{{url('selling_order_collect_date')}}" 
							@if(!empty($order->collected_date)) value="{{date('Y-m-d', strtotime($order->collected_date))}}" @endif />
						</div>


						<div class='col-md-4'>
							<select name="status" class="form-control selling_order_status" num="{{$order->id}}" url="{{url('selling_order_status')}}">
								@foreach ($statuss as $status)
									<option value="{{$status->id}}" @if($status->id == $order->status) selected @endif>{{$status->title}}</option>
								@endforeach
							</select>
						</div>

				</div>

				<hr />



				<table class="table table-striped">

					<thead>

						<tr>

							<th>#</th>

							<th>Product</th>

							<th>Qty</th>

							<th>Price</th>

							<th>Color</th>

							<th>Size</th>

							<th>Total</th>

							<th>Action</th>

						</tr>

					</thead>

					<tbody>

						@foreach ($order->items as $item)

							<tr>

								<td>{{$loop->iteration}}</td>

								<td>{{$item->product_info->title}}</td>

								<td>{{$item->qty}}</td>

								<td>{{$item->price}} EGP</td>

								<td>@if($item->color > 0) {{$item->color_info->title}} @endif</td>

								<td>@if($item->size > 0) {{$item->size_info->title}} @endif</td>

								<td>{{$item->qty * $item->price}} EGP</td>

								<td>

								    <a class="btn btn-danger" data-toggle="modal" href="#myModal-{{ $item->id }}"><i class="fas fa-trash"></i></a>

                                    <div class="modal fade" id="myModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    								<div class="modal-dialog">

    								<div class="modal-content">

    								<div class="modal-header">

    									<h5 class="modal-title">Delete Order Item</h5>

    									<button type="button" class="close" data-dismiss="modal" aria-label="Close">

    									<span aria-hidden="true">&times;</span>

    									</button>

    								</div>

    								<div class="modal-body">

        								<form role="form" action="{{ url('delete_selling_order_item/'.$item->id) }}" class="" method="POST">

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

					<tr class="bg-dark text-white">

					    <td>Shipping Fees</td>

					    <td>{{$order->shipping_fees}} EGP</td>

						<td colspan="4"></td>

						<td colspan="2">{{$totals + $order->shipping_fees}} EGP</td>

					</tr>

					</tbody>

				</table>



		</div>

	</div>

</div>					

@endsection