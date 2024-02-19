@extends('admin.layout.main')
@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">


        <div id="home_reports">
            <div class="row">
                @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory'))
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Qty</p>
                            <p class="report_number">{{$total_qty}}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="report_box">
                            <p class="report_title">Total Amount</p>
                            <p class="report_number">{{$total_amount}}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button id="export_inventories" class="btn btn-outline-success">Export
                            Inventory
                        </button>
                        <button class="btn btn-outline-danger " id="importBtn">Import Inventory</button>


                    </div>
                @endif
            </div>
        </div>

        <div class="kt-portlet__head-toolbar m-3">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <form action="{{route('inventories.index')}}" method="get">


                        <div class="row g-4">

                            <div class="d-flex flex-column mb-7 fv-row col-sm-4">
                                <!--begin::Label-->

                                <label for="product_id_search"
                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Product</span>
                                </label>
                                <select id='product_id' name="product_id[]" multiple style='width:100%;'>
                                    @isset($request['product_id'])
                                        @foreach($request['product_id'] as $productId)
                                            <option selected
                                                    value="{{ $productId }}">{{ \App\Product::find($productId)->title ?? '' }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>


                            <div class="d-flex flex-column mb-7 fv-row col-sm-3">
                                <!--begin::Label-->
                                <label for="tag_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Tags</span>
                                </label>
                                <select id='tag_id' name="tag_id[]" multiple style='width: 100%;'>
                                    @isset($request['tag_id'])
                                        @foreach($request['tag_id'] as $tag)
                                            <option selected
                                                    value="{{$tag}}">{{\App\TagGroup::find($tag)->title??''}}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>


                            <div class="d-flex flex-column mb-7 fv-row col-sm-2">
                                <!--begin::Label-->

                                <label for="open"
                                       class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Open</span>
                                </label>
                                <select id='open' name="open" class="form-control">
                                    <option @if(isset($request['open']))  @if($request['open']=='all')  selected
                                            @endif    @endif value="all">All
                                    </option>
                                    <option @if(isset($request['open']))  @if($request['open']=='open')  selected
                                            @endif    @endif value="open">Open
                                    </option>
                                    <option @if(isset($request['open']))  @if($request['open']=='not_open')  selected
                                            @endif    @endif value="not_open">Not Open
                                    </option>

                                </select>
                            </div>


                            <input type="hidden" name="search" value="search">

                            <div class="d-flex flex-column mb-7 fv-row col-sm-2 m-4">
                                <button class="btn btn-success">Search</button>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div id="table-wrapper">
            <div id="table-scroll">
                <table id="kt_table_1" class="table table-striped- table-bordered table-hover table-checkable">
                    <thead>
                    <tr>

                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>fulfillment</th>
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory'))
                            <th>Open</th>
                        @endif
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory'))
                            <th>New Qty</th>
                        @endif
                        <th>Avg. Price</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $key=> $row)
                        <tr id="tr_{{$row->id}}">
                            <td>{{$key+1}}</td>
                            <td>{{$row->product_info->title??''}}
                                {{$row->color_info->title??''}}
                                {{$row->size_info->title??''}}

                            </td>
                            <td id="qty_{{$row->id}}">{{$row->bought-$row->sold}}</td>
                            <td>{{product_fullfilment_units($row->product, $row->color, $row->size)}}</td>
                            @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory'))
                                <td>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input activeBtn" @if($row->open==1)  checked
                                               @endif data-id="{{$row->id}}" type="checkbox" role="switch">
                                    </div>

                                </td>
                            @endif
                            @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory'))
                                <td>
                                    <input class="form-control changeQty" type="number" data-id="{{$row->id}}">
                                </td>
                            @endif

                            <td>{{$row->last_cost??0}}</td>

                            <td>
                                <div class="dropdown">

                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        Action
                                    </button>
                                    @if(permission_checker(Auth::guard('admin')->user()->id, 'ruined_item'))
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="ruinedItem" data-id="{{$row->id}}">Ruined Items</a>
                                        </div>
                                    @endif
                                </div>

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @isset($request['search'])

                    {!! $rows->appends($queryParameters)->links() !!}
                @endif
            </div>
        </div>


    </div>

    <div class="modal" id="ruinedModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="operationType">Ruined Item</h5>
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
    <script>
        import Options from "../../../../../public/assets/vendors/general/bootstrap-switch/docs/options.html";

        export default {
            components: {Options}
        }
    </script>
    <link href="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css
