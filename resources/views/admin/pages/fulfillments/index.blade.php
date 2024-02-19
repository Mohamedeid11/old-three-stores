@extends('admin.layout.main')
@section('styles')
    <style>
        .yellow-square {
            width: 200px;
            height: 40px;
            background-color: #f0a202;
            color: white;
            text-align: center;
            line-height: 40px;
            padding: 2px 5px; /* 10px top and bottom, 15px right and left */
        }
        .red-square {
            width: 200px;
            height: 40px;
            background-color: red;
            color: white;
            text-align: center;
            line-height: 40px;
            padding: 2px 5px; /* 10px top and bottom, 15px right and left */
        }
    </style>
@endsection
@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                    <h3 class="kt-portlet__head-title">Fullfillment</h3>
                </div>

            </div>
            <div style="display:none" id="check_image">

            </div>
            <div class="kt-portlet__body">
                <form action="{{route('fulfillments.index')}}" method="get" class="kt-form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="All">All</option>
                                        <option value="1" @if(isset($request['status']))  @if($request['status']==1)  selected @endif   @endif >Pending</option>
                                        <option value="11" @if(isset($request['status']))  @if($request['status']==11)  selected @endif   @endif >Partly Available</option>
                                        <option value="2" @if(isset($request['status']))  @if($request['status']==2)  selected @endif   @endif > Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label"><br /></label>
                                <button type="submit" class="btn btn-success btn-block">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_2">
                    <thead>
                    <tr>
                        <th class="disable_sort">
                        </th>
                        <th>Order Num.</th>
                        <th>Client</th>
                        <th>City</th>
                        <th>Item</th>
                        <th>Items</th>
                        <th>Date</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <label class="kt-checkbox kt-checkbox--success kt-checkbox--bold">
                                                <input type="checkbox" class="fulfillment_checker_change" name="item[]"
                                                       @if($item->fulfillment==1) checked    @endif value="{{$item->id}}"
                                                        data-id="{{$item->id}}"  />
                                                <span></span>
                                            </label>
                                            <span id="loader_{{$item->id}}"></span>
                                        </td>
                                        <td>{{$item->order_info->order_number??''}}</td>
                                        <td>{{ optional($item->order_info->client_info)->name ?? '' }}</td>
                                        <td>{{optional($item->order_info->city_info)->title??''}}</td>
                                        <td>
                                            {{optional($item->product_info)->title}}	@if($item->color > 0) <b> {{optional($item->color_info)->title}}</b> @endif  		@if($item->size > 0) <b> {{optional($item->size_info)->title}}</b> @endif
                                        </td>
                                        <td>


                                            <span @if($item->qty==1) class=" h5"@elseif($item->qty>1 ) class="yellow-square h5"   @else  class="red-square h5"  @endif>{{$item->qty}}</span>


                                        </td>
                                        <td>{{$item->order_info->created_at}}</td>
                                        <td>
                                            {{$item->note}}
                                            @foreach ($item->order_info->tags??[] as $tag)
                                                <span class="badge badge-danger mb-1">{{ optional($tag->tag)->title }}</span>
                                            @endforeach
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



    <script>
        var loader_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/loader2.gif">';
        var true_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/true.png">';
        var warning_img='<img height="25px" width="25px" src="{{asset('loaders/')}}/warning.png">';
        $('#check_image').html(warning_img);
        $(document).on('change', '.fulfillment_checker_change', function() {
            var id = $(this).val();
            var warn_img=warning_img;
            var load_img=loader_img;

            var action = "{{route('admin.fulfillments_action',':id')}}";
                action=action.replace(':id',id)
            $.ajax({
                type: 'POST',
                beforeSend: function () {
                    $(`#loader_${id}`).html(load_img);
                },
                data: {  },
                url: action,
                success: function(data) {

                    if (data.status == 200) {
                        $(`#loader_${id}`).html(true_img);
                        toastr.success((data.message) ?? 'تم تحديث البيانات بنجاح');
                    } else
                        toastr.error('عذرا هناك خطأ فني 😞');

                },

                error: function(data) {
                    $(`#loader_${id}`).html(warn_img);

                }
            });
        });
    </script>

@endsection
