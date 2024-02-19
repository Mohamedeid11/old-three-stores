<?php

namespace App\Imports;

use App\Ad;
use App\AdPlatform;
use App\AdProduct;
use App\OrderTag;
use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;


class AdsImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
    }
    public function model(array $row)
    {
        $ad=null; $status=1;
        if (isset($row['status'])){
            if ($row['status']=='inactive'){
                $status=0;
            }
        }
        $ad=Ad::where('date',$row['date'])->where('ad_number',$row['ad_number'])->first();

        // If a matching user is found, update the existing record; otherwise, create a new one
        if (!$ad){

            $ad=Ad::create([
                'date'=>$row['date'],
                'ad_number'=>$row['ad_number']??'',
                'result'=>$row['result']??0,
                'cost_per_result'=>$row['cost_per_result']??0,
                'status'=>$status,
                'parent_id'=>null,
            ]);
            if (isset($row['products']) ) {

                $array = explode('-', $row['products']);

                foreach ($array as $item) {

                    $product = Product::where('title', 'like', '%' . trim($item) . '%')->first();

                    if ($product){
                        AdProduct::create([
                            'ad_id'=>$ad->id,
                            'product_id'=>$product->id,
                        ]);
                    }

                }

                $parent = Ad::where('id', '!=', $ad->id)
                    ->where('date', $ad->date)
                    ->where('parent_id',null)
                    ->first();

                if ($parent) {
                    // Get the product IDs of the current ad
                    $adProductIds = $ad->products->pluck('id')->toArray();

                    // Get the product IDs of the parent ad
                    $parentProductIds = $parent->products->pluck('id')->toArray();

                    // Check if the product IDs are the same
                    if (count($adProductIds) == count($parentProductIds) && empty(array_diff($adProductIds, $parentProductIds))) {
                       $ad->parent_id=$parent->id;
                       $ad->save();
                    }
                }


            }

            if (isset($row['platform_id']) ) {

                $platformArray = explode('-', $row['platform_id']);

                foreach ($platformArray as $platform) {



                    AdPlatform::create([
                        'ad_id'=>$ad->id,
                        'platform_id'=>$platform,
                    ]);


                }

            }




        }
        else {

            $ad->update([
                'date'=>$row['date'],
                'ad_number'=>$row['ad_number']??0,
                'result'=>$row['result']??0,
                'cost_per_result'=>$row['cost_per_result']??0,
                'status'=>$status,
            ]);

            $ad=Ad::where('date',$row['date'])->where('ad_number',$row['ad_number'])->first();


            if (isset($row['products']) ) {
                AdProduct::where('ad_id',$ad->id)->delete();
                $array = explode('-', $row['products']);

                foreach ($array as $item) {

                    $product = Product::where('title', 'like', '%' . trim($item) . '%')->first();

                    if ($product){
                        AdProduct::create([
                            'ad_id'=>$ad->id,
                            'product_id'=>$product->id,
                        ]);
                    }

                }

            }


            $parent = Ad::where('id', '!=', $ad->id)
                ->where('date', $ad->date)
                ->where('parent_id',null)

                ->first();

            if ($parent) {
                // Get the product IDs of the current ad
                $adProductIds = $ad->products->pluck('id')->toArray();

                // Get the product IDs of the parent ad
                $parentProductIds = $parent->products->pluck('id')->toArray();

                // Check if the product IDs are the same
                if (count($adProductIds) == count($parentProductIds) && empty(array_diff($adProductIds, $parentProductIds))) {
                    $ad->parent_id=$parent->id;
                    $ad->save();
                }
            }



            if (isset($row['platform_id']) ) {

                AdPlatform::where('ad_id',$ad->id)->delete();

                $platformArray = explode('-', $row['platform_id']);

                foreach ($platformArray as $platform) {



                    AdPlatform::create([
                        'ad_id'=>$ad->id,
                        'platform_id'=>$platform,
                    ]);


                }

            }



        }
        return null; // Return null to skip adding a new model to the collection
    }
}
