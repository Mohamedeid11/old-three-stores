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

    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                    <h3 class="kt-portlet__head-title">Ads Report</h3>
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
                                             value="{{$fromDate}}"
                                            />
                                </div>
                            </div>
                            <div class="col-md-3 col-5">
                                <div class="form-group">
                                    <label>To Date </label>
                                    <input type="date" class="form-control" name="toDate" id="toDate"
                                           value="{{$toDate}}"
                                            />

                                </div>
                            </div>


{{--                            <div class="col-md-4 col-4">--}}
{{--                                <!--begin::Label-->--}}
{{--                                <label for="platform_id"--}}
{{--                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">--}}
{{--                                    <span class="required mr-1">   Platforms</span>--}}
{{--                                </label>--}}
{{--                                <select id='platform_id' name="platform_id" style='width: 100%;'>--}}
{{--                                    <option selected disabled>- Search for Platform</option>--}}
{{--                                    @isset($request['platform_id'])--}}
{{--                                        <option selected--}}
{{--                                                value="{{$request['platform_id']}}">{{\App\OrderTag::find($request['platform_id'])->title??''}}</option>--}}

{{--                                    @endisset--}}
{{--                                </select>--}}
{{--                            </div>--}}


{{--                            <div class="d-flex flex-column mb-7 fv-row col-sm-6">--}}
{{--                                <!--begin::Label-->--}}

{{--                                <label for="product_id_search"--}}
{{--                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">--}}
{{--                                    <span class="required mr-1">   Product</span>--}}
{{--                                </label>--}}
{{--                                <select id='product_id_search' name="product_id" style='width: 100%;'>--}}
{{--                                    <option selected disabled>- Search for Product</option>--}}
{{--                                    @isset($request['product_id'])--}}
{{--                                        <option selected--}}
{{--                                                value="{{$request['product_id']}}">{{\App\Product::find($request['product_id'])->title??''}}</option>--}}
{{--                                    @endisset--}}
{{--                                </select>--}}
{{--                            </div>--}}


{{--                            <div class="d-flex flex-column mb-7 fv-row col-sm-4">--}}
{{--                                <!--begin::Label-->--}}
{{--                                <label for="tag_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">--}}
{{--                                    <span class="required mr-1">   Tags</span>--}}
{{--                                </label>--}}
{{--                                <select id='tag_id' name="tag_id" style='width: 100%;'>--}}
{{--                                    <option selected disabled>- Search for Tag</option>--}}
{{--                                    @isset($request['tag_id'])--}}
{{--                                        <option selected--}}
{{--                                                value="{{$request['tag_id']}}">{{\App\TagGroup::find($request['tag_id'])->title??''}}</option>--}}

{{--                                    @endisset--}}
{{--                                </select>--}}
{{--                            </div>--}}


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
                                <th>Day</th>
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
                            </tr>
                            </thead>
                            <tbody id="table_body_data">
                            @foreach($rows as $row)
                                <tr>
                                    <td>{{$row['date']}}</td>
                                    <td>{{$row['result']}}</td>
                                    <td>{{$row['cost_per_result']}}</td>
                                    <td>{{$row['total1']}}</td>
                                    <td>{{$row['order']}}</td>
                                    <td>{{$row['order_value']}}</td>
                                    <td>{{$row['total2']}}</td>
                                    <td>
                                        @if($row['total1']==0)
                                            {{$row['total2']-$row['product_buy']}}

                                        @else
                                            {{($row['total2']-$row['product_buy'])/$row['total1']}}

                                        @endif
                                    </td>
                                    <td>
                                        {{$revenue=$row['total2']-$row['product_buy']-$row['total1']}}

                                    </td>
                                    <td>
                                        @if($row['order']>0)
                                            {{$cpo=$row['total1']/$row['order']}}
                                        @else
                                            {{$cpo=$row['total1']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['order']>0)
                                            {{$result_per_order=$row['result']/$row['order']}}
                                        @else
                                            {{$result_per_order=$row['result']}}
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


@section('scripts')




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