<?php

use App\SellOrder;
use App\BuyOrder;

use App\OrderNote;
use App\OrderNoteStatus;

use App\BuyOrderNote;
use App\BuyOrderNoteStatus;

use App\Fulfillment;
use App\RuinedItem;
use App\SellOrderItem;
use App\BuyOrderItem;
use App\OrderNoteTag;
use App\Partner;
use App\Expanse;

use App\Lib\Permissions;
use App\AdminPermission;

use App\OrderNoteRep;

use App\Admin;
use App\Inventory;

use App\Product;
use App\Category;
use App\TimeLine;

use App\TagGroup;

use Codexshaper\WooCommerce\Facades\Product as WooCommerceProduct;
use Codexshaper\WooCommerce\Facades\Category as WooCommerceCategory;
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;

use App\ProductColor;
use App\ProductSize;



function get_all_tags()
{
    include 'app.Traits.Chat.php';

    $tags = TagGroup::pluck('title')->toArray();
    $oo = "";
    for ($i = 0; $i < count($tags); $i++) {
        if ($oo != '') {
            $oo .= ",";
        }
        $oo .= '"' . $tags[$i] . '"';
    }
    return $oo;
}

function get_mylerz_awb($order)
{
    $order_info = SellOrder::findorfail($order);
    if ($order_info->mylerz_barcode != '') {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.mylerz.net/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=three.stores&password=Ahmed_1991&grant_type=password',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $access_token = $response['access_token'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.mylerz.net/api/Packages/GetAWB',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "Barcode": "' . $order_info->mylerz_barcode . '", 
                "ReferenceNumber": "' . $order_info->order_number . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);
        if (array_key_exists('Value', $response)) {
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="mylerz-awb-' . $order_info->order_number . '.pdf"');
            echo base64_decode($response['Value']);
        }
    }
}