" rel="stylesheet">
    <script>
        var loader = `<div class="linear-background">
		<div class="inter-crop"></div>
		<div class="inter-right--top"></div>
		<div class="inter-right--bottom"></div>
	</div>`;
        $(document).on('click', '.ruinedItem', function (e) {
            e.preventDefault();
            var inventory_id = $(this).attr('data-id');
            $('#modal-body').html(loader)
            $('#operationType').text('ruined Item');

            $('#ruinedModal').modal('show')

            var routing = "{{route('admin.ruinedItemFromInventory',':id')}}";
            routing = routing.replace(':id', inventory_id);

            setTimeout(function () {
                $('#modal-body').load(routing)
            }, 500)

        })

        $(document).on('submit', 'Form#addForm', function (e) {
            var inventory_row_id = $('#inventory_row_id').val();
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
                        $('#table_body_data').prepend(`<tr id="tr_${data.id}">${data.row}</tr>`);
                        $('#ruinedModal').modal('hide')
                        $(`#tr_${inventory_row_id}`).html(data.row)


                    } else
                        toastr.error('عذرا هناك خطأ فني ??');
                    $('#addButton').html(`اضافة`).attr('disabled', false);
                },
                error: function (data) {
                    if (data.status === 500) {
                        toastr.error('عذرا هناك خطأ فني ??');
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
                        toastr.error('عذرا هناك خطأ فني ??');
                    $('#addButton').html(`اضافة`).attr('disabled', false);
                },//end error method

                cache: false,
                contentType: false,
                processData: false
            });
        });

    </script>

    {{--    <script>--}}


    {{--        setTimeout(function () {--}}
    {{--            $.ajax({--}}
    {{--                type: 'GET',--}}
    {{--                url: "{{route('admin.inventoryGetTotalQty')}}",--}}
    {{--                data: {},--}}

    {{--                success: function (res) {--}}
    {{--                    if (res['status'] == true) {--}}
    {{--                        $('#total_qty').text(res.total_qty)--}}
    {{--                    } else {--}}

    {{--                    }--}}
    {{--                },--}}
    {{--                error: function (data) {--}}
    {{--                    // location.reload();--}}
    {{--                }--}}
    {{--            });--}}

    {{--        }, 0)--}}

    {{--        setTimeout(function () {--}}
    {{--            $.ajax({--}}
    {{--                type: 'GET',--}}
    {{--                url: "{{route('admin.inventoryGetTotalAmount')}}",--}}
    {{--                data: {},--}}

    {{--                success: function (res) {--}}
    {{--                    if (res['status'] == true) {--}}
    {{--                        $('#total_amount').text(res.total_amount)--}}
    {{--                    } else {--}}

    {{--                    }--}}
    {{--                },--}}
    {{--                error: function (data) {--}}
    {{--                    // location.reload();--}}
    {{--                }--}}
    {{--            });--}}

    {{--        }, 0)--}}

    {{--    </script>--}}

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
        $(document).on('click', '#importBtn', function () {

            $('#modal-body').html(loader)
            $('#operationType').text('Import');
            $('#ruinedModal').modal('show')


            setTimeout(function () {
                $('#modal-body').load("{{route("admin.import_inventory")}}")
            }, 500)

        });

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
        $(document).on('click', '#export_inventories', function () {
            var product_id = $('#product_id').val();
            var tag_id = $('#tag_id').val();

            var import_link = "{{route('admin.export_inventories')}}?product_id=" + product_id + "&&tag_id=" + tag_id;
            window.location.href = import_link;
        });

    </script>

    <script>
        $(document).on('change', '.custom-select', function () {
            var custom_select = $(this).val();

            var currentURL = window.location.href;

            // Check if the URL already contains parameters
            if (currentURL.includes('?')) {
                // Use a regular expression to replace the existing 'custom_select' parameter
                currentURL = currentURL.replace(/([\?&])custom_select=[^&]*/, '$1' + 'custom_select=' + custom_select);
            } else {
                // If 'custom_select' parameter doesn't exist, add it
                if (currentURL.includes('?')) {
                    currentURL += '&';
                } else {
                    currentURL += '?';
                }
                currentURL += 'custom_select=' + custom_select;
            }

            window.location.href = currentURL;
        });
    </script>



    <script>
        $(document).on('change', '.activeBtn', function () {
            var inventory_id = $(this).attr('data-id');
            var open = 0;
            if ($(this).is(':checked')) {
                open = 1;
            }
            $.ajax({
                type: 'GET',
                url: "{{route('changeInventoryOpen')}}",
                data: {
                    inventory_id: inventory_id,
                    open: open,

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
        $(document).on('change keyup', '.changeQty', function () {
            var qty = $(this).val();
            var inventory_id = $(this).attr('data-id');


            $.ajax({
                type: 'GET',
                url: "{{route('changeInventoryQty')}}",
                data: {
                    inventory_id: inventory_id,
                    qty: qty,

                },

                success: function (res) {
                    if (res['status'] == true) {
                        $(`#qty_${inventory_id}`).text(qty);
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






@endsection