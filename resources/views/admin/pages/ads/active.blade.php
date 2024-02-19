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

    <style>
        .green-square {
            width: 200px;
            height: 40px;
            background-color: green;
            color: white;
            text-align: center;
            line-height: 40px;
            padding: 2px 5px; /* 10px top and bottom, 15px right and left */
        }
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
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                    <h3 class="kt-portlet__head-title">Active Ads</h3>
                </div>



            </div>


            <div class="kt-portlet__body">
{{--                <input id="searchInput" class="form-control" type="text">--}}

                <div id="table-wrapper">
                    <div id="table-scroll">
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                            <tr>
{{--                                <th>ID</th>--}}
                                <th>Platform</th>
                                <th>Active</th>
                                <th>Ad Number</th>
{{--                                <th>Tags</th>--}}
                                <th>Products</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_data">

                            @forelse ($ads ??[] as $key=>$ad)
                                <tr >
{{--                                    <td>{{$ad->id??''}}</td>--}}
                                    <td>
                                        @forelse($ad->platforms ??[] as $platform )
                                            {{$platform->title??''}} <br>
                                        @empty

                                        @endforelse
                                    </td>
                                    <td>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input activeBtn" @if($ad->status==1)  checked
                                                   @endif data-id="{{$ad->id}}" type="checkbox" role="switch">
                                        </div>

                                    </td>

                                    <td>{{$ad->ad_number	}}
                                        @foreach(\App\Ad::where('parent_id',$ad->id)->where('date',$ad->date)->pluck('ad_number')->toArray() as $ad_number)
                                            <hr>
                                            {{$ad_number}}
                                        @endforeach
                                    </td>
{{--                                    <td>--}}

{{--                                        @forelse($ad->products ??[] as $product)--}}
{{--                                            <span style="display:none">--}}
{{--                                                @php--}}
{{--                                                    $tags_ids= \App\productTag::where('product_id', $product->id)->pluck('tag_id')->toArray();--}}
{{--                                                    $tags=\App\TagGroup::whereIn('id',$tags_ids)->get();--}}
{{--                                                @endphp--}}
{{--                                            </span>--}}

{{--                                            @forelse($tags ??[] as $tag)--}}
{{--                                                {{$tag->title??''}}--}}
{{--                                                <br>--}}
{{--                                            @empty--}}
{{--                                            @endforelse--}}
{{--                                        @empty--}}