function create_mylerz_order($order)
{
    $order_info = SellOrder::findorfail($order);
    if ($order_info->note != '') {
        $note = $order_info->note;
    } else {
        $note = "Deliver Carefully";
    }

    if ($order_info->mylerz_barcode != '') {
        return true;
    }
    $cc = TimeLine::where('order', $order_info->id)->where('order_type', 3)->first();
    sleep(1);
    $order_info = SellOrder::findorfail($order);
    if ($order_info->mylerz_barcode != '') {
        return true;
    }
    $cc = TimeLine::where('order', $order_info->id)->where('order_type', 3)->first();
    if ($cc === NULL) {
        $order_status_deliver = NULL;
        $has_deliver = false;
        $has_return = false;
        $total_deliver = 0;
        foreach ($order_info->itemsq as $item) {
            if ($item->qty > 0) {
                $has_deliver = true;
                $total_deliver = $total_deliver + ($item->qty * $item->price);
            } else {
                $has_return = true;
                $total_deliver = $total_deliver + ($item->qty * $item->price);
            }
        }
        $event = new TimeLine;
        $event->admin = 11;
        $event->order = $order_info->id;
        $event->order_type = 3;
        $event->text = " Sent to Mylerz System";
        $event->save();


        $pieces = "";
        $pieces .= '{"PieceNo":1,"Weight":"0","ItemCategory":"Products","Dimensions":"","Special_Notes": "Products"}';
        // dd($pieces);
        $total = $order_info->total_price + $order_info->shipping_fees;
        if (substr($order_info->order_number, 0, 2) === "Re") {
            $service_category = "RETURN";
            $Payment_Type = "PP";
        } else {
            $service_category = "Delivery";
            if ($total > 0) {
                $Payment_Type = "COD";
            } else {
                $Payment_Type = "PP";
            }
        }
        if ($has_deliver && $total > 0) {
            $service_category = "Delivery";
            $Payment_Type = "COD";
            $payloads = '[
                {
                    "WarehouseName":"Three Stores",
                    "PickupDueDate": "' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                    "Package_Serial": "' . $order_info->id . '",
                    "Reference": "' . $order_info->order_number . '",
                    "Description": "' . $note . '",
                    "Total_Weight": 0,
                    "Service_Type": "CTD",
                    "Service": "SD",
                    "ServiceDate":"' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                    "Service_Category": "' . $service_category . '",
                    "Payment_Type": "' . $Payment_Type . '",
                    "COD_Value": "' . $total . '",
                    "Customer_Name": "' . optional($order_info->client_info)->name . '",
                    "Mobile_No": "' . optional($order_info->client_info)->phone . '",
                    "Building_No": "",
                    "Street": "' . $order_info->address . '",
                    "Floor_No": "",
                    "Apartment_No": "",
                    "Country": "Egypt",
                    "City": "' . optional($order_info->city_info)->mylerz_neighborhood . '",
                    "Neighborhood": "' . optional($order_info->city_info)->mylerz_district . '",
                    "District": "",
                    "GeoLocation": "",
                    "Address_Category": "H",
                    "CustVal": "",
                    "Currency": "",
                    "Pieces": [' . $pieces . ']
                }
            ]';

            $order_info = SellOrder::findorfail($order);
            if ($order_info->mylerz_barcode != '') {
                return true;
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'username=three.stores&password=Ahmed_1991&grant_type=password',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                )
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            $access_token = $response['access_token'];


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/api/Orders/AddOrders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response, true);
            if (array_key_exists('Value', $response)) {
                if (array_key_exists('Packages', $response['Value'])) {
                    if (count($response['Value']['Packages']) > 0) {
                        if (array_key_exists('BarCode', $response['Value']['Packages'][0])) {
                            $order_info->mylerz_barcode = $response['Value']['Packages'][0]['BarCode'];
                            $order_info->shipping_number = $response['Value']['Packages'][0]['BarCode'];
                            $order_info->save();
                        }

                    }
                }
            }
        } else if ($has_deliver && $total <= 0) {
            $service_category = "Delivery";
            $Payment_Type = "PP";
            $payloads = '[
            {
                "WarehouseName":"Three Stores",
                "PickupDueDate": "' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                "Package_Serial": "' . $order_info->id . '",
                "Reference": "' . $order_info->order_number . '",
                "Description": "' . $note . '",
                "Total_Weight": 0,
                "Service_Type": "CTD",
                "Service": "SD",
                "ServiceDate":"' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                "Service_Category": "' . $service_category . '",
                "Payment_Type": "' . $Payment_Type . '",
                "COD_Value": "0",
                "Customer_Name": "' . optional($order_info->client_info)->name . '",
                "Mobile_No": "' . optional($order_info->client_info)->phone . '",
                "Building_No": "",
                "Street": "' . $order_info->address . '",
                "Floor_No": "",
                "Apartment_No": "",
                "Country": "Egypt",
                "City": "' . optional($order_info->city_info)->mylerz_neighborhood . '",
                "Neighborhood": "' . optional($order_info->city_info)->mylerz_district . '",
                "District": "",
                "GeoLocation": "",
                "Address_Category": "H",
                "CustVal": "",
                "Currency": "",
                "Pieces": [' . $pieces . ']
            }
            ]';

            $order_info = SellOrder::findorfail($order);
            if ($order_info->mylerz_barcode != '') {
                return true;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'username=three.stores&password=Ahmed_1991&grant_type=password',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                )
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            $access_token = $response['access_token'];


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/api/Orders/AddOrders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response, true);
            if (array_key_exists('Value', $response)) {
                if (array_key_exists('Packages', $response['Value'])) {
                    if (count($response['Value']['Packages']) > 0) {
                        if (array_key_exists('BarCode', $response['Value']['Packages'][0])) {
                            $order_info->mylerz_barcode = $response['Value']['Packages'][0]['BarCode'];
                            $order_info->shipping_number = $response['Value']['Packages'][0]['BarCode'];
                            $order_info->save();
                        }

                    }
                }
            }
        }
        if ($has_return) {
            $service_category = "RETURN";
            $Payment_Type = "PP";
            $payloads = '[
                {
                    "WarehouseName":"Three Stores",
                    "PickupDueDate": "' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                    "Package_Serial": "' . $order_info->id . '",
                    "Reference": "' . $order_info->order_number . '",
                    "Description": "' . $note . '",
                    "Total_Weight": 0,
                    "Service_Type": "DTC",
                    "Service": "SD",
                    "ServiceDate":"' . date('Y-m-d') . 'T' . date('H:i:s') . '",
                    "Service_Category": "' . $service_category . '",
                    "Payment_Type": "' . $Payment_Type . '",
                    "COD_Value": "0",
                    "Customer_Name": "' . optional($order_info->client_info)->name . '",
                    "Mobile_No": "' . optional($order_info->client_info)->phone . '",
                    "Building_No": "",
                    "Street": "' . $order_info->address . '",
                    "Floor_No": "",
                    "Apartment_No": "",
                    "Country": "Egypt",
                    "City": "' . optional($order_info->city_info)->mylerz_neighborhood . '",
                    "Neighborhood": "' . optional($order_info->city_info)->mylerz_district . '",
                    "District": "",
                    "GeoLocation": "",
                    "Address_Category": "H",
                    "CustVal": "",
                    "Currency": "",
                    "Pieces": [' . $pieces . ']
                }
            ]';

            // $order_info = SellOrder::findorfail($order);
            // if($order_info->mylerz_barcode != '') {return true;}
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'username=three.stores&password=Ahmed_1991&grant_type=password',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                )
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            $access_token = $response['access_token'];


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/api/Orders/AddOrders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $access_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response, true);
            if (!$has_deliver) {
                if (array_key_exists('Value', $response)) {
                    if (array_key_exists('Packages', $response['Value'])) {
                        if (count($response['Value']['Packages']) > 0) {
                            if (array_key_exists('BarCode', $response['Value']['Packages'][0])) {
                                $order_info->mylerz_barcode = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->shipping_number = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->save();
                            }

                        }
                    }
                }
            }
        }
    }
}


