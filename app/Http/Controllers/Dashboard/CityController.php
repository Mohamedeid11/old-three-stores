<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\City;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::where('hide', '=', 0)->get();
        return view('admin.pages.cities.index')->with(['cities'=>$cities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.mylerz.net/api/Packages/GetCityZoneList',
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

        $neighborhoods = array();

        foreach ($response['Value'] as $neighborhood)
        {
            $neighborhoods[] = array("Code"=>$neighborhood['Code'], "ArName"=>$neighborhood['ArName'], "EnName"=>$neighborhood['EnName']);
        }

        return view('admin.pages.cities.create', compact('neighborhoods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'shipment'=>'required|numeric|min:0'
        ],
        [
            'title.required'=>'Please Enter City Name',
            'shipment.required'=>'Please Enter Shipment Value To This City',
            'shipment.numeric'=>'Shipment Value Must Be Number',
            'shipment.min'=>'Shipment Value Must Be Zero Or More',
        ]);
        $city = new City;
        $city->title  = $request->title;
        $city->shipment  = $request->shipment;
        if ($request->has('mylerz_shipping')) {$city->mylerz_shipping  = 1;}
        else {$city->mylerz_shipping = 0;}
        $city->mylerz_neighborhood  = $request->mylerz_neighborhood;
        $city->mylerz_district  = $request->mylerz_district;
        $city->save();   
        return redirect()->route('cities.index'); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {        
        $city = City::findorfail($id);
        if($city->hide == 0)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://integration.mylerz.net/api/Packages/GetCityZoneList',
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

            $neighborhoods = array();
            $zones = array();

            foreach ($response['Value'] as $neighborhood)
            {
                $neighborhoods[] = array("Code"=>$neighborhood['Code'], "ArName"=>$neighborhood['ArName'], "EnName"=>$neighborhood['EnName']);
                if($neighborhood['Code'] == $city->mylerz_neighborhood)
                {
                    foreach ($neighborhood['Zones'] as $zone)
                    {
                        $zones[] = array("Code"=>$zone['Code'], "ArName"=>$zone['ArName'], "EnName"=>$zone['EnName']);
                    }
                }

            }

            return view('admin.pages.cities.edit')->with(['city'=>$city, 'neighborhoods'=>$neighborhoods, 'zones'=>$zones]);    
        }
        else
        {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $city = City::findorfail($id);
        if($city->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
                'shipment'=>'required|numeric|min:0'
            ],
            [
                'title.required'=>'Please Enter City Name',
                'shipment.required'=>'Please Enter Shipment Value To This City',
                'shipment.numeric'=>'Shipment Value Must Be Number',
                'shipment.min'=>'Shipment Value Must Be Zero Or More',
            ]);
            $city->title  = $request->title;
            $city->shipment  = $request->shipment;
            if ($request->has('mylerz_shipping')) {$city->mylerz_shipping  = 1;}
            else {$city->mylerz_shipping = 0;}
            $city->mylerz_neighborhood  = $request->mylerz_neighborhood;
            $city->mylerz_district  = $request->mylerz_district;    
            $city->save();   
            return redirect()->route('cities.index');  
        }
        else
        {
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findorfail($id);
        $city->hide = 1;
        $city->save();
        return redirect()->route('cities.index'); 
    }
    
    public function shipping_price_info (Request $request)
    {
        $city = City::findorfail($request->city);
        return $city->shipment;
    }

    public function city_zones  (Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.mylerz.net/api/Packages/GetCityZoneList',
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

        $zones = array();
        foreach ($response['Value'] as $neighborhood)
        {
            if($neighborhood['Code'] == $request->city)
            {
                foreach ($neighborhood['Zones'] as $zone)
                {
                    $zones[] = array("Code"=>$zone['Code'], "ArName"=>$zone['ArName'], "EnName"=>$zone['EnName']);
                }
                break;
            }
        }
        return view('admin.pages.cities.zones', compact(['zones']))->render();
    }
}
