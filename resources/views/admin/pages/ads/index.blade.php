@extends('admin.layout.main')
@section('styles')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <style>
        #table-wrapper {
            position: relative;
        }

        #table-scroll {
            /*height: 150px;*/
            overflow: auto;
            margin-top: 20px;
        }

        #table-wrapper table {
            width: 100%;

        }

        /*#table-wrapper table * {*/
        /*    background: yellow;*/
        /*    color: black;*/
        /*}*/

        #table-wrapper table thead th .text {
            position: absolute;
            top: -20px;
            z-index: 2;
            height: 20px;
            width: 35%;
            border: 1px solid red;
        }
    </style>
@endsection
@section('content')

    @php

        $all_total_result=$total_count=$all_total_cost_per_result=$all_total_first_total=$all_total_order=$all_total_order_value=$all_total_second_total=$all_total_roi=$all_total_revenue=$all_total_cpo=$all_total_result_per_order=0;



    @endphp

    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                    <h3 class="kt-portlet__head-title">Ads</h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_ads'))
                            <div class="kt-portlet__head-actions">
                                <a href="{{ route('export-ads') }}" class="btn btn-outline-success">Export Ads</a>
                                <button class="btn btn-outline-danger " id="importBtn">Import Ads</button>

                                <button id="addBtn" class="btn btn-brand btn-elevate btn-icon-sm"><i
                                            class="la la-plus"></i>
                                    New Add
                                </button>


                            </div>
                        @endif
                    </div>
                </div>


            </div>


            <div class="kt-portlet__body">

                <form action="#" method="get" class="kt-form">
                    <div class="form-group">
                        <div class="row">


                            <div class="col-md-3 col-5">
                                <div class="form-group">
                                    <label>From Date </label>
                                    <input type="date" class="form-control" name="fromDate" id="fromDate"
                                           @if(isset($request['fromDate']))  value="{{$request['fromDate']}}"
                                           @else value="{{date("Y-m-d", strtotime("yesterday"))}}" @endif />
                                </div>
                            </div>
                            <div class="col-md-3 col-5">
                                <div class="form-group">
                                    <label>To Date </label>
                                    <input type="date" class="form-control" name="toDate" id="toDate"
                                           @if(isset($request['toDate']))  value="{{$request['toDate']}}"
                                           @else value="{{date("Y-m-d")}}" @endif />

                                </div>
                            </div>

                            <div class="col-md-2 col-2">
                                <label> Status </label>
                                <select class="form-control" name="status">
                                    <option selected disabled></option>
                                    <option @if($status==1) selected @endif value="1">Active</option>
                                    <option @if($status==-1) selected @endif value="-1">Not Active</option>
                                    <option @if($status=='all') selected @endif value="all">All</option>

                                </select>

                            </div>


                            <div class="col-md-4 col-4">
                                <!--begin::Label-->
                                <label for="platform_id"
                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Platforms</span>
                                </label>
                                <select id='platform_id' name="platform_id" style='width: 100%;'>
                                    <option selected disabled>- Search for Platform</option>
                                    @isset($request['platform_id'])
                                        <option selected
                                                value="{{$request['platform_id']}}">{{\App\OrderTag::find($request['platform_id'])->title??''}}</option>

                                    @endisset
                                </select>
                            </div>


                            <div class="d-flex flex-column mb-7 fv-row col-sm-6">
                                <!--begin::Label-->

                                <label for="product_id_search"
                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Product</span>
                                </label>
                                <select id='product_id_search' name="product_id" style='width: 100%;'>
                                    <option selected disabled>- Search for Product</option>
                                    @isset($request['product_id'])
                                        <option selected
                                                value="{{$request['product_id']}}">{{\App\Product::find($request['product_id'])->title??''}}</option>
                                    @endisset
                                </select>
                            </div>


                            <div class="d-flex flex-column mb-7 fv-row col-sm-4">
                                <!--begin::Label-->
                                <label for="tag_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Tags</span>
                                </label>
                                <select id='tag_id' name="tag_id" style='width: 100%;'>
                                    <option selected disabled>- Search for Tag</option>
                                    @isset($request['tag_id'])
                                        <option selected
                                                value="{{$request['tag_id']}}">{{\App\TagGroup::find($request['tag_id'])->title??''}}</option>

                                    @endisset
                                </select>
                            </div>


                            <div class="col-md-2 col-2">
                                <label class="control-label"><br/></label>
                                <button type="submit" class="btn btn-success btn-block">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="table-wrapper">
                    <div id="table-scroll">
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Platform</th>
                                <th>Active</th>
                                <th>Ad Number</th>
                                <th>Tags</th>
                                <th>Products</th>
                                <th>Result</th>
                                <th>Cost Per Result</th>
                                <th>Total</th>
                                <th>Order</th>
                                <th>Order Value</th>
                                <th>Total</th>
                                <th>Rol</th>
                                <th>Revenue</th>
                                <th>CPO</th>
                                <th>Results Per Order</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_data">
                            {{--                            @if(isset($ads) && is_array($ads))--}}

                            @forelse ($ads ??[] as $key=>$ad)
{{--                                @php--}}
{{--                                   dd( \App\Ad::where('parent_id',$ad->id)->where('date',$ad->date)->pluck('ad_number')->toArray());--}}
{{--                                @endphp--}}
                                @php
                                    $total_count=$total_count+1;
                                        $dateFilter=[$ad->date.' '.'00 00 00',$ad->date.' '.'23 59 59'];

                                  $invoiceDate= date('Y-m-d', strtotime($ad->date. ' + 10 days')).' 23::59::59';
                                $platform_ides=\App\AdPlatform::where('ad_id',$ad->id)->pluck('platform_id')->toArray();

                                @endphp

                                <tr id="tr_{{$ad->id}}">
                                    <td>{{$ad->date}}</td>
                                    <td>
                                        @forelse($ad->platforms ??[] as $platform )
                                            {{$platform->title??''}} <br>
                                        @empty

                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_ads')) class="form-check-input activeBtn"
                                                   @else class="form-check-input" @endif @if($ad->status==1)  checked
                                                   @endif data-id="{{$ad->id}}" type="checkbox" role="switch">
                                        </div>
                                    </td>
                                    @php
                                        $child_ads = \App\Ad::where('parent_id',$ad->id)->get();

                                    @endphp

                                    <td>
                                        @if(count($child_ads) >0)
                                            @foreach($child_ads as $indexHr => $ad_number)
                                                {{$ad_number->ad_number}}
                                                @if($indexHr < count($child_ads) - 1)
                                                    <hr>
                                                @endif
                                            @endforeach
                                        @else
{{--                                            {{ $ad->ad_number }}--}}
                                        @endif
                                    </td>
                                    <td>

                                        @forelse($ad->products ??[] as $product)
                                            <span style="display:none">
                                                @php
                                                    $tags_ids= \App\productTag::where('product_id', $product->id)->pluck('tag_id')->toArray();
                                                    $tags=\App\TagGroup::whereIn('id',$tags_ids)->get();
                                                @endphp
                                            </span>
                                            @forelse($tags ??[] as $tag)
                                                {{$tag->title??''}}
                                                <br>
                                            @empty
                                            @endforelse
                                        @empty

                                        @endforelse
                                    </td>
                                    <td>

                                        @forelse($ad->products ??[] as $product)
                                            {{$product->title??''}}
                                            <br>
                                        @empty

                                        @endforelse

                                    </td>


                                    <span style="display:none;">
                                        <span style="display: none">

                                            {{ $child_ad_total=\App\Ad::where('parent_id',$ad->id)->where('date',$ad->date)->sum(DB::raw('result * cost_per_result'))}}
                                        </span>
                                        @if($ad->result==0)
                                            {{round($ad->cost_per_result+$child_ad_total,2)}}
                                        @else
                                            {{round($ad->total+$child_ad_total,2)}}
                                        @endif
                                        <span style="display: none">
                                              @if($ad->result==0)
                                                {{$total1= $ad->cost_per_result+$child_ad_total}}
                                            @else
                                                {{$total1=$ad->total+$child_ad_total}}
                                            @endif
                                            {{$all_total_first_total=$all_total_first_total+$total1}}
                                        </span>
                                    </span>
                                    @php
                                        $child_ad_result=\App\Ad::where('parent_id',$ad->id)->where('date',$ad->date)->sum('result');
                                        $result=$ad->result+$child_ad_result;
                                        $all_total_result=$all_total_result+$result;

                                        if($result==0){
                                            $cost_per_result=$total1;

                                        }else {
                                            $cost_per_result = $total1 / $result;

                                        }
                                        $all_total_cost_per_result = $all_total_cost_per_result + $cost_per_result;
                                    @endphp

                                    <td>
                                        {{round($ad->result+$child_ad_result,2)}}
                                    </td>

                                    <td>
                                        @if($result==0)
                                            {{round($total1,2)}}
                                        @else
                                            {{round($total1/$result,2)}}
                                        @endif
                                    </td>



                                    <td>
                                        <span style="display: none">

                                            {{ $child_ad_total=\App\Ad::where('parent_id',$ad->id)->where('date',$ad->date)->sum(DB::raw('result * cost_per_result'))}}
                                        </span>
                                        @if($ad->result==0)
                                            {{round($ad->cost_per_result+$child_ad_total,2)}}
                                        @else
                                            {{round($ad->total+$child_ad_total,2)}}
                                        @endif

                                    </td>




                                    <td>

                                        @php
                                            $ordersNummbers=0;
                                        @endphp


                                        <span style="display: none;">

                                        @forelse($ad->products ??[] as $product)
                                                @php
                                                $order_ides=\App\SellOrderItem::where('product',$product->id)->pluck('order')->toArray();
                                                $orders=\App\SellOrder::whereIn('id',$order_ides)->where('hide',0)->whereBetween('created_at', array($ad->date . " 00:00:00", $ad->date . " 23:59:59"))->get();
                                                @endphp
                                       	     	@forelse($orders ??[] as $order)



                                                        {{$hasMatchingTag = false}}
                                                        @forelse($order->tags as $tag)
                                                            @if (in_array($tag->tag_id, $platform_ides))
                                                                {{$hasMatchingTag = true}}
                                                                @break

                                                            @endif
                                                        @empty
                                                        @endforelse
                                                        @if($hasMatchingTag)
                                                            {{$ordersNummbers=$ordersNummbers+1}}

                                                        @endif


                                                @empty
                                                @endforelse

                                        @empty
                                        @endforelse
                                                    										 </span>

                                            {{$ordersNummbers}}


                                        <span style="display:none">
                                            {{$all_total_order=$all_total_order+$ordersNummbers}}

                                        </span>

                                    </td>

                                    <td>

                                        @php
                                            $orderValue=0;
                                            $products_buy=0;
                                        @endphp

                                        @forelse($ad->products ??[] as $product)
                                            <span style="display: none;">
       @php
           $order_ides=\App\SellOrderItem::where('product',$product->id)->pluck('order')->toArray();
           $orders=\App\SellOrder::whereIn('id',$order_ides)->where('hide',0)->whereBetween('created_at', array($ad->date . " 00:00:00", $ad->date . " 23:59:59"))->get();
       @endphp
                                                @forelse($orders ??[] as $order)



                                                        {{$hasMatchingTag = false}}


                                                        @forelse($order->tags as $tag)
                                                            @if (in_array($tag->tag_id, $platform_ides))
                                                                {{$hasMatchingTag = true}}
                                                                @break

                                                            @endif
                                                        @empty
                                                        @endforelse



                                                        @if($hasMatchingTag)
                                                            {{$orderValue=$orderValue+$order->total_price??'0'}}
                                                            @php($product_buy=0)
                                                            @forelse(\App\SellOrderItem::where('order',$order->id)->where('hide',0)->get() as $details)
                                                                @if($invoice=\App\BuyOrderItem::orderBy('id','DESC')->where('product',$details->product)->where('qty','>',0)->where('hide',0)->where('price','>',5)->where('created_at','<=',$invoiceDate)->first()??0)
                                                                    {{$product_buy=$product_buy+$invoice->price*$details->qty}}
                                                                @endif
                                                            @empty
                                                            @endforelse
                                                            {{$products_buy=$products_buy+$product_buy}}
                                                        @endif
                                                @empty
                                                @endforelse

                                            </span>
                                        @empty

                                        @endforelse

                                        @if($ordersNummbers>0)
                                            {{round($orderValue/$ordersNummbers,2)}}
                                        @else
                                            {{round($orderValue,2)}}
                                        @endif
                                        <span style="display:none">
                                            @if($ordersNummbers>0)
                                                {{$final_order_value=$orderValue/$ordersNummbers}}
                                            @else
                                                {{$final_order_value=$orderValue}}
                                            @endif
                                            {{$all_total_order_value=$all_total_order_value+$final_order_value}}


                                        </span>
                                    </td>
                                    <td>
                                        {{$secondTotal=$orderValue}}
                                        <span style="display:none">
                                            {{$all_total_second_total=$all_total_second_total+$secondTotal}}

                                        </span>
                                    </td>
                                    <td>


                                        @if($total1==0)
                                            {{round($secondTotal-$products_buy,2)}}

                                        @else
                                            {{round(($secondTotal-$products_buy)/$total1,2)}}

                                        @endif

                                        <span style="display:none">
                                              @if($total1==0)
                                                {{$roi=($secondTotal-$products_buy)}}

                                            @else
                                                {{$roi=($secondTotal-$products_buy)/$total1}}

                                            @endif

                                            {{$all_total_roi=$all_total_roi+$roi}}

                                        </span>

                                    </td>
                                    <td>
                                        {{round($secondTotal-$products_buy-$total1,2)}}
                                        <span style="display:none">
                                            {{$revenue=$secondTotal-$products_buy-$total1}}
                                            {{$all_total_revenue=$all_total_revenue+$revenue}}

                                        </span>

                                    </td>

                                    <td>

                                        @if($ordersNummbers>0)
                                            {{round($total1/$ordersNummbers,2)}}
                                        @else
                                            {{round($total1,2)}}
                                        @endif
                                        <span style="display: none">
                                           @if($ordersNummbers>0)
                                                {{$cpo=$total1/$ordersNummbers}}
                                            @else
                                                {{$cpo=$total1}}
                                            @endif
                                            {{$all_total_cpo=$all_total_cpo+$cpo}}
                                        </span>
                                    </td>

                                    <th>
                                        @if($ordersNummbers>0)
                                            {{round($ad->result/$ordersNummbers,2)}}
                                        @else
                                            {{round($ad->result,2)}}
                                        @endif
                                        <span style="display:none">
                                            @if($ordersNummbers>0)
                                                {{$result_per_order=$ad->result/$ordersNummbers}}
                                            @else
                                                {{$result_per_order=$ad->result}}
                                            @endif
                                            {{$all_total_result_per_order=$all_total_result_per_order+$result_per_order}}
                                        </span>
                                    </th>


                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link" type="button" id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_ads'))'))
                                                <button class="dropdown-item editBtn" data-id="{{$ad->id}}">Edit
                                                </button>
                                                @endif
                                                @if(permission_checker(Auth::guard('admin')->user()->id, 'add_ads'))
                                                    <button class="dropdown-item" data-id="{{$ad->id}}">Re-Ad</button>
                                                @endif
                                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_ads'))
                                                    <button class="dropdown-item delete" data-id="{{$ad->id}}">Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>


                                    </td>


                                </tr>
                            @empty
                            @endforelse
                            {{--                            @endif--}}
                            </tbody>
                            <span style="display:none">
                               @if($total_count==0)
                                    {{$total_count=1}}
                                @endif
                            </span>

                            <tfoot>
                            <td colspan="6">
                                <h3 style="text-align:center">Total:</h3>
                            </td>
                            <td>
                                <h5>{{round($all_total_result,2)}}</h5>

                            </td>
                            <td>
                                 @if($total_count!=0)
                                    <h5>{{round($all_total_cost_per_result/$total_count, 2)}}</h5>
                                @else
                                    <h5>{{round($all_total_cost_per_result, 2)}}</h5>

                                @endif
                               

                            </td>
                            <td>
                                 <h5>{{round($all_total_first_total,2)}}</h5>
                               

                            </td>

                            <td>
                                <h5> {{round($all_total_order,2)}}</h5>

                            </td>
                            <td>
                                @if($all_total_order!=0)
                                    <h5> {{round($all_total_second_total/$all_total_order, 2)}}</h5>
                                @else
                                    <h5> {{round($all_total_second_total, 2)}}</h5>

                                @endif
                            </td>
                            <td>

                                <h5>  {{round($all_total_second_total,2)}}</h5>
                            </td>
                            <td>
                                @if($all_total_first_total!=0)
                                    <h5> {{round($all_total_revenue/$all_total_first_total, 2)}}</h5>
                                @else
                                    <h5> {{round($all_total_revenue, 2)}}</h5>

                                @endif


                            </td>
                            <td>
                                <h5>{{round($all_total_revenue,2)}}</h5>

                            </td>
                            <td>
                                <h5>
                                    @if($all_total_order!=0)
                                        {{round($all_total_first_total/$all_total_order, 2)}}
                                    @else
                                        {{round($all_total_first_total, 2)}}

                                    @endif
                                </h5>

                            </td>
                            <td>
                                <h5>
                                    @if($all_total_order!=0)
                                        {{round($all_total_result/$all_total_order, 2)}}
                                    @else
                                        {{round($all_total_result, 2)}}

                                    @endif
                                </h5>
                            </td>
                            <td>

                            </td>

                            </tfoot>
                        </table>
                    </div>
                </div>
                {!! $ads->appends($queryParameters)->links() !!}
            </div>
        </div>
    </div>


    <div class="modal" id="editOrCreate" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ads</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">
                </div>

            </div>
        </div>
    </div>