function xoxoxoxoproduct_available_units($product, $color, $size)
{
    $items = 0;
    $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [8])->pluck('id');
    $sitems = SellOrderItem::
    whereIn('sell_order_items.order', $sorders)
        ->where('sell_order_items.product', $product)
        ->where('sell_order_items.color', $color)
        ->where('sell_order_items.size', $size)
        ->sum('qty');

    $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
    $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
    $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
    if ($cr !== NULL) {
        $ruined = $cr->qty;
    } else {
        $ruined = 0;
    }
    $items = $bitems - ($sitems + $ruined);
    return $items;
}


if (!function_exists('product_fullfilment_units')) {
    function product_fullfilment_units($product, $color, $size)
    {
        $itemsCount = SellOrderItem::whereHas('order_info', function ($query) {
            $query->where('hide', 0)
                ->whereIn('sell_orders.status', [1, 11]);
        })
            ->where('sell_order_items.fulfillment', 0)
            ->where('sell_order_items.product',$product)
            ->where('sell_order_items.color',$color)
            ->where('sell_order_items.size',$size)
            ->where('sell_order_items.hide', 0)
            ->join('sell_orders', 'sell_order_items.order', '=', 'sell_orders.id')
            ->sum('qty');
        return $itemsCount;
    }
}

if (!function_exists('vvjupdate_woocommerce_product')) {
    function vvjupdate_woocommerce_product($product_id)
    {
        $product = Product::findorfail($product_id);
        /*
        if($product->woocommerce > 0)
        {
            $variations = Variation::all($product->woocommerce);
            $options = ['force' => true];
            foreach ($variations as $v)
            {
                Variation::delete($product->woocommerce, $v->id, $options);
            }
        }
        */
        $product_price = number_format($product->price / 100, 2);
        $category = Category::findorfail($product->cat);
        $cat = $category->woocommerce_id;

        $images = array();
        foreach ($product->images as $image) {
            $images [] = array('src' => asset($image->image));
        }
        if (count($images) == 0) {
            $images [] = array('src' => 'http://three-store.com/site1/wp-content/uploads/2020/10/logo-white.png');
        }

        $colors = array();
        $colorsid = array();

        foreach ($product->colors as $color) {
            if ($color->color_info->name != '') {
                $colors[] = $color->color_info->name;
            } else {
                $colors[] = $color->color_info->title;
            }
            $colorsid[] = $color->color_info->id;
        }
        $sizes = array();
        $sizesid = array();
        foreach ($product->sizes as $size) {
            $sizes[] = $size->size_info->title;
            $sizesid[] = $size->size_info->id;
        }

        $attributes = array();
        if (count($colors) > 0) {
            $attributes[] = array('name' => 'Color', 'position' => 1, 'visible' => true, 'variation' => true, 'options' => $colors);
        } else {
            $attributes[] = array('name' => 'Color', 'position' => 1, 'visible' => false, 'variation' => false, 'options' => $colors);
        }
        if (count($sizes) > 0) {
            $attributes[] = array('name' => 'Size', 'position' => 2, 'visible' => true, 'variation' => true, 'options' => $sizes);
        } else {
            $attributes[] = array('name' => 'Size', 'position' => 2, 'visible' => false, 'variation' => false, 'options' => $sizes);
        }
        $type = 'variable';

        if (count($colors) == 0 && count($sizes) == 0) {
            $data =
                [
                    'name' => $product->title,
                    'type' => 'simple',
                    'stock_status' => 'instock',
                    'manage_stock' => true,
                    'stock_quantity' => get_product_qty($product->id, 0, 0),
                    'price' => ".$product_price.",
                    'regular_price' => ".$product_price.",
                    'categories' => [
                        ['id' => $cat]
                    ],
                    'images' => $images
                ];
            if ($product->woocommerce == 0) {
                $product_woo = WooCommerceProduct::create($data);
                $product->woocommerce = $product_woo->id;
                $product->save();
            } else if ($product->woocommerce > 0) {
                $product_woo = WooCommerceProduct::update($product->woocommerce, $data);
            }
        } else {
            if ($product->woocommerce == 0) {
                $data =
                    [
                        'name' => $product->title,
                        'type' => 'variable',
                        'stock_status' => 'instock',
                        'regular_price' => ".$product_price.",
                        'description' => $product->text,
                        'short_description' => strip_tags($product->text),
                        'categories' => [
                            ['id' => $cat]
                        ],
                        'images' => $images,
                        'attributes' => $attributes
                    ];
                $product_woo = WooCommerceProduct::create($data);
                $product->woocommerce = $product_woo->id;
                $product->save();
            } else if ($product->woocommerce > 0) {
                $data =
                    [
                        'name' => $product->title,
                        'type' => 'variable',
                        'stock_status' => 'instock',
                        'regular_price' => ".$product_price.",
                        'categories' => [
                            ['id' => $cat]
                        ],
                        'images' => $images,
                        'attributes' => $attributes
                    ];
                $product_woo = WooCommerceProduct::update($product->woocommerce, $data);
            }
            if (count($colors) > 0 && count($sizes) > 0) {
                for ($i = 0; $i < count($colors); $i++) {
                    $color_images = array();
                    foreach ($product->images as $image) {
                        if ($image->color == $colorsid[$i]) {
                            $color_images[] = array('src' => asset($image->image));
                        }
                    }
                    if (count($color_images) == 0) {
                        $color_images = $images;
                    }
                    for ($x = 0; $x < count($sizes); $x++) {
                        $inventory = Inventory::where('product', $product->id)->where('color', $colorsid[$i])->where('size', $sizesid[$x])->first();
                        if ($inventory === NULL || $inventory->woocommerce == 0) {
                            $product_id = $product_woo->id;
                            $data = [
                                'regular_price' => ".$product_price.",
                                'image' => $color_images[0],
                                'stock_status' => 'instock',
                                'manage_stock' => true,
                                'stock_quantity' => get_product_qty($product->id, $colorsid[$i], $sizesid[$x]),
                                'attributes' => [
                                    [
                                        "name" => "Color",
                                        "option" => $colors[$i]
                                    ],
                                    [
                                        "name" => "Size",
                                        "option" => $sizes[$x]
                                    ]
                                ]
                            ];
                            $inventory = Inventory::where('product', $product->id)->where('color', $colorsid[$i])->where('size', $sizesid[$x])->first();
                            $variation = Variation::create($product_id, $data);
                            $inventory->woocommerce = $variation->id;
                            $inventory->save();
                        }
                    }
                }
            } else if (count($colors) > 0 && count($sizes) == 0) {
                for ($i = 0; $i < count($colors); $i++) {
                    $color_images = array();
                    foreach ($product->images as $image) {
                        if ($image->color == $colorsid[$i]) {
                            $color_images[] = array('src' => asset($image->image));
                        }
                    }
                    if (count($color_images) == 0) {
                        $color_images = $images;
                    }
                    $inventory = Inventory::where('product', $product->id)->where('color', $colorsid[$i])->where('size', 0)->first();
                    if ($inventory === NULL || $inventory->woocommerce == 0) {
                        $product_id = $product_woo->id;
                        $data = [
                            'regular_price' => ".$product_price.",
                            'image' => $color_images[0],
                            'stock_status' => 'instock',
                            'manage_stock' => true,
                            'stock_quantity' => get_product_qty($product->id, $colorsid[$i], 0),
                            'attributes' => [
                                [
                                    "name" => "Color",
                                    "option" => $colors[$i]
                                ]
                            ]
                        ];
                        $inventory = Inventory::where('product', $product->id)->where('color', $colorsid[$i])->where('size', 0)->first();
                        $variation = Variation::create($product_id, $data);
                        $inventory->woocommerce = $variation->id;
                        $inventory->save();
                    }
                }
            } else if (count($colors) == 0 && count($sizes) > 0) {
                $images = array();
                foreach ($product->images as $image) {
                    $images[] = array('src' => asset($image->image));
                }
                for ($i = 0; $i < count($sizes); $i++) {
                    $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $sizesid[$i])->first();
                    if ($inventory === NULL || $inventory->woocommerce == 0) {
                        $product_id = $product_woo->id;
                        $data = [
                            'regular_price' => ".$product_price.",
                            'image' => $images,
                            'stock_status' => 'instock',
                            'manage_stock' => true,
                            'stock_quantity' => get_product_qty($product->id, 0, $sizesid[$i]),
                            'attributes' => [
                                [
                                    "name" => "Size",
                                    "option" => $sizes[$i]
                                ]
                            ]
                        ];
                        $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $sizesid[$i])->first();
                        $variation = Variation::create($product_id, $data);
                        $inventory->woocommerce = $variation->id;
                        $inventory->save();
                    }
                }
            }
        }
    }
}


