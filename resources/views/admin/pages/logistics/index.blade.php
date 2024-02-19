@extends('admin.layout.main')
@section('styles')
    <link rel="stylesheet" href="{{asset('tagsinput/tagsinput.css')}}">

    <style>
        table thead {
            background: #001587;
            color: white;
        }

        .dataTables_wrapper .dataTable th {
            border: solid 1px white;
            color: white;
            font-size: 12px;
            width: auto !important;
        }

        table th hr {
            border-color: white;
        }

        .dataTables_wrapper .dataTable tbody tr.odd {
            background: #a3d1ff;
        }

        table.table-bordered.dataTable th:last-child:before, table.table-bordered.dataTable th:last-child:before,
        table.table-bordered.dataTable th:last-child:after, table.table-bordered.dataTable th:last-child:after {
            content: "";
        }
    </style>
@endsection
@section('content')
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                    <h3 class="kt-portlet__head-title">Logistics </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions d-flex mx-5">

                            <div class="dropdown mr-3">
                                <button class="btn btn-warning d-sm-inline-block" type="button" id="dropdownMenuButtonA"
                                        data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"><i class="fas fa-cogs"></i> Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonA">
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalSTATUS"
                                       id="selling_order_changing_status"><i class="fas fa-check-square"></i> Orders
                                        Status</a>

                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#myModalNOTE"><i
                                                class="fas fa-comment"></i> Orders Notes</a>
                                    <a class=" importShipping dropdown-item"><i class="fa fa-file-import"></i> Import Shipping</a>

                                </div>
                            </div>

                            <div class="dropdown mr-5">
                                <button class="btn btn-dark  d-none d-sm-inline-block" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"><i class="fas fa-print"></i> Print
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button type="button" class="dropdown-item get_selected_orders_shiping_info"
                                            task="Shipping_Info" url="{{url('selling_order/orders_operation')}}"><i
                                                class="fas fa-list"></i> Delivery List
                                    </button>
                                    <button type="button"
                                            class="dropdown-item get_selected_orders_shiping_info mb-1  d-none d-sm-inline-block"
                                            task="Print_Invoice" url="{{url('selling_order/orders_operation')}}"><i
                                                class="fas fa-file-invoice"></i> Invoices
                                    </button>
                                    <button class="dropdown-item getCopyOfOrderNumber">
                                        <i class="fa fa-copy"></i>Copy
                                    </button>
                                </div>
                            </div>
                            {{--                            <div class="modal fade" id="myModalTotalAmount" tabindex="-1" role="dialog"--}}
                            {{--                                 aria-labelledby="myModalLabel" aria-hidden="true">--}}
                            {{--                                <div class="modal-dialog">--}}
                            {{--                                    <div class="modal-content">--}}
                            {{--                                        <div class="modal-header">--}}
                            {{--                                            <h5 class="modal-title">Selected Orders</h5>--}}
                            {{--                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                            {{--                                                <span aria-hidden="true">&times;</span>--}}
                            {{--                                            </button>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="modal-body">--}}
                            {{--                                            <div id="calcualte_selected_orders_amount"></div>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            <div class="modal fade" id="myModalNOTE" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Orders Notes</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="change_selected_orders_notes_res"></div>
                                            <form role="form" action="{{ url('selling_order_notes_multi') }}" class=""
                                                  method="POST" id="ajsuformreload_mnotes">
                                                {{ csrf_field() }}
                                                <div id="ajsuform_yu_mnotes"></div>
                                                <input type="hidden" id="selected_note_orders" name="order"/>
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <textarea name="note" class="form-control"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Moderator </label>
                                                    <select name="mpderator[]" multiple
                                                            class="d-block form-control orders_selector_mul_reps">
                                                        @foreach ($moderators  as $moderator)
                                                            <option value="{{$moderator->id}}">{{$moderator->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tags </label>
                                                    <select name="tag[]" multiple
                                                            class="d-block form-control orders_selector_mul_tags">
                                                        @foreach ($tags as $sa)
                                                            <option value="{{$sa->id}}">{{$sa->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <button type="button" class="btn btn-info"
                                                        id='create_mutiple_orders_notes'>Save
                                                </button>
                                                <button type="button" class="btn btn-success" data-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="myModalSTATUS" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Orders Status</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="change_selected_orders_status_res"></div>
                                            <div class="form-group">
                                                <label>Status </label>
                                                <select class="form-control" name="orders_status"
                                                        id="all_orders_status_seelctor">
                                                    <option value="" disabled selected>Choose Status</option>
                                                    @foreach ($statuss as $status)
                                                        @if($status->id != 1 && $status->id != 11)
                                                            <option value="{{$status->id}}">{{$status->title}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                            <button type="button" class="btn btn-info"
                                                    id='change_selected_orders_status' task="Change_Status"
                                                    url="{{url('selling_order/orders_task')}}">Save
                                            </button>
                                            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <form action="{{route('admin.logistics_search')}}" method="get" class="kt-form"
                      id="selling_order_search_form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3 d-none d-sm-inline-block">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{date('Y-m-d')}}"/>
                                </div>
                            </div>
                            <div class="col-md-3 d-none d-sm-inline-block">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{date('Y-m-d')}}"/>
                                </div>
                            </div>

                            <div class="col-md-6 d-none d-sm-inline-block">
                                <div class="form-group" id="order_number_gr">
                                    <label>Order Number / Shipping Number</label>
                                    <input type="text" class="form-control" name="order_number" id="order_number"
                                           data-role="tagsinput"/>
                                </div>
                            </div>

                            <div class="col-md-3 d-none d-sm-inline-block">
                                <div class="form-group">
                                    <label>Old Status</label>
                                    <select class="form-control js-example-basic-multiple" name="from_status[]" id="from_status" multiple>
                                        @foreach($statuss as $status)
                                            <option value="{{$status->id}}">{{$status->title}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3 d-none d-sm-inline-block">
                                <div class="form-group">
                                    <label>Last Status</label>
                                    <select class="form-control js-example-basic-multiple" name="last_status[]" id="last_status" multiple>
                                        @foreach($statuss as $status)
                                            <option value="{{$status->id}}">{{$status->title}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-none d-sm-inline-block">

                                <div class="form-group">
                                    <label>Moderator </label>
                                    <select id="moderator_id" name="moderator_id[]" multiple
                                            class="d-block form-control orders_selector_mul_reps">
                                        @foreach ($moderators  as $moderator)
                                            <option value="{{$moderator->id}}">{{$moderator->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-3 d-none d-sm-inline-block">

                                <div class="form-group">
                                    <label>Repp </label>
                                    <select id="repp_id" name="repp_id[]" multiple
                                            class="d-block form-control orders_selector_mul_reps">
                                        @foreach ($repps  as $repp)
                                            <option value="{{$repp->id}}">{{$repp->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-1">
                                <label class="control-label"><br/></label>
                                <button type="submit" class="btn btn-success btn-block"><i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive" id="selling_order_results"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="timeLineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order <span id="operationType"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body time_line_list" id="modal-body">



                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('tagsinput/tagsinput.js')}}"></script>

    <script>
        $(document).ready(function () {

            $('body').on('keyup', function (event) {
                if (event.ctrlKey && event.key === 'i') {
                    $('#order_number_gr .bootstrap-tagsinput input').focus();
                }
            });
            $('body').on('submit', '#selling_order_search_form', function (e) {
                e.preventDefault();
                $('#selling_order_results').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');
                var action = $(this).attr('action');
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var from_status = $('#from_status').val();
                var last_status = $('#last_status').val();
                var order_number = $('#order_number').val();
                var moderator_id = $('#moderator_id').val();
                var repp_id = $('#repp_id').val();

                $.ajax({
                    type: 'GET',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        from_status: from_status,
                        last_status: last_status,
                        order_number: order_number,
                        moderator_id: moderator_id,
                        repp_id: repp_id
                    },
                    url: action,
                    success: function (data) {
                        $('#selling_order_results').html(data);
                        $('.orders_selector_mul_reps').select2({placeholder: "Select Rep"});
                        $('.orders_selector_mul_tags').select2({placeholder: "Select Tags"});
                        $('#kt_table_2').DataTable({
                            "iDisplayLength": 100,
                            columnDefs: [{
                                orderable: false,
                                className: 'checkbox',
                                targets: 0
                            },
                                {
                                    orderable: true,
                                    className: '',
                                    targets: 1,
                                    type: "numeric"
                                }],
                            select: {
                                style: 'os',
                                selector: 'td:first-child'
                            },
                            order: [[1, 'asc']]
                        });
                    }
                });
                return false;
            });
        });
    </script>

    <script>

        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
    </script>

    <script>
        $(document).on('click','.importShipping',function (){
            $('#operationType').text('Import')
            $('#modal-body').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>')
            $('#timeLineModal').modal('show');
            var import_link="{{route('admin.import_sell_orders')}}";

            setTimeout(function () {
                $('#modal-body').load(import_link)
            }, 500)

        })
    </script>
    <script>
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
                        $('#table_body_data').prepend(`<tr id="tr_${data.id}">${data.row}</tr>`);

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

    </script>
    <script>
        $(document).on('click','.getCopyOfOrderNumber',function (){
            var items = [];
            var table = $('#kt_table_2').DataTable();
            table.$(".check_single:checked").each(function(index, value) {
                items.push($(value).val());
            });
            if (items.length > 0) {
                              var order_number_route="{{route('admin.copyOrderNumber')}}";
                $.ajax({
                    url: order_number_route,
                    method: 'POST',
                    data: { items: items },
                    success: function(response) {
                        // The response will contain the order numbers
                        var orderNumbers = response.order_numbers;

                        // Join the order numbers into a string separated by line breaks
                        var orderNumbersString = orderNumbers.join('\n');

                        // Create a temporary textarea element to hold the text
                        var textarea = document.createElement('textarea');
                        textarea.value = orderNumbersString;

                        // Append the textarea to the document
                        document.body.appendChild(textarea);

                        // Select the text in the textarea
                        textarea.select();

                        // Copy the selected text to the clipboard
                        document.execCommand('copy');

                        // Remove the temporary textarea
                        document.body.removeChild(textarea);

                        // Log a message or perform any other action
                        console.log('Order numbers copied to clipboard!');
                    },
                    error: function(error) {
                        console.error('Error fetching order numbers:', error);
                    }
                });
            }
            else {
                toastr.error('يرجي اختيار احد الاوردرات  😞');
            }

        });

    </script>
@endsection