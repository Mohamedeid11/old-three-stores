<?php

namespace App\Imports;

use App\DetailsFile;
use App\Inventory;
use App\Product;
use App\TagGroup;
use App\productTag;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryImport implements ToModel, WithHeadingRow
{
    private $file_gard_id;

    public function __construct($file_gard_id)
    {
        $this->file_gard_id = $file_gard_id;
    }

    public function model(array $row)
    {
        $inventory = null;
        if (isset($row['id'])) {
            $inventory = Inventory::find($row['id']);
        }

        if ($inventory) {
            $last_cost = $inventory->last_cost;
            $sold = $inventory->sold;

            if (isset($row['qty'])) {

                $old_sold = $inventory->sold;
                $old_bought = $inventory->bought;
                $new_sum = $old_bought - $row['qty'] - $old_sold;
                $sold = $old_sold + $new_sum;

            }
            if (isset($row['last_cost'])) {
                if ($row['last_cost'] > 5) {
                    $last_cost = $row['last_cost'];
                }
            }


            $inventory->sold = $sold;
            $inventory->last_cost = $last_cost;
            $inventory->save();

            if (isset($row['qty'])) {

                if ($new_sum != 0) {
                    DetailsFile::create([
                        'inventory_id' => $inventory->id,
                        'file_gard_id' => $this->file_gard_id,
                        'price' => $inventory->last_cost,
                        'qty' => $new_sum,
                    ]);
                }

            }

            if (isset($row['tags'])) {

                $tags_array = explode(',', $row['tags']);

                $product = Product::find($inventory->product);
                if ($product) {
                    productTag::where('product_id', $product->id)->delete();
                    foreach ($tags_array as $tag_title) {

                        $tag = TagGroup::where('title', '=', trim($tag_title))->first();
                        if ($tag) {
                            productTag::create([
                                'product_id' => $product->id,
                                'tag_id' => $tag->id,
                            ]);
                        }

                    }
                }

            }


        }


        return null; // Return null to skip adding a new model to the collection
    }
}