if (!function_exists('get_product_qty_alt')) {
    function get_product_qty_alt($product, $color, $size)
    {
        $product_info = Product::findorfail($product);
        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();
        $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
        $sold = SellOrderItem::whereHas('order_info', function ($query) {
                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [8]);
            })->where('sell_order_items.product', $product)->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)->where('sell_order_items.qty', '>', 0)
                ->sum('qty') + SellOrderItem::whereHas('order_info', function ($query) {
                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->where('status', 6);
            })->where('sell_order_items.product', $product)->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)->where('sell_order_items.qty', '<', 0)
                ->sum('qty');

        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
        $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
        $total = 0;
        if ($inventory === NULL) {
            $inventory = new Inventory;
            $inventory->product = $product;
            $inventory->color = $color;
            $inventory->size = $size;
            $inventory->sold = $sold;
            $inventory->bought = $bought;
            $inventory->save();
        } else {
            $inventory->sold = $sold;
            $inventory->bought = $bought;
            $inventory->save();
        }
        if ($cr !== NULL) {
            $ruined = $cr->qty;
        } else {
            $ruined = 0;
        }

        $total = $inventory->bought - ($inventory->sold + $ruined);

        return $total;

    }
}

//if (!function_exists('get_product_qty')) {
//    function get_product_qty($product, $color, $size)
//    {
//        $product_info = Product::findorfail($product);
//        $sku = "Three00" . $product_info->id;
//        if ($color > 0 && $size == 0) {
//            $sku .= "-00" . $color . "-000";
//        } else if ($color > 0 && $size > 0) {
//            $sku .= "-00" . $color . "-00" . $size;
//        } else if ($color == 0 && $size > 0) {
//            $sku .= "-000-00" . $size;
//        }
//        $woocommerce_product = $product_info->woocommerce;
//        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();
//        $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
//        $sold = SellOrderItem::whereHas('order_info', function ($query) {
//                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [8]);
//            })->where('sell_order_items.product', $product)->where('sell_order_items.color', $color)
//                ->where('sell_order_items.size', $size)->where('sell_order_items.qty', '>', 0)
//                ->sum('qty') + SellOrderItem::whereHas('order_info', function ($query) {
//                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->where('status', 6);
//            })->where('sell_order_items.product', $product)->where('sell_order_items.color', $color)
//                ->where('sell_order_items.size', $size)->where('sell_order_items.qty', '<', 0)
//                ->sum('qty');
//
//
//        $sold = (int)$sold;
//        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
//        $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
//        $bought = (int)$bought;
//        $total = 0;
//        if ($inventory === NULL) {
//            $inventory = new Inventory;
//            $inventory->product = $product;
//            $inventory->color = $color;
//            $inventory->size = $size;
//        }
//        $inventory->sold = $sold;
//        $inventory->bought = $bought;
//        $inventory->save();
//
//        if ($cr !== NULL) {
//            $ruined = $cr->qty;
//        } else {
//            $ruined = 0;
//        }
//        $total = $inventory->bought - ($inventory->sold + $ruined);
//
//
//        return $total;
//
//    }
//}