@endsection


@section('scripts')

    <script src="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js
"></script>
    <link href="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css
" rel="stylesheet">
    <script>
        var loader = `<div class="linear-background">
		<div class="inter-crop"></div>
		<div class="inter-right--top"></div>
		<div class="inter-right--bottom"></div>
	</div>`;
        $(document).on('click', '#importBtn', function () {

            $('#modal-body').html(loader)
            $('#operationType').text('Import');
            $('#editOrCreate').modal('show')


            setTimeout(function () {
                $('#modal-body').load("{{route("import-ads")}}")
            }, 500)

        });

        $(document).on('click', '#addBtn', function () {

            $('#modal-body').html(loader)
            $('#operationType').text('اضافة');
            $('#editOrCreate').modal('show')


            setTimeout(function () {
                $('#modal-body').load("{{route("ads.create")}}")
            }, 500)

        });


        // Create New Data By Ajax
        $(document).on('submit', 'Form#addForm', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = $('#addForm').attr('action');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $('#addButton').html('<span style="margin-right: 4px;">انتظر ..</span><i class="bx bx-loader bx-spin"></i>').attr('disabled', true);
                },
                success: function (data) {
                    if (data.status == 200) {
                        // $('#main-datatable').DataTable().ajax.reload(null, false);
                        // show custom message or use the default
                        toastr.success((data.message) ?? 'تم اضافة البيانات بنجاح');
                        $('#addForm')[0].reset()

                    } else
                        toastr.error('عذرا هناك خطأ فني 😞');
                    $('#addButton').html(`اضافة`).attr('disabled', false);
                },
                error: function (data) {
                    if (data.status === 500) {
                        toastr.error('عذرا هناك خطأ فني 😞');
                    } else if (data.status === 422) {
                        var errors = $.parseJSON(data.responseText);
                        $.each(errors, function (key, value) {
                            if ($.isPlainObject(value)) {
                                $.each(value, function (key, value) {
                                    toastr.error(value);
                                });
                            }
                        });
                    } else
                        toastr.error('عذرا هناك خطأ فني 😞');
                    $('#addButton').html(`اضافة`).attr('disabled', false);
                },//end error method

                cache: false,
                contentType: false,
                processData: false
            });
        });


        $(document).on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $('#modal-body').html(loader)
            $('#operationType').text('تعديل');
            $('#editOrCreate').modal('show')
            var editUrl = "{{route("ads.edit",':id')}}";
            editUrl = editUrl.replace(':id', id)
            setTimeout(function () {
                $('#modal-body').load(editUrl)
            }, 500)
        });

        // Update Script using Ajax
        $(document).on('submit', 'Form#updateForm', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = $('#updateForm').attr('action');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $('#editBtn').html('<span style="margin-right: 4px;">انتظر ..</span><i class="bx bx-loader bx-spin"></i>').attr('disabled', true);
                    ;
                },
                success: function (data) {
                    $('#editBtn').html(`تحديث`).attr('disabled', false);
                    if (data.status == 200) {
                        toastr.success((data.message) ?? 'تم تحديث البيانات بنجاح');
                    } else
                        toastr.error('عذرا هناك خطأ فني 😞');

                    $('#editOrCreate').modal('hide')
                },
                error: function (data) {
                    if (data.status === 500) {
                        toastr.error('عذرا هناك خطأ فني 😞');
                    } else if (data.status === 422) {
                        var errors = $.parseJSON(data.responseText);
                        $.each(errors, function (key, value) {
                            if ($.isPlainObject(value)) {
                                $.each(value, function (key, value) {
                                    toastr.error(value, 'Error');
                                });
                            }
                        });
                    } else
                        toastr.error('عذرا هناك خطأ فني 😞');
                    $('#editBtn').html(`تحديث`).attr('disabled', false);
                },//end error method

                cache: false,
                contentType: false,
                processData: false
            });
        });


        $(document).on('click', '.delete', function () {

            var id = $(this).data('id');
            // swal.fire({
            //     title: "Are you sure to delete?",
            //     text: "Can't you undo then?",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#DD6B55",
            //     confirmButtonText: "Ok",
            //     cancelButtonText: "Cancel",
            //     okButtonText: "Ok",
            //     closeOnConfirm: false
            // }).then((result) => {
            //     if (!result.isConfirmed) {
            //         return true;
            //     }


            var url = '{{ route("ads.destroy",":id") }}';
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: 'DELETE',
                beforeSend: function () {
                    $('.loader-ajax').show()

                },

                success: function (data) {

                    window.setTimeout(function () {
                        $('.loader-ajax').hide()
                        if (data.status == 200) {
                            $(`#tr_${id}`).remove();
                            toastr.success(data.message)
                        } else {
                            toastr.error('there is an error')
                        }

                    }, 1000);
                }, error: function (data) {

                    if (data.code === 500) {
                        toastr.error('there is an error')
                    }


                    if (data.code === 422) {
                        var errors = $.parseJSON(data.responseText);

                        $.each(errors, function (key, value) {
                            if ($.isPlainObject(value)) {
                                $.each(value, function (key, value) {
                                    toastr.error(value)
                                });

                            } else {

                            }
                        });
                    }
                }

            });
            // });
        });


    </script>


    <script>

        (function () {

            $("#product_id").select2({
                closeOnSelect: false,
                placeholder: 'Channel...',
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



    <script>
        $(document).on('change', '.activeBtn', function () {
            var ad_id = $(this).attr('data-id');
            var status = 0;
            if ($(this).is(':checked')) {
                status = 1;
            }

            $.ajax({
                type: 'GET',
                url: "{{route('changeAdStatus')}}",
                data: {
                    status: status,
                    ad_id: ad_id,

                },

                success: function (res) {
                    if (res['status'] == true) {
                        toastr.success("تمت العملية بنجاح")
                    } else {
                        // location.reload();

                    }
                },
                error: function (data) {
                    // location.reload();
                }
            });


        })
    </script>

    <script>

        (function () {

            $("#product_id_search").select2({
                closeOnSelect: false,

                placeholder: 'Channel...',
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


    <script>

        (function () {

            $("#tag_id").select2({
                closeOnSelect: false,
                placeholder: 'Channel...',
                // width: '350px',
                allowClear: true,
                ajax: {
                    url: '{{route('admin.getTags')}}',
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
    <script>
        $(document).on('change', '#status', function () {
            var checked = $(this).is(':checked');
            if (checked) {
                $('#status_data').val("1")

            } else {
                $('#status_data').val("-1")
            }

        })
    </script>

    <script>

        (function () {

            $("#platform_id").select2({
                closeOnSelect: false,
                placeholder: 'Channel...',
                // width: '350px',
                allowClear: true,
                ajax: {
                    url: '{{route('admin.getPlatforms')}}',
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