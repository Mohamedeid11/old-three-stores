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
                    <h3 class="kt-portlet__head-title">File Gard</h3>
                </div>



            </div>


            <div class="kt-portlet__body">

                <form action="{{route('file_gard.index')}}" method="get" class="kt-form">
                    <div class="form-group">
                        <div class="row">



                            <div class="col-md-2 col-2">
                                <div class="form-group">
                                    <label>From Date </label>
                                    <input type="date" class="form-control" name="fromDate" id="fromDate"
                                           @if(isset($request['fromDate']))  value="{{$request['fromDate']}}"
                                           @else value="{{date("Y-m-d", strtotime("yesterday"))}}" @endif />
                                </div>
                            </div>
                            <div class="col-md-2 col-2">
                                <div class="form-group">
                                    <label>To Date </label>
                                    <input type="date" class="form-control" name="toDate" id="toDate"
                                           @if(isset($request['toDate']))  value="{{$request['toDate']}}"
                                           @else value="{{date("Y-m-d")}}" @endif />

                                </div>
                            </div>

                            <div class="d-flex flex-column mb-7 fv-row col-sm-6">
                                <!--begin::Label-->
                                <label for="product_id"  class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                                    <span class="required mr-1">   Products</span>
                                </label>
                                <select id='product_id'  name="product_id[]"  multiple style='width: 100%;'>
                                    @isset($request['product_id'])
                                        @foreach($request['product_id'] as $productId)
                                            <option selected
                                                    value="{{ $productId }}">{{ \App\Product::find($productId)->title ?? '' }}</option>
                                        @endforeach
                                    @endisset                                </select>
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
{{--                                <th>ID</th>--}}
                                <th>Type</th>
                                <th>Admin</th>
                                <th>Date</th>
                                <th>Lost Qty</th>
                                <th>Lost Amount </th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_data">
                            @forelse($rows as $row)
                                <tr id="tr_{{$row->id}}">
{{--                                    <td>{{$row->id}}</td>--}}
                                     <td>{{$row->type}}</td>
                                    <td>{{$row->admin->name??''}}</td>
                                    <td>{{$row->created_at??''}}</td>
                                    <td>{{$row->detailsFiles->sum('qty')??0}}</td>
                                    <td>{{$row->detailsFiles->sum('price')??0}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link" type="button" id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="{{route('file_gard.show',$row->id)}}" class="dropdown-item fileDetails" data-id="{{$row->id}}">Details</a>
                                                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_file_gard'))
                                                <button  class="dropdown-item delete" data-id="{{$row->id}}">Delete</button>
                                                @endif

                                            </div>
                                        </div>

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


<script>
    $(document).on('click', '.delete', function () {

        var id = $(this).data('id');
        swal.fire({
            title: "Are you sure to delete?",
            text: "Can't you undo then?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ok",
            cancelButtonText: "Cancel",
            okButtonText: "Ok",
            closeOnConfirm: false
        }).then((result) => {
            if (!result.isConfirmed) {
                return true;
            }


        var url = '{{ route("file_gard.destroy",":id") }}';
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
        });
    });

</script>


<script>

    (function () {

        $("#product_id").select2({
            closeOnSelect: false,
            placeholder: 'Search...',
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


@endsection