//if (!function_exists('get_product_total_qty')) {
//    function get_product_total_qty($product)
//    {
//        $product_info = Product::findorfail($product);
//
//        $cr = RuinedItem::where('product', '=', $product)->first();
//
//        $sold = SellOrderItem::whereHas('order_info', function ($query) {
//                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [8]);
//            })->where('sell_order_items.product', $product)->where('sell_order_items.qty', '>', 0)
//                ->sum('qty') + SellOrderItem::whereHas('order_info', function ($query) {
//                $query->where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->where('status', 6);
//            })->where('sell_order_items.product', $product)->where('sell_order_items.qty', '<', 0)
//                ->sum('qty');
//
//
//        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
//        $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->sum('qty');
//        $bought = (int)$bought;
//        $total = 0;
//
//
//        if ($cr !== NULL) {
//            $ruined = $cr->qty;
//        } else {
//            $ruined = 0;
//        }
//        $total = $bought - ($sold + $ruined);
//
//
//        return $total;
//
//    }
//}

if (!function_exists('note_tags')) {
    function note_tags($note)
    {
        $reps = OrderNoteTag::where('note', $note)->get();
        $aas = array();
        foreach ($reps as $ss) {
            $aas[] = $ss->tag;
        }

        return $aas;
    }
}

if (!function_exists('note_reps')) {
    function note_reps($note)
    {
        $reps = OrderNoteRep::where('note', $note)->get();
        $aas = array();
        foreach ($reps as $ss) {
            $aas[] = $ss->rep;
        }

        return $aas;
    }
}
if (!function_exists('ruinded_items_admn')) {
    function ruinded_items_admn($product, $color, $size)
    {
        $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
        if ($cr !== NULL) {
            if ($cr->added_by > 0) {
                $admin = Admin::where('id', $cr->added_by)->first();
                return "<p><b>Last Update BY </b> " . $admin->name . " <b>At</b> " . date('Y-m-d h:i A', strtotime($cr->updated_at)) . "</p>";
            }
        }
    }
}

