@extends('admin.layout.main')

@section('styles')
<link rel="stylesheet" href="{{asset('tagsinput/amsify.suggestags.css')}}">
<style>
    .amsify-suggestags-area {width: 90%;}
</style>
@endsection

@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">

    <div class="kt-portlet__body">

        @if(Auth::guard('admin')->user()->position == 1)

            <div id="home_reports" class="d-none">

                <div class="row">



                </div>

            </div>

            <div id="orders_stats" class="container-fluid">

                <div class="row">

                    <div class="col-md col-sm-4 col-6 mb-5">

                        <p class="stat_title">Today</p>

                        <p class="stat_amount">{{order_stats_data(0, 'all')}}</p>

                        <p class="stat_amount_wl">Win : {{order_stats_data(0, 'win')}}</p>

                        <p class="stat_amount_wl">Loss : {{order_stats_data(0, 'loss')}}</p>

                        <p class="stat_amount_wl">Open : {{order_stats_data(0, 'open')}}</p>

                    </div>

                    <div class="col-md col-sm-4 col-6 mb-5">

                        <p class="stat_title">Yesterday</p>

                        <p class="stat_amount">{{order_stats_data(1, 'all')}}</p>

                        <p class="stat_amount_wl">Win : {{order_stats_data(1, 'win')}}</p>

                        <p class="stat_amount_wl">Loss : {{order_stats_data(1, 'loss')}}</p>

                        <p class="stat_amount_wl">Open : {{order_stats_data(1, 'open')}}</p>

                    </div>

                    <div class="col-md col-sm-4 col-6 mb-5">

                        <p class="stat_title">Last 7 Days</p>

                        <p class="stat_amount">{{order_stats_data(7, 'all')}}</p>

                        <p class="stat_amount_wl">Win : {{order_stats_data(7, 'win')}}</p>

                        <p class="stat_amount_wl">Loss : {{order_stats_data(7, 'loss')}}</p>

                        <p class="stat_amount_wl">Open : {{order_stats_data(7, 'open')}}</p>

                    </div>

                    <div class="col-md col-sm-4 col-6 mb-5">

                        <p class="stat_title">Last 30 Days</p>

                        <p class="stat_amount">{{order_stats_data(30, 'all')}}</p>

                        <p class="stat_amount_wl">Win : {{order_stats_data(30, 'win')}}</p>

                        <p class="stat_amount_wl">Loss : {{order_stats_data(30, 'loss')}}</p>

                        <p class="stat_amount_wl">Open : {{order_stats_data(30, 'open')}}</p>

                    </div>

                    <div class="col-md col-sm-4 col-6 mb-5">

                        <p class="stat_title">Last 90 Days</p>

                        <p class="stat_amount">{{order_stats_data(90, 'all')}}</p>

                        <p class="stat_amount_wl">Win : {{order_stats_data(90, 'win')}}</p>

                        <p class="stat_amount_wl">Loss : {{order_stats_data(90, 'loss')}}</p>

                        <p class="stat_amount_wl">Open : {{order_stats_data(90, 'open')}}</p>

                    </div>

                </div>

            </div>

            <hr />

            <div id="stats_tables" class="container-fluid">

                <div class="row">

                    <div class="col-sm-6 col-12 mb-5">

                        <h2>Search Order's</h2>

                        <div class="form-group">

                            <div class="input-group">

                                <input type="text" class="form-control" placeholder="Enter Order Number"

                                id="order_number_filter" data-url="{{url('order_dashboard_search')}}">

                                <div class="input-group-append">

                                    <button type="button" class="btn btn-brand" id="dashboard_search_btn"><i class="fas fa-search text-white"></i></button>

                                </div>

                            </div>

                        </div>

                        <div id="order_dashboard_details" class="dashboard_results"></div>

                    </div>

                    <div class="col-sm-6 col-12 mb-5">

                        <h2>REP's</h2>

                        <div id="reps_dashboard_tasks" class="notloaded dashboard_results" data-url="{{url('get_reps_data')}}"></div>                    

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-6 col-12 mb-5">

                        <h2>

                            Purchase List

                            <a href="{{url('print_purchase_list')}}" target="_blank" class="pull-right mr-5"><small><i class="fas fa-print"></i></small></a>

                        </h2>

                        <div id="purchases_dashboard_tasks" class="notloaded dashboard_results" data-url="{{url('purchases_dashboard_tasks')}}"></div>                    

                    </div>

                    <div class="col-sm-6 col-12 mb-5">

                        <h2>Client Info </h2>

                        <div class="form-group">

                            <div class="input-group">


                                <input type="text" class="form-control" placeholder="Enter Client Number"

                                       id="client_info_filter" data-url="">

                                <div class="input-group-append">

                                    <button type="button" class="btn btn-brand" id="client_info_btn"><i class="fas fa-search text-white"></i></button>

                                </div>

                            </div>

                        </div>

                        <div id="client_info_data" class="dashboard_results"></div>

                    </div>


                </div>

                <div class="row">

                    <div class="col-sm-6 col-12 mb-5">

                        <h2>Search Product</h2>

                        <div class="form-group">

                            <div class="input-group">

                                <select class="form-control" name="product" id="product_id" data-url="{{url('product_dashboard_options')}}">

    								<option value="" disabled selected>Choose Product</option>


    							</select>

                                <div class="input-group-append">

                                    <button type="button" class="btn btn-brand" id="dashboard_product_search_btn"><i class="fas fa-search text-white"></i></button>

                                </div>

                            </div>

                        </div>

                        <div id="product_dashboard_details" class="dashboard_results"></div>

                    </div>

                    <div class="col-sm-6 col-12 mb-5">
                        <h2>Search Product By Tags</h2>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name="tags" id="product_tags" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-brand" id="dashboard_product_tags_search_btn"><i class="fas fa-search text-white"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="dashboard_product_tags_table" class="dashboard_results container-fluid"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
