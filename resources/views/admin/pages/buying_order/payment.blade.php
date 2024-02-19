@extends('admin.layout.main')
@section('content')
    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-plus-square"></i></span>
                    <h3 class="kt-portlet__head-title">Create Payment </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <form class="kt-form" method="post" action="{{url('buying_order')}}" enctype="multipart/form-data" id="ajsuform">
                    {{csrf_field()}}
                    <div id="ajsuform_yu"></div>
                    <div class="form-group" id="client_finder">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Find Agent</label>
                                <select class="form-control agents_selector" name="client" id="client">
                                    <option value="" disabled selected>Choose Agent</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr />
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Order NUmber</label>
                                <input class="form-control order_num" type="text" readonly placeholder="order number" name="masd" value="{{$order_number}}"  />
                            </div>
                            {{-- <div class="col-md-6">
                                <label>Order Number</label>
                                <input class="form-control" type="text" placeholder="Order Number" name="order_number" value="{{$order_number}}"  />
                            </div> --}}
                            <div class="col-md-3">
                                <label>Invoice Date</label>
                                <input class="form-control" type="date" placeholder="Invoice Date" name="shipping_date" value="{{date('Y-m-d')}}"  />
                            </div>
                            <div class="col-md-6">
                                <label>Order Invoice</label>
                                <input class="form-control" type="file" name="order_invoice"  />
                            </div>

                            <div class="col-md-6">
                                <label>Payment Status</label>

                                <select class="form-control" id='payment_status' name="payment_status"  >
                                    <option value="partly_paid">Partly Paid </option>


                                </select>
                            </div>

                            <div class="col-md-6" id="payment_amount_container">

                                <label>Payment Amount </label>
                                <input required class="form-control" type="number" min="0" accept="any" placeholder="" name="payment_amount" id="payment_amount" value="0"  />


                            </div>

                            <input type="hidden" name="type" value="payment">


                        </div>
                    </div>

                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions text-right">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>

@endsection