if (!function_exists('get_product_cost')) {
    function get_product_cost($product, $color, $size)
    {
        $cost = 0;
        $items = array();
        $totals = 0;
        $orders = BuyOrder::where('hide', '=', 0)->get();
        foreach ($orders as $order) {
            foreach ($order->itemsq->where('product', $product)->where('color', $color)->where('size', $size) as $item) {
                $itx = $item->product . "_" . $item->color . $item->size;
                if (in_array($itx, $items)) {
                    $in = array_search($itx, $items);
                    $cost = $cost + ($item->qty * $item->price);
                    $totals = $item->qty + $totals;
                } else {
                    $items[] = $itx;
                    $px = "";
                    $px = $item->product_info->title;
                    if ($item->color > 0) {
                        $px .= " - " . $item->color_info->title;
                    }
                    if ($item->size > 0) {
                        $px .= " - " . $item->size_info->title;
                    }
                    $cr = RuinedItem::where('product', '=', $item->product)->where('color', '=', $item->color)->where('size', '=', $item->size)->first();
                    if ($cr !== NULL) {
                        $ruined = $cr->qty;
                    } else {
                        $ruined = 0;
                    }
                    $cost = $item->qty * $item->price;
                    $totals = $item->qty;
                }
            }
        }
        if ($totals != 0) {
            $cost = $cost / $totals;
        } else {
            $cost = 0;
        }
        return $cost;
    }
}

if (!function_exists('uncompleted_notes')) {
    function uncompleted_notes($date = NULL)
    {
        if ($date == NULL) {
            $all_notes = OrderNote::orderBy('created_at', 'desc')->where('status', '=', 0)->get();
            return $all_notes;
        } else {
            $all_notes = OrderNote::where('created_at', '>=', $date)->orderBy('created_at', 'desc')->where('status', '=', 0)->get();
            return $all_notes;
        }
    }
}

if (!function_exists('update_all_inventory')) {
    function update_all_inventory()
    {
        $inventory = Inventory::get();
        foreach ($inventory as $iory) {
            $i++;
            $product = $iory->product;
            $color = $iory->color;
            $size = $iory->size;
            get_product_qty($iory->product, $iory->color, $iory->size);
        }
    }
}

if (!function_exists('purchases_data')) {
    function purchases_data()
    {
        $itemsCount = SellOrderItem::whereHas('order_info', function ($query) {
            $query->where('hide', 0)
                ->whereIn('sell_orders.status', [1, 11]);
        })
            ->where('sell_order_items.fulfillment', 0)
            ->where('sell_order_items.hide', 0)
            ->join('sell_orders', 'sell_order_items.order', '=', 'sell_orders.id')
            ->groupBy('product', 'color', 'size')
            ->selectRaw('product, color, size, SUM(qty) as count')
            ->orderBy('count', 'desc') // Order by count in descending order
            ->get();

// Now $itemsCount contains the count for each unique combination of product, color, and size

        return $itemsCount;
    }
}

if (!function_exists('reps_data')) {
    function reps_data()
    {
        $admins = Admin::where('hide', '=', 0)->where('position', '=', 2)->get();
        $data = array();
        $all = array();
        $ret = array();
        $xx = 0;
        foreach ($admins as $admin) {

            $shipped = SellOrder::where('hide', '=', 0)->where('delivered_by', $admin->id)->where('status', 4)->get()->count();
            $delivered = SellOrder::where('hide', '=', 0)->where('delivered_by', $admin->id)->where('status', 5)->get()->count();
            $rejected = SellOrder::where('hide', '=', 0)->where('delivered_by', $admin->id)->where('status', 7)->get()->count();
            $total = $shipped + $delivered + $rejected;
            $data[] = array($admin->name, $total, $shipped, $delivered, $rejected);
            $all[] = array($total, $xx);
            $xx++;
        }
        rsort($all);
        $ret[] = $all;
        $ret[] = $data;
        return $ret;
    }
}

