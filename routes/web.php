<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/show_bar_codes', 'BarCodeController@index');

  Route::get('/export_products', 'SiteController@export_products');
  Route::get('/export_notes', 'SiteNoteController@export_notes');


  Route::get('/inventory_test/{id}', 'SiteController@inventory_test');
  Route::get('/test_wp_mysql', 'WooCommerceController@test_wp_mysql');
  Route::get('/test_add_product', 'WooCommerceController@test_add_product');
  Route::get('/wp_update_woocommerce_product/{id}', 'WooCommerceController@wp_update_woocommerce_product');
  Route::get('/testcats', 'WooCommerceController@cats');

Route::group(['namespace' => 'MylerzShipping', 'prefix' => 'mylerz_shipping'], function() {
  Route::get('get_neighborhoods', 'NeighborhoodsController@index');
  Route::get('create_order/{order}', 'OrderController@index');
  Route::get('get_awb/{order}', 'OrderController@get_awb');
});

Route::get('update_inventory_api', 'Api\InventoryController@update_inventory_api');


Route::group(['namespace' => 'Dashboard', 'middleware'=>'admin'], function() {
  Route::get('/', 'DashboardController@index');
  Route::get('/test', 'DashboardController@test');
  Route::get('/profile', 'DashboardController@profile');
  Route::post('/profile', 'DashboardController@save_profile');
  Route::get('/change_password', 'DashboardController@change_password');
  Route::post('/change_password', 'DashboardController@password_save');
  Route::post('/inventory_total_amounts', 'InventoryController@inventory_total_amounts');
  Route::post('/get_reps_data', 'DashboardController@get_reps_data');
  Route::post('/notes_dashboard_tasks', 'DashboardController@notes_dashboard_tasks');
  Route::post('/purchases_dashboard_tasks', 'DashboardController@purchases_dashboard_tasks');
  Route::get('/print_purchase_list', 'DashboardController@print_purchase_list');
  Route::post('/order_dashboard_search', 'DashboardController@order_dashboard_search');
  Route::post('/product_dashboard_options', 'DashboardController@product_dashboard_options');
  Route::get('reports', 'DashboardController@reports');
  Route::post('/report_products_table', 'DashboardController@report_products_table');
    Route::get('getClientInfo', 'DashboardController@getClientInfo')->name('admin.getClientInfo');


    Route::get('product_reports', 'ProductReportController@index');
  Route::post('/report_products_table', 'ProductReportController@report_products_table');

  Route::get('city_reports', 'CityReportController@index');
  Route::post('/report_city_table', 'CityReportController@report_city_table');

  Route::get('reps_reports', 'RepReportController@index');
  Route::post('/report_reps_table', 'RepReportController@report_reps_table');

  Route::get('moderators_reports', 'ModeratorReportController@index');
  Route::post('/report_moderators_table', 'ModeratorReportController@report_moderators_table');

    Route::get('ads_reports', 'AdReportController@index');


    Route::resource('admins', 'AdminController');
  Route::get('admins/edit_password/{id}', 'AdminController@change_password');
  Route::post('admins/save_password/{id}', 'AdminController@password_save');
    Route::get('getAdmins', 'AdminController@getAdmins')->name('admin.getAdmins');


    Route::resource('countries', 'CountryController');
  Route::resource('cities', 'CityController');  
  Route::post('city_zones', 'CityController@city_zones');  
  Route::post('shipping_price_info', 'CityController@shipping_price_info');
  Route::resource('pay_methods', 'PayMethodsController');
  Route::resource('colors', 'ColorController');  
  Route::resource('sizes', 'SizeController');

  Route::resource('clients', 'ClientController');
  Route::post('find_client', 'ClientController@find_client');
  Route::post('client_location/{id}', 'ClientController@client_location');
  Route::get('get_clients', 'ClientController@get_clients')->name('admin.getClients');


    Route::resource('agents', 'AgentController');
  Route::post('find_agent', 'AgentController@find_agent');

  Route::resource('categories', 'CategoryController');
  Route::get('categories/create/{id}', 'CategoryController@create_sub');
  Route::post('get_subs', 'CategoryController@get_subs');

  Route::resource('products', 'ProductController');
    Route::get('getProducts', 'ProductController@getProducts')->name('admin.getProducts');
    Route::get('getTags', 'ProductController@getTags')->name('admin.getTags');

    Route::get('products/timeline/{id}', 'ProductController@products_timeline');
  Route::post('products/new_tags', 'ProductController@products_new_tags');
  Route::resource('products_tags', 'ProductTagController');
  Route::any('tags_suggestions', 'ProductTagController@tags_suggestions');
  Route::post('product_tag_serch', 'ProductTagController@tag_search');
  Route::post('product_tag_serch_dashboard', 'ProductTagController@tag_search_dashboard');
  
  Route::get('product_copy/{id}', 'ProductController@copy_product');
  Route::post('product_discontinue/{id}', 'ProductController@product_discontinue');
  Route::resource('/products_images', 'ProductImageController')->except(['create']);
  Route::get('/products_images/{id}/create', 'ProductImageController@create');

    Route::resource('orders_tags', 'OrderTagController');
    Route::get('order_tag/changePlatform', 'OrderTagController@changePlatform')->name('changePlatform');
    Route::any('order_tags_suggestions', 'OrderTagController@tags_suggestions');

  Route::resource('selling_order', 'SellingOrderController');
  Route::get('selling_order_create2', 'SellingOrderController@create2');
  Route::resource('buying_order', 'BuyingOrderController');
  Route::get('getAgents', 'BuyingOrderController@getAgents')->name('admin.getAgents');
    Route::get('add_payment', 'BuyingOrderController@add_payment')->name('admin.add_payment');
    Route::get('update_payment_amount', 'BuyingOrderController@update_payment_amount')->name('admin.update_payment_amount');


    ///// route for ahmed edit
    Route::get('new_buying_order', 'BuyingOrderController@newcreate');
     Route::post('ajax_store', 'BuyingOrderController@ajax_store')->name('ajax_store');
     Route::delete('buys', 'BuyingOrderController@delete')->name('buys.destroy');
      Route::post('key_enent_ajax', 'BuyingOrderController@key_enent_ajax')->name('key_enent_ajax');
      Route::post('fetch_color', 'BuyingOrderController@fetch_color')->name('fetch_color');
      Route::post('fetch_size', 'BuyingOrderController@fetch_size')->name('fetch_size');
     Route::post('update_qty_ajax', 'BuyingOrderController@update_qty_ajax')->name('update_qty_ajax');
     Route::post('update_price_ajax', 'BuyingOrderController@update_price_ajax')->name('update_price_ajax');
     Route::post('test', 'BuyingOrderController@test')->name('test');
  ///// route for ahmed edit
  Route::post('add_order_item', 'OrderController@add_order_item');
  Route::post('product_options', 'OrderController@product_options');
  Route::post('product_available_units', 'OrderController@product_available_units');
  Route::any('calculate_buyorder_qtys', 'OrderController@calculate_buyorder_qtys');
  Route::resource('order_status', 'OrderStatusController');
    Route::get('changeIsCounted', 'OrderStatusController@changeIsCounted')->name('changeIsCounted');


    Route::resource('order_category', 'OrderCategoryController');

  
  Route::post('selling_order_status', 'SellingOrderController@selling_order_status');
  Route::post('selling_order_collect_date', 'SellingOrderController@selling_order_collect_date');
  Route::get('selling_order/{id}/invoice', 'SellingOrderController@invoice');
  Route::post('selling_order/orders_task', 'SellingOrderController@orders_task');
  Route::get('selling_order/orders_operation/{type}', 'SellingOrderController@orders_operation');
  Route::post('selling_order_notes/{order}', 'SellingOrderController@create_note');
  Route::post('selling_order_notes_multi', 'SellingOrderController@selling_order_notes_multi');
  Route::post('sellorder_notes_viewer', 'SellingOrderController@notes_viewer');
  Route::post('product_price', 'SellingOrderController@sell_order_price');
  Route::post('delete_selling_order_item/{id}', 'SellingOrderController@delete_selling_order_item');
  Route::get('delivery', 'SellingOrderController@delivery');
  Route::get('reps_delivery', 'SellingOrderController@reps_delivery');
  Route::post('selling_order_client/{id}', 'SellingOrderController@selling_order_client');
  Route::post('order_location/{id}', 'SellingOrderController@order_location');

  Route::get('selling_order/delivered/{id}', 'SellingOrderController@delivered_order');
  Route::get('selling_order/rejected/{id}', 'SellingOrderController@rejected_order');
  Route::get('getOrdersTags','SellingOrderController@getOrdersTags')->name('admin.getOrdersTags');


  Route::get('selling_order_a/home2', 'SellingOrderController@home2');
  Route::get('selling_order_search/home_search', 'SellingOrderController@home_search');
  Route::get('order_time_line/{id}', 'SellingOrderController@order_time_line')->name('admin.order_time_line');
  Route::get('order_notes/{id}', 'SellingOrderController@order_notes')->name('admin.order_notes');

  Route::post('buying_order_status', 'BuyingOrderController@selling_order_status');
  Route::get('buying_order/{id}/invoice', 'BuyingOrderController@invoice');
  Route::post('buying_order/orders_task', 'BuyingOrderController@orders_task');
  Route::get('buying_order/orders_operation/{type}', 'BuyingOrderController@orders_operation');
  Route::post('buying_order_notes/{order}', 'BuyingOrderController@create_note');
  Route::post('buyingorder_notes_viewer', 'BuyingOrderController@notes_viewer');

  Route::resource('inventory', 'InventoryController');
  Route::post('inventory_ruined_items', 'InventoryController@ruined_items');
  Route::post('inventory/task', 'InventoryController@task_calc');
  Route::get('inventory_v2', 'InventoryController@indexv2');
  Route::post('inventory_data', 'InventoryController@inventory_data');


    Route::resource('inventories', 'InventoriesController');
    Route::get('ruinedItemFromInventory/{id}', 'InventoriesController@ruinedItemFromInventory')->name('admin.ruinedItemFromInventory');
    Route::get('inventoryGetTotalQty', 'InventoriesController@inventoryGetTotalQty')->name('admin.inventoryGetTotalQty');
    Route::get('inventoryGetTotalAmount', 'InventoriesController@inventoryGetTotalAmount')->name('admin.inventoryGetTotalAmount');
    Route::get('/export_inventories', 'InventoriesController@export_inventories')->name('admin.export_inventories');
    Route::get('/import_inventory', 'InventoriesController@import_inventory')->name('admin.import_inventory');
    Route::post('/import_inventory_store', 'InventoriesController@import_inventory_store')->name('admin.import_inventory_store');
    Route::get('changeInventoryOpen', 'InventoriesController@changeInventoryOpen')->name('changeInventoryOpen');
    Route::get('changeInventoryQty', 'InventoriesController@changeInventoryQty')->name('changeInventoryQty');



    Route::resource('fulfillment', 'FulfillmentController')->except(['show']);
  Route::post('fulfillment/avilable_items', 'FulfillmentController@avilable_items');
  Route::get('fulfillment/print', 'FulfillmentController@print_items');

  Route::get('accounting', 'AccountingController@index');
  Route::get('accounting_report', 'AccountingController@monthly_report');
  /***** Expanses *****/
  Route::resource('expanses_categories', 'ExpanseCategoryController')->except(['create', 'edit']);
  Route::post('/expanses_categories/expanses_categories_task', 'ExpanseCategoryController@expanses_categories_task');
  Route::resource('expanses', 'ExpanseController')->except(['create', 'edit']);
  Route::post('/expanses/expanses_task', 'ExpanseController@expanses_task');
  /***** Partners *****/
  Route::resource('partners_categories', 'PartnerCategoryController')->except(['create', 'edit']);
  Route::post('/partners_categories/partners_categories_task', 'PartnerCategoryController@partners_categories_task');
  Route::resource('partners', 'PartnerController')->except(['create', 'edit']);
  Route::post('/partners/partners_task', 'PartnerController@partners_task');

  Route::get('orders_notes', 'SellingOrderController@all_notes');
  Route::post('orders_notes/order_notes_checker', 'SellingOrderController@order_notes_checker');
  Route::post('load_order_notes', 'SellingOrderController@load_order_notes');

  Route::post('selling_order_note_delete/{id}', 'SellingOrderController@delete_note');
  Route::post('selling_order_note_edit/{id}', 'SellingOrderController@edit_note');    
  
  Route::get('selling_reorder/{id}', 'SellingOrderController@selling_reorder');    
  Route::post('save_selling_reorder/{id}', 'SellingOrderController@save_selling_reorder');



    Route::resource('ads', 'AdController');
    Route::post('ads/updated/{id}', 'AdController@update')->name('ads.updated');
    Route::get('changeAdStatus', 'AdController@changeAdStatus')->name('changeAdStatus');
    Route::get('/export-ads', 'AdController@exportAds')->name('export-ads');
    Route::get('/import-ads', 'AdController@importAds')->name('import-ads');

    Route::post('/import-ads-store', 'AdController@importAdsStore')->name('import-ads-store');
    Route::get('getPlatforms', 'AdController@getPlatforms')->name('admin.getPlatforms');


    Route::resource('logistics', 'LogisticController');
    Route::get('logistics_search', 'LogisticController@logistics_search')->name('admin.logistics_search');


    Route::get('/import_sell_orders', 'LogisticController@import_sell_orders')->name('admin.import_sell_orders');
    Route::post('/import_sell_orders_store', 'LogisticController@import_sell_orders_store')->name('admin.import_sell_orders_store');

    Route::post('copyOrderNumber','LogisticController@copyOrderNumber')->name('admin.copyOrderNumber');


    Route::resource('active_ads', 'ActiveAdController');

    Route::resource('file_gard', 'FileGardController');
    Route::delete('detail_file_delete/{id}','FileGardController@detail_file_delete')->name('admin.detail_file_delete');




        Route::resource('fulfillments', 'FulfillmentsController');
        Route::post('fulfillments_action/{id}', 'FulfillmentsController@fulfillments_action')->name('admin.fulfillments_action');


        ### integrations ####



});
Route::group([], function () {
    

  Route::get('/login', 'AdminAuth\LoginController@showLoginForm')->name('login');
  Route::post('/login', 'AdminAuth\LoginController@login');
  Route::get('/logout', 'AdminAuth\LoginController@logout')->name('logout');
});





Route::get('/clear/route', function (){
//    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('migrate');
//    \Illuminate\Support\Facades\Artisan::call('route:clear');

    return 'Optimize Cleared Successfully By El Sdodey';
});


