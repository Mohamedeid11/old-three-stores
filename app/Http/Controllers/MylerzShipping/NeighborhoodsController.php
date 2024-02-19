<?php

namespace App\Http\Controllers\MylerzShipping;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NeighborhoodsController extends Controller
{
    public function index (Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://41.33.122.61:8888/MylerzIntegrationStaging/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=threetechstores&password=5gMi9a5M%241%5DC1&grant_type=password',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);

        $access_token = $response['access_token'];
        echo '<hr />';
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://41.33.122.61:8888/MylerzIntegrationStaging/api/Packages/GetCityZoneList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response, true);

        // $neighborhoods = array();
        // foreach ($response['Value'] as $neighborhood)
        // {
        //     $neighborhoods[] = array("Code"=>$neighborhood['Code'], "ArName"=>$neighborhood['ArName'], "EnName"=>$neighborhood['EnName']);
        // }
        $zones = array();
        foreach ($response['Value'] as $neighborhood)
        {
            if($neighborhood['Code'] == 'CA')
            {
                foreach ($neighborhood['Zones'] as $zone)
                {
                    $zones[] = array("Code"=>$zone['Code'], "ArName"=>$zone['ArName'], "EnName"=>$zone['EnName']);
                }
                break;
            }
        }
        dd($zones);
    }
}