if (!function_exists('order_stats_data')) {
    function order_stats_data($days, $type)
    {
        $orders = SellOrder::where('hide', '=', 0);

        if ($type == 'win') {
            $orders = $orders->whereIn('status', array(5, 6));
        } else if ($type == 'loss') {
            $orders = $orders->whereIn('status', array(7, 8));
        } else if ($type == 'open') {
            $orders = $orders->whereNotIn('status', array(5, 6, 7, 8));
        }

        if ($days >= 7) {
            $from = date('Y-m-d', strtotime('- ' . $days . ' Days')) . " 00:00:00";
            $orders = $orders->whereBetween('created_at', array($from, date('Y-m-d') . " 23:59:59"));
        } else if ($days == 0) {
            $orders = $orders->whereBetween('created_at', array(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59"));
        } else if ($days == 1) {
            $from = date('Y-m-d', strtotime('- ' . $days . ' Days')) . " 00:00:00";
            $to = date('Y-m-d', strtotime('- ' . $days . ' Days')) . " 23:59:59";
            $orders = $orders->whereBetween('created_at', array($from, $to));
        }
        $orders = $orders->get();

        return $orders->count();
    }
}

if (!function_exists('get_all_permissions')) {
    function get_all_permissions()
    {
        $permissions = new Permissions;
        return $permissions->all_permissions();
    }
}

if (!function_exists('permission_checker')) {
    function permission_checker($admin, $permission)
    {
        $admin_info = Admin::findorfail($admin);
        if ($admin_info->position == 2) {
            return false;
        }
        $apx = AdminPermission::where('admin', $admin)->where('permission', $permission)->first();
        if ($apx === NULL) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('permission_group_checker')) {
    function permission_group_checker($admin, $permission)
    {
        $admin_info = Admin::findorfail($admin);
        if ($admin_info->position == 2) {
            return false;
        }
        $permissions = new Permissions;
        $pxx = $permissions->permissions_group($permission);
        $res = false;
        foreach ($pxx as $key => $value) {
            $apx = AdminPermission::where('admin', $admin)->where('permission', $key)->first();
            if ($apx != NULL) {
                $res = true;
            }
        }
        return $res;
    }
}

if (!function_exists('calculate_site_income')) {
    function calculate_site_income($day, $month, $year)
    {
        $total_price = SellOrder::where('hide', '=', 0)->where('status', '=', 6);
        $shipping_fees = SellOrder::where('hide', '=', 0)->where('status', '=', 6);
        if ($day > 0) {
            $total_price = $total_price->whereDay('collected_date', '=', $day);
            $shipping_fees = $shipping_fees->whereDay('collected_date', '=', $day);
        }
        if ($month > 0) {
            $total_price = $total_price->whereMonth('collected_date', '=', $month);
            $shipping_fees = $shipping_fees->whereMonth('collected_date', '=', $month);
        }
        if ($year > 0) {
            $total_price = $total_price->whereYear('collected_date', '=', $year);
            $shipping_fees = $shipping_fees->whereYear('collected_date', '=', $year);
        }
        $total = $total_price->sum('total_price') + $shipping_fees->sum('shipping_fees');
        // foreach ($orders as $order)
        // {
        //     foreach ($order->itemsq as $item)
        //     {
        //         $total = $total + ($item->qty * $item->price);
        //     }
        // }
        return $total;

    }
}


if (!function_exists('calculate_bought_orders')) {
    function calculate_bought_orders($day, $month, $year)
    {
        $orders = BuyOrder::where('hide', '=', 0);

        if ($day > 0) {
            $orders->whereDay('created_at', '=', $day);
        }

        if ($month > 0) {
            $orders->whereMonth('created_at', '=', $month);
        }

        if ($year > 0) {
            $orders->whereYear('created_at', '=', $year);
        }

        $total = $orders->sum('payment_amount');

        return $total;
    }
}


if (!function_exists('calculate_site_partner')) {
    function calculate_site_partner($cat, $day, $month, $year)
    {
        $total = 0;
        $expanses = Partner::where('hide', '=', 0);
        if ($cat > 0) {
            $expanses = $expanses->where('cat', '=', $cat);
        }
        if ($day > 0) {
            $expanses = $expanses->whereDay('added_at', '=', $day);
        }
        if ($month > 0) {
            $expanses = $expanses->whereMonth('added_at', '=', $month);
        }
        if ($year > 0) {
            $expanses = $expanses->whereYear('added_at', '=', $year);
        }
        $total = $expanses->sum('amount');
        return $total;
    }
}

if (!function_exists('calculate_site_expanse')) {
    function calculate_site_expanse($cat, $day, $month, $year)
    {
        $total = 0;
        $expanses = Expanse::where('hide', '=', 0);
        if ($cat > 0) {
            $expanses = $expanses->where('cat', '=', $cat);
        }
        if ($day > 0) {
            $expanses = $expanses->whereDay('added_at', '=', $day);
        }
        if ($month > 0) {
            $expanses = $expanses->whereMonth('added_at', '=', $month);
        }
        if ($year > 0) {
            $expanses = $expanses->whereYear('added_at', '=', $year);
        }
        $total = $expanses->sum('amount');
        return $total;
    }
}

function order_notes_checker($item)
{
    return true;
}

function fulfillment_avilable_item($item, $index)
{
    $ch = Fulfillment::where('item_index', '=', $index)->where('item', '=', $item)->first();
    if ($ch === NULL) {
        return false;
    } else {
        return true;
    }
}

function get_admin_notifications()
{
    return array();
}

function buy_order_has_colors($order)
{
    $res = false;
    $aa = BuyOrder::findorfail($order);
    foreach ($aa->items as $item) {
        if ($item->color > 0) {
            $res = true;
        }
    }
    return $res;
}

function buy_order_has_sizes($order)
{
    $res = false;
    $aa = BuyOrder::findorfail($order);
    foreach ($aa->items as $item) {
        if ($item->size > 0) {
            $res = true;
        }
    }
    return $res;
}

function order_has_colors($order)
{
    $res = false;
    $aa = SellOrder::findorfail($order);
    foreach ($aa->items as $item) {
        if ($item->color > 0) {
            $res = true;
        }
    }
    return $res;
}

function order_has_sizes($order)
{
    $res = false;
    $aa = SellOrder::findorfail($order);
    foreach ($aa->items as $item) {
        if ($item->size > 0) {
            $res = true;
        }
    }
    return $res;
}

function order_notes_stats($order)
{
    $last_note = OrderNote::where('order', '=', $order)->orderBy('id', 'desc')->first();
    if ($last_note !== NULL) {
        $last_viewed = OrderNoteStatus::where('admin', '=', Auth::guard('admin')->user()->id)->where('order', '=', $order)->orderBy('note', 'desc')->first();
        if ($last_viewed === NULL) {
            return false;
        } else {
            if ($last_note->id != $last_viewed->note) {
                return false;
            }
        }
    } else {
        return true;
    }
}


function buy_order_notes_stats($order)
{
    $last_note = BuyOrderNote::where('order', '=', $order)->orderBy('id', 'desc')->first();
    if ($last_note !== NULL) {
        $last_viewed = BuyOrderNoteStatus::where('admin', '=', Auth::guard('admin')->user()->id)->where('order', '=', $order)->orderBy('note', 'desc')->first();
        if ($last_viewed === NULL) {
            return false;
        } else {
            if ($last_note->id != $last_viewed->note) {
                return false;
            }
        }
    } else {
        return true;
    }
}


if (!function_exists('update_sold_inventory')) {

    function update_sold_inventory($product, $color, $size, $qty, $type, $old_status = null, $new_status = null)
    {
        $product_info = Product::findorfail($product);

        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'sold' => 0,
                'bought' => 0,
            ]);
        }
        $old_sold = $inventory->sold;
        $new_sold = $old_sold;
        if ($type == 'add') {
            if ($qty > 0) {
                $new_sold = $old_sold + $qty;
            }
        }
        elseif ($type == 'convert') {
            // التحويلات السالبة
            if ($qty < 0) {
                if ($old_status->id == 6) {
                    $new_sold = $old_sold - $qty;
                }
                elseif ($new_status->id==6){
                    $new_sold = $old_sold + $qty;
                }
                else
                {

                }
            } // التحويلات الموجبة

            else {

                if ($old_status->is_counted != $new_status->is_counted){

                    if ($old_status->is_counted==1 && $new_status->is_counted==0)
                    {
                        $new_sold = $old_sold - $qty;

                    }
                    elseif ($old_status->is_counted==0 && $new_status->is_counted==1)
                    {
                        $new_sold = $old_sold + $qty;

                    }
                    else{

                    }
                }
            }
        }





        $inventory->sold=$new_sold;
        $inventory->save();


        $qty=$inventory->bought-$inventory->sold;
        \App\Traits\Chat::notify($qty,$inventory->product,$inventory->color,$inventory->size);


        return $new_sold;

    }


}