{{--                                        @endforelse--}}
{{--                                    </td>--}}
                                    <td>

                                        @forelse($ad->products ??[] as $product)
                                            {{$product->title??''}}
                                            <br>
                                            <span style="display:none">
                                            {{$colors=\App\ProductColor::where('product',$product->id)->get()}}
                                                {{$sizes=\App\ProductSize::where('product',$product->id)->get()}}
                                                {{$inventory=null}}
                                                {{$qty=0}}

                                        </span>
                                            @if($colors->count() > 0 || $sizes->count() > 0)
                                                @if($colors->count() > 0)
                                                    @foreach ($colors as $color)
                                                        @if($sizes->count() == 0)
                                                            <span style="display:none">
                                                                {{$qty=qty_sold_inventory($product->id, $color->color_info->id, 0)}}
                                                                {{$inventory=\App\Inventory::where('product',$product->id)->where('color',$color->color_info->id)->where('size',0)->first()}}

                                                            </span>
                                                            @if($inventory)
                                                                {{$color->color_info->title??''}}
                                                                @if($inventory->open==1)
                                                                    <span class="green-square h5">Open</span>
                                                                @else
                                                                    <span @if($qty<2) class="red-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"@elseif($qty>=2 && $qty<=10 ) class="yellow-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"   @else  class="green-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"  @endif>{{$qty}}</span>
                                                                @endif
                                                            @endif


                                                        @else
                                                            @foreach ($sizes as $size)


                                                                <span style="display:none">
                                                                {{$qty=qty_sold_inventory($product->id, $color->color_info->id, $size->size_info->id)}}
                                                                    {{$inventory=\App\Inventory::where('product',$product->id)->where('color',$color->color_info->id)->where('size',$size->size_info->id)->first()}}

                                                                </span>
                                                                @if($inventory)
                                                                    {{$color->color_info->title??''}}{{$size->size_info->title??''}}
                                                                    @if($inventory->open==1)
                                                                        <span class="green-square h5">Open</span>
                                                                    @else
                                                                        <span @if($qty<2) class="red-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"@elseif($qty>=2 && $qty<=10 ) class="yellow-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"   @else  class="green-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"  @endif>{{$qty}}</span>
                                                                    @endif
                                                                @endif

                                                            @endforeach

                                                        @endif

                                                    @endforeach

                                                @elseif($colors->count() == 0 && $sizes->count() > 0)

                                                    @foreach ($sizes as $size)
                                                        {{ $qty=qty_sold_inventory($product, 0, $size->size_info->id)}}




                                                        <span style="display:none">
                                                            {{ $qty=qty_sold_inventory($product, 0, $size->size_info->id)}}
                                                            {{$inventory=\App\Inventory::where('product',$product->id)->where('color',0)->where('size',$size->size_info->id)->first()}}

                                                                </span>
                                                        @if($inventory)
                                                            {{$size->size_info->title??''}}
                                                            @if($inventory->open==1)
                                                                <span class="green-square h5">Open</span>
                                                            @else
                                                                <span @if($qty<2) class="red-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}" @elseif($qty>=2 && $qty<=10 ) class="yellow-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"   @else  class="green-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"  @endif >{{$qty}}</span>
                                                            @endif
                                                        @endif



                                                    @endforeach

                                                @else


                                                @endif



                                            @else

                                                <span style="display:none">
                                                    {{$qty= qty_sold_inventory($product, 0, 0)}}
                                                    {{$inventory=\App\Inventory::where('product',$product->id)->where('color',0)->where('size',0)->first()}}

                                                 </span>
                                                Main
                                                @if($inventory)
                                                    @if($inventory->open==1)
                                                        <span class="green-square h5">Open</span>
                                                    @else
                                                        <span @if($qty<2) class="red-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"@elseif($qty>=2 && $qty<=10 ) class="yellow-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"   @else  class="green-square h5 inventory_{{$inventory->product}}_{{$inventory->color}}_{{$inventory->size}}"  @endif>{{$qty}}</span>
                                                    @endif
                                                @endif
                                            @endif

                                            <br>
                                            </hr>

                                        @empty

                                        @endforelse

                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>


                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


@section('scripts')





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
        // بعد تحميل الصفحة
        $(document).ready(function () {
            // بمجرد كتابة شيء في حقل البحث
            $('#searchInput').on('input', function () {
                var searchTerm = $(this).val().toLowerCase();

                // قم بالتصفية حسب القيمة المدخلة في حقل البحث
                $('#kt_table_1 tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            });
        });
    </script>


    <script>
        $('input').on('keyup', function(event) {
            var searchTerm = $(this).val().toLowerCase();
            // قم بالتصفية حسب القيمة المدخلة في حقل البحث
            $('#kt_table_1 tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initialize DataTable

            // Access the DataTables search input field
            var searchInput = $('input[type="search"]').on('keyup', function() {
                // Handle keyup event

                $('#kt_table_1 tbody tr').filter(function () {
                    $(this).
                    toggle($(this).text().toLowerCase().indexOf(searchInput.val().toLowerCase()) > -1);
                });              });

            // $('#kt_table_1').on('search.dt', function() {
            //     // Handle keyup event
            //
            //     $('#kt_table_1 tbody tr').filter(function () {
            //         $(this).
            //         toggle($(this).text().toLowerCase().indexOf(searchInput.val().toLowerCase()) > -1);
            //     });
            // });


        });
    </script>

    @include('admin.layout.pusher')

@endsection