@if(Auth::guard('admin')->user()->position == 1)
@section('scripts')
<script src="{{ asset('tagsinput/jquery.amsify.suggestags.js')}}"></script>
<script>
$(document).ready(function() { 
    get_reps();

    $('#product_tags').amsifySuggestags({
		suggestionsAction : {
			url: '{{url("tags_suggestions")}}',
		},
	});

    $('body').on('click', '#dashboard_product_tags_search_btn', function(){
        $('#dashboard_product_tags_table').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');
        products_filter_with_tags();
    });
	function products_filter_with_tags()
	{
		var tags = $('#product_tags').val();
		var action = "{{url('product_tag_serch_dashboard')}}";
		$.ajax({
			type: 'POST',
			data: {tags: tags},
			url: action,
			success: function(data) 
			{
				$('#dashboard_product_tags_table').html(data);
                $('#dashboard_product_tags_table table').DataTable({
                    "iDisplayLength": 100,
                    order: [[2, 'desc']]
                });
			}
		});
	}

    $('body').on('keyup', function(event){
        if(event.ctrlKey && event.key === 'i')
        {
            $('#order_number_filter').focus();
        }
    });

    $('body').on('click', '#dashboard_product_search_btn', function() {
        $('#product_dashboard_details').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');
        var item = $('#product_id').val();
        var action = $('#product_id').attr('data-url');
        $.ajax({
            type: 'POST',
            data: {item: item},
            url: action,
            success: function(data) 
            {
                $('#product_dashboard_details').html(data);
            }
        });    
    });
    
    $('body').on('change', '#dashboard_product_item', function() {
        $('#product_dashboard_details').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');
        var item = $(this).val();
        var action = $(this).attr('data-url');
        $.ajax({
            type: 'POST',
            data: {item: item},
            url: action,
            success: function(data) 

            {

                $('#product_dashboard_details').html(data);

            }

        });    

    });



    $('body').on('click', '#dashboard_search_btn', function() {

        var action = $("#order_number_filter").attr('data-url');

        var search = $("#order_number_filter").val();

        $('#order_dashboard_details').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');

        $.ajax({

            type: 'POST',

            data: {search: search},

            url: action,

            success: function(data) 

            {

                $('#order_dashboard_details').html(data);

                $('.orders_selector_mul_reps').select2({placeholder: "Select Rep"});
                $("#order_number_filter").select();



            }

        }); 

        return false;

    });

    

    $('body').on('keyup', '#order_number_filter', function(e) {

        var code = (e.keyCode ? e.keyCode : e.which);

        if (code == 13) 

        {

            var action = $(this).attr('data-url');

            var search = $(this).val();

            $('#order_dashboard_details').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');

            $.ajax({

                type: 'POST',

                data: {search: search},

                url: action,

                success: function(data) 

                {

                    $('#order_dashboard_details').html(data);
                    $("#order_number_filter").select();


                }

            }); 

            return false;

        }

    });



});

function get_reps()

{

    var action = $("#reps_dashboard_tasks").attr('data-url');

    $.ajax({

        type: 'POST',

        data: {},

        url: action,

        success: function(data) 

        {

            $("#reps_dashboard_tasks").html(data);

            $("#reps_dashboard_tasks").removeClass('notloaded');

            $("#reps_dashboard_tasks").removeAttr('data-url');

        }

    });

    get_purchase_list();



}

function get_tasks()

{

    var action = $("#notes_dashboard_tasks").attr('data-url');

    $.ajax({

        type: 'POST',

        data: {},

        url: action,

        success: function(data)

        {

            $("#notes_dashboard_tasks").html(data);

            $("#notes_dashboard_tasks").removeClass('notloaded');

            $("#notes_dashboard_tasks").removeAttr('data-url');

        }

    });



}

function get_purchase_list()

{

    var action = $("#purchases_dashboard_tasks").attr('data-url');

    $.ajax({

        type: 'POST',

        data: {},

        url: action,

        success: function(data) 

        {

            $("#purchases_dashboard_tasks").html(data);

            $("#purchases_dashboard_tasks").removeClass('notloaded');

            $("#purchases_dashboard_tasks").removeAttr('data-url');
        }
    });
    get_tasks();    
}
</script>

        <script>

            (function () {

                $("#product_id").select2({
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
      $(document).on('click','#client_info_btn',function(){
          var phone=$('#client_info_filter').val();

          var numericSearch = parseInt(phone, 10); // or Number(search);

          if (!isNaN(numericSearch) && phone.length === 11 && phone.startsWith("01")) {
              $('#client_info_data').html('<div class="fa-3x text-center pt-5 mt-5"><i class="fas fa-sync fa-spin"></i></div>');

              setTimeout(function () {
                  $('#client_info_data').load("{{route("admin.getClientInfo")}}?phone="+phone)
              }, 500)
          }

          else {
              toastr.error( 'the phone number not correct');

          }

          })
    </script>

@endsection
@endif