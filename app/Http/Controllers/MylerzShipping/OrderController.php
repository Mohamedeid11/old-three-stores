<?php

namespace App\Http\Controllers\MylerzShipping;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SellOrder;

class OrderController extends Controller
{
    public function index($order)
    {
        $order_info = SellOrder::findorfail($order);
        if($order_info->mylerz_barcode != '') {return true;}
        $cc = TimeLine::where('order', $order_info->id)->where('order_type', 3)->first();
        sleep(1);
        $order_info = SellOrder::findorfail($order);
        if($order_info->mylerz_barcode != '') {return true;}
        $cc = TimeLine::where('order', $order_info->id)->where('order_type', 3)->first();
        if($cc === NULL)
        {
            $order_status_deliver = NULL;
            $has_deliver = false;
            $has_return = false;
            $total_deliver = 0;
            foreach ($order_info->itemsq as $item)
            {
                if($item->qty > 0)
                {
                    $has_deliver = true;
                    $total_deliver = $total_deliver + ($item->qty * $item->price);
                }
                else
                {
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
            if(substr($order_info->order_number, 0, 2) === "Re")
            {
                $service_category = "RETURN";
                $Payment_Type = "PP";
            }
            else
            {
                $service_category = "Delivery";
                if($total > 0)
                {
                    $Payment_Type = "COD";                
                }
                else
                {
                    $Payment_Type = "PP";
                }
            }
            if($has_deliver && $total > 0)
            {
                $service_category = "Delivery";
                $Payment_Type = "COD";
                $payloads = '[
                    {
                        "WarehouseName":"Three Stores",
                        "PickupDueDate": "'.date('Y-m-d').'T'.date('H:i:s').'",
                        "Package_Serial": "'.$order_info->id.'",
                        "Reference": "'.$order_info->order_number.'",
                        "Description": "'.$order_info->note.'",
                        "Total_Weight": 0,
                        "Service_Type": "CTD",
                        "Service": "SD",
                        "ServiceDate":"'.date('Y-m-d').'T'.date('H:i:s').'",
                        "Service_Category": "'.$service_category.'",
                        "Payment_Type": "'.$Payment_Type.'",
                        "COD_Value": "'.$total.'",
                        "Customer_Name": "'.optional($order_info->client_info)->name.'",
                        "Mobile_No": "'.optional($order_info->client_info)->phone.'",
                        "Building_No": "",
                        "Street": "'.$order_info->address.'",
                        "Floor_No": "",
                        "Apartment_No": "",
                        "Country": "Egypt",
                        "City": "'.optional($order_info->city_info)->mylerz_neighborhood.'",
                        "Neighborhood": "'.optional($order_info->city_info)->mylerz_district.'",
                        "District": "",
                        "GeoLocation": "",
                        "Address_Category": "H",
                        "CustVal": "",
                        "Currency": "",
                        "Pieces": ['.$pieces.']
                    }
                ]';
            
                $order_info = SellOrder::findorfail($order);
                if($order_info->mylerz_barcode != '') {return true;}
        
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
                CURLOPT_POSTFIELDS =>$payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$access_token
                ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                $response = json_decode($response, true);
                if(array_key_exists('Value', $response))
                {
                    if(array_key_exists('Packages', $response['Value']))
                    {
                        if(count($response['Value']['Packages']) > 0)
                        {
                            if(array_key_exists('BarCode', $response['Value']['Packages'][0]))
                            {
                                $order_info->mylerz_barcode = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->shipping_number = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->save();
                            }
            
                        }
                    }
                }    
            }
            else if($has_deliver && $total <= 0)
            {
                $service_category = "Delivery";
                $Payment_Type = "PP";
                $payloads = '[
                {
                    "WarehouseName":"Three Stores",
                    "PickupDueDate": "'.date('Y-m-d').'T'.date('H:i:s').'",
                    "Package_Serial": "'.$order_info->id.'",
                    "Reference": "'.$order_info->order_number.'",
                    "Description": "'.$order_info->note.'",
                    "Total_Weight": 0,
                    "Service_Type": "CTD",
                    "Service": "SD",
                    "ServiceDate":"'.date('Y-m-d').'T'.date('H:i:s').'",
                    "Service_Category": "'.$service_category.'",
                    "Payment_Type": "'.$Payment_Type.'",
                    "COD_Value": "0",
                    "Customer_Name": "'.optional($order_info->client_info)->name.'",
                    "Mobile_No": "'.optional($order_info->client_info)->phone.'",
                    "Building_No": "",
                    "Street": "'.$order_info->address.'",
                    "Floor_No": "",
                    "Apartment_No": "",
                    "Country": "Egypt",
                    "City": "'.optional($order_info->city_info)->mylerz_neighborhood.'",
                    "Neighborhood": "'.optional($order_info->city_info)->mylerz_district.'",
                    "District": "",
                    "GeoLocation": "",
                    "Address_Category": "H",
                    "CustVal": "",
                    "Currency": "",
                    "Pieces": ['.$pieces.']
                }
                ]';
        
                $order_info = SellOrder::findorfail($order);
                if($order_info->mylerz_barcode != '') {return true;}
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
                CURLOPT_POSTFIELDS =>$payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$access_token
                ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                $response = json_decode($response, true);
                if(array_key_exists('Value', $response))
                {
                    if(array_key_exists('Packages', $response['Value']))
                    {
                        if(count($response['Value']['Packages']) > 0)
                        {
                            if(array_key_exists('BarCode', $response['Value']['Packages'][0]))
                            {
                                $order_info->mylerz_barcode = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->shipping_number = $response['Value']['Packages'][0]['BarCode'];
                                $order_info->save();
                            }
            
                        }
                    }
                }    
            }
            if($has_return)
            {
                $service_category = "RETURN";
                $Payment_Type = "PP";
                $payloads = '[
                    {
                        "WarehouseName":"Three Stores",
                        "PickupDueDate": "'.date('Y-m-d').'T'.date('H:i:s').'",
                        "Package_Serial": "'.$order_info->id.'",
                        "Reference": "'.$order_info->order_number.'",
                        "Description": "'.$order_info->note.'",
                        "Total_Weight": 0,
                        "Service_Type": "DTC",
                        "Service": "SD",
                        "ServiceDate":"'.date('Y-m-d').'T'.date('H:i:s').'",
                        "Service_Category": "'.$service_category.'",
                        "Payment_Type": "'.$Payment_Type.'",
                        "COD_Value": "0",
                        "Customer_Name": "'.optional($order_info->client_info)->name.'",
                        "Mobile_No": "'.optional($order_info->client_info)->phone.'",
                        "Building_No": "",
                        "Street": "'.$order_info->address.'",
                        "Floor_No": "",
                        "Apartment_No": "",
                        "Country": "Egypt",
                        "City": "'.optional($order_info->city_info)->mylerz_neighborhood.'",
                        "Neighborhood": "'.optional($order_info->city_info)->mylerz_district.'",
                        "District": "",
                        "GeoLocation": "",
                        "Address_Category": "H",
                        "CustVal": "",
                        "Currency": "",
                        "Pieces": ['.$pieces.']
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
                CURLOPT_POSTFIELDS =>$payloads,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$access_token
                ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                $response = json_decode($response, true);
                if(!$has_deliver)
                {
                    if(array_key_exists('Value', $response))
                    {
                        if(array_key_exists('Packages', $response['Value']))
                        {
                            if(count($response['Value']['Packages']) > 0)
                            {
                                if(array_key_exists('BarCode', $response['Value']['Packages'][0]))
                                {
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
        else
        {
            dd('1');
        }
    }

    public function get_awb($order)
    {
        get_mylerz_awb ($order);
    }
}