if (!function_exists('update_bought_inventory')) {

    function update_bought_inventory($product, $color, $size, $qty)
    {

        $product_info = Product::findorfail($product);

        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'sold' => 0,
                'bought' => 0,
            ]);
        }

        $old_bought=$inventory->bought;
        $new_bought=$old_bought+$qty;


        $inventory->bought=$new_bought;
        $inventory->save();


        $qty=$inventory->bought-$inventory->sold;
        \App\Traits\Chat::notify($qty,$inventory->product,$inventory->color,$inventory->size);


        return $new_bought;

    }


}


if (!function_exists('qty_sold_inventory')) {

    function qty_sold_inventory($product, $color, $size)
    {

        $product_info = Product::findorfail($product);

        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'sold' => 0,
                'bought' => 0,
            ]);
        }
        if ($inventory->open == 1){
            $qty = 'open';
        }else{
            $qty=$inventory->bought-$inventory->sold;
        }

        return $qty;

    }


}



if (!function_exists('update_sold_inventory_after_update')) {

    function update_sold_inventory_after_update($product, $color, $size, $qty, $status)
    {
        $product_info = Product::findorfail($product);

        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'sold' => 0,
                'bought' => 0,
            ]);
        }
        $old_sold = $inventory->sold;
        $new_sold = $old_sold;

        if ($qty>0)
        {

            if ($status->is_counted==1){
                $new_sold=$old_sold-$qty;
            }

        }
        elseif ($qty<0){

            if ($status->id==6){
                $new_sold=$old_sold-$qty;

            }

        }
        else {}


        $inventory->sold=$new_sold;
        $inventory->save();

        $qty=$inventory->bought-$inventory->sold;
        \App\Traits\Chat::notify($qty,$inventory->product,$inventory->color,$inventory->size);


        return $new_sold;


    }


}




if (!function_exists('update_ruined')) {

    function update_ruined($product, $color, $size, $qty)
    {
        $product_info = Product::findorfail($product);

        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'sold' => 0,
                'bought' => 0,
            ]);
        }
        $old_sold = $inventory->sold;
        $new_sold = $old_sold+$qty;





        $inventory->sold=$new_sold;
        $inventory->save();

        $qty=$inventory->bought-$inventory->sold;
        \App\Traits\Chat::notify($qty,$inventory->product,$inventory->color,$inventory->size);


        return $new_sold;


    }


}


function order_items($start = 0, $limit = 50)
{

}

?>