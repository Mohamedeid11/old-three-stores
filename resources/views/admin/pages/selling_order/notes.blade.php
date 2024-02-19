@extends('admin.layout.main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-clipboard-list"></i></span>
                <h3 class="kt-portlet__head-title">Order Notes</h3>
				</div>
				<div class="kt-portlet__head-toolbar">
            	<div class="kt-portlet__head-wrapper">
                	<div class="kt-portlet__head-actions">
						<a href="#" class="btn btn-warning export_selected_notes" url="{{url('export_notes')}}"><i class="fas fa-download"></i> Export Selected Notes</a>
					</div>
				</div>
			</div>
		</div>
			<div class="kt-portlet__body">
			 <form action="{{url('orders_notes')}}" method="get" class="kt-form">
				<div class="form-group">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>From</label>												
								<input type="date" class="form-control" name="from_date" id="from_date" value="{{$from_date}}" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>To</label>				
								<input type="date" class="form-control" name="to_date" id="to_date" value="{{$to_date}}" />
							</div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
								<label>Status</label>				
								<select class="form-control" name="status" id="status">
                                    <option value="All">All</option>
                                    <option value="Completed" @if($status_filter == 'Completed') selected @endif >Completed</option>
                                    <option value="UnCompleted" @if($status_filter == 'UnCompleted') selected @endif>UnCompleted</option>
								</select>
							</div>
						</div>
						<div class="col-md-3 col-5">
							<div class="form-group">
								<label>REP</label>												
								<select class="form-control" name="admin[]" id="admin_select2" multiple>
									@foreach ($repps as $admin)
										<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_admin)) selected @endif>{{$admin->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-2 col-5">
							<div class="form-group">
								<label>Tags</label>												
								<select class="form-control" name="tags[]" id="tags_select2" multiple>
									@foreach ($tags as $admin)
										<option value="{{$admin->id}}" @if(in_array($admin->id, $selected_tags)) 
											selected @endif>{{$admin->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-1">
							<label class="control-label"><br /></label>
							<button type="submit" class="btn btn-success btn-block"><i class="fas fa-search"></i></button>
						</div>	
					</div>	
				</div>
			</form>
			 <div class="table-responsive">
				<table class="table table-striped- table-bordered table-hover table-checkable" id="notestable">
					<thead>
						<tr>
							
							<th class="disable_sort"></th>
							<th>Date</th>
							<th>Order Num.</th>
							<th>Order Status</th>
							<th style="width: 350px">Note</th>
							<th>Export</th>
							<th>Client</th>
							<th>Added By</th>
							<th>Rep</th>
							@if(Auth::guard('admin')->user()->position == 1)
							<th>Action</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@php $_note = 0; @endphp
						@foreach ($all_notes as $note)
							@if($_note != $note->order)
								@php $note = $note->order_info->notes_desc[0] @endphp
								@if ($note->order_info->hide == 0)
									<tr id="note_row_{{$note->id}}" @if($note->status == 1) class="completed_note" @endif>
										<td>
											<label class="kt-checkbox kt-checkbox--success kt-checkbox--bold">
												<input type="checkbox" class="order_notes_checker" name="item[]" 
												@if($note->status == 1) checked @endif
												data-url="{{url('orders_notes/order_notes_checker')}}" data-item="{{$note->id}}" />
												<span></span>
											</label>
										</td>
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">{{date('Y-m-d h:i A', strtotime($note->created_at))}}</td>
										<td class="more_order_notes_click text-center" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">
											{{$note->order_info->order_number}}
											@if(count($note->order_info->notes) - 1 > 0)
												<span class="badge badge-danger border-rounded" style="border-radius:50%;">{{count($note->order_info->notes) - 1}}</span>
											@endif
										</td>
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">{{$note->order_info->status_info->title}}</td>
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">
											{{$note->note}}
											@foreach ($note->tags as $tag)
												<span class="badge badge-{{$tag->tag_info->color}}">{{$tag->tag_info->title}}</span>
											@endforeach
										</td>
										<td><input type="checkbox" name="export[]" class="check_single" value="{{$note->order}}" /></td>
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">{{$note->order_info->client_info->name}}</td>
										
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">@if($note->admin_info) {{$note->admin_info->name}} @endif</td>
										<td class="more_order_notes_click" data-num="{{$note->order}}" data-url="{{url('load_order_notes')}}">@foreach($note->reps as $ss) @if($loop->iteration > 1) - @endif {{$ss->rep_info->name}} @endforeach</td>
										@if(Auth::guard('admin')->user()->position == 1)
										<td>
										<div class="dropdown">
											<button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
											aria-expanded="false">
												<i class="fas fa-ellipsis-h"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item" data-toggle="modal" href="#myModalEditNote-{{ $note->id }}">Edit Note</a>
												
												<a class="dropdown-item" data-toggle="modal" href="#myModalDelNote-{{ $note->id }}">Delete Note</a>
												<div class="dropdown-divider"></div>
												@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders'))
												<a class="dropdown-item" href="{{route('selling_order.edit', $note->order_info->id)}}">Edit Order</a>
												@endif
												@if(permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order'))
													@if($note->order_info->order_id == 0)
														<a class="dropdown-item" href="{{url('selling_reorder/'.$note->order_info->id)}}">Re-Order</a>
													@else
														<a class="dropdown-item" href="{{url('selling_reorder/'.$note->order_info->order_id)}}">Re-Order</a>										    
													@endif
												@endif
												<a class="dropdown-item" href="{{route('selling_order.show', $note->order_info->id)}}">Order Details</a>
												
												<a class="dropdown-item sellorder_notes_viewer" order-num="{{$note->order_info->id}}" data-toggle="modal" 
												url="{{url('sellorder_notes_viewer')}}" href="#myNotes-{{ $note->order_info->id }}">New Note</a>
												@if($note->order_info->time_lines->count() > 0)
												<a class="dropdown-item" data-toggle="modal" href="#myTime-{{ $note->order_info->id }}">Time Line</a>
												@endif
												@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_client') && 
												permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders'))
												<a class="dropdown-item" data-toggle="modal" href="#OrderClient-{{ $note->order_info->id }}">Client Info</a>
												@endif
												@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))
												<a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $note->order_info->id }}">Delete Order</a>
												@endif
											</div>
										</div>


										<div class="modal fade" id="myModalEditNote-{{ $note->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Edit Note</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<form role="form" action="{{ url('selling_order_note_edit/'.$note->id) }}" class="ajsuformreloadedit" method="POST"
														data-num="{{$note->id}}">
															<div id="ajsuform_yu_{{$note->id}}"></div>
														{{ csrf_field() }}
														<div class="form-group">
															<label>Note</label>
															<textarea name="note" class="form-control">{{$note->note}}</textarea>
														</div>
														<div class="form-group">
															<label>Rep </label>
															<select name="rep[]" multiple class="d-block form-control orders_selector_mul_reps">
																@foreach ($repps as $sa)
																<option value="{{$sa->id}}"  @if(in_array($sa->id, note_reps($note->id))) selected @endif>{{$sa->name}}</option>
																@endforeach
															</select>
														</div>
														<div class="form-group">
															<label>Tags </label>
															<select name="tag[]" multiple class="d-block form-control orders_selector_mul_tags">
																@foreach ($tags as $sa)
																<option value="{{$sa->id}}"  @if(in_array($sa->id, note_tags($note->id))) selected @endif>{{$sa->title}}</option>
																@endforeach
															</select>
														</div>
														<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>
														<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
														</form>
													</div>
												</div>
											</div>
										</div>
										
										<div class="modal fade" id="myModalDelNote-{{ $note->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Delete Note</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<form role="form" action="{{ url('selling_order_note_delete/'.$note->id) }}" class="" method="POST">
														{{ csrf_field() }}
														<p>Are You Sure?</p>
														<button type="submit" class="btn btn-danger" name='delete_modal'>Delete</button>
														<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
														</form>
													</div>
												</div>
											</div>
										</div>
										
										@if(permission_checker(Auth::guard('admin')->user()->id, 'edit_client') && 
										permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders'))
										<div class="modal fade" id="OrderClient-{{ $note->order_info->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
										<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">Client Order</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
										<form role="form" action="{{ url('selling_order_client/'.$note->order_info->id) }}" class="ajsuformreloadedit" 
										method="POST" data-num="{{$note->order_info->id}}">
										<div id="ajsuform_yu_{{$note->order_info->id}}"></div>
										{{ csrf_field() }}
										<div class="form-group">
											<label>Name</label>												
											<input class="form-control" type="text" placeholder="Name" name="name" value="{{$note->order_info->client_info->name}}" id="name" />
										</div>
										<div class="form-group">
											<label>Phone</label>										
											<input class="form-control" type="text"  placeholder="Phone" name="phone" value="{{$note->order_info->client_info->phone}}" id="phone" />
										</div>
										<button type="submit" class="btn btn-danger" name='delete_modal'>Save</button>
										<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
										</form>
										</div>
										</div>
										</div>
										</div>
										@endif

										<div class="modal fade" id="myNotes-{{ $note->order_info->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-lg">
										<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">New Note</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
										<form role="form" action="{{ url('selling_order_notes/'.$note->order_info->id) }}" class="" method="POST" id="ajsuformreload">
										{{ csrf_field() }}
										<div id="ajsuform_yu"></div>
										<div class="form-group">
											<label>Note</label>
											<textarea name="note" class="form-control"></textarea>
										</div>
										<div class="form-group">
											<label>Rep </label>
											<select name="rep[]" multiple class="d-block form-control orders_selector_mul_reps">
												@foreach ($repps as $sa)
												<option value="{{$sa->id}}">{{$sa->name}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Tags </label>
											<select name="tag[]" multiple class="d-block form-control orders_selector_mul_tags">
												@foreach ($tags as $sa)
												<option value="{{$sa->id}}">{{$sa->title}}</option>
												@endforeach
											</select>
										</div>
										<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>
										<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
										</form>
										</div>
										</div>
										</div>
										</div>


										<div class="modal fade" id="myTime-{{ $note->order_info->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-lg">
											<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Order Timeline</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body time_line_list">
											<ul>
												@foreach ($note->order_info->time_lines as $line)
												<li class="row">
													<div class="col-md-3"><b>{{date('Y-m-d h:i A', strtotime($line->created_at))}}</b></div>
													<div class="col-md-9">{{$line->admin_info->name.$line->text}}</div>
												</li>
												@endforeach
											</ul>
												
											
											</div>
											</div>
											</div>
										</div>
										
										@if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))
										<div class="modal fade" id="myModal-{{ $note->order_info->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
										<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">Delete Order</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
										<form role="form" action="{{ url('selling_order/'.$note->order_info->id) }}" class="" method="POST">
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
									@endif
									</tr>
									<tr>
									@if(Auth::guard('admin')->user()->position == 1)
										<td id="load_order_notes_{{$note->order}}" colspan="12" class="hide load_order_notes p-0"></td>
									@else
										<td id="load_order_notes_{{$note->order}}" colspan="11" class="hide load_order_notes p-0"></td>
									@endif
										
									</tr>
								@endif
							@endif
							@php $_note = $note->order @endphp
						@endforeach
					</tbody>
				</table>
			</div>
			{{$all_notes->appends($_GET)->links()}}
		</div>
	</div>
</div>					
@endsection

