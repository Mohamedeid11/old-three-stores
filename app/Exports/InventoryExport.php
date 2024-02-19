<?php

namespace App\Exports;

use App\Inventory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    private $tag_id;
    private $product_id;
    public function __construct($data)
    {
        $this->tag_id=$data['tag_id'];
        $this->product_id=$data['product_id'];

    }


    public function collection()
    {

        $query = Inventory::select('id', 'product', 'color', 'size')
            ->addSelect(DB::raw('bought - sold as qty'))
            ->where('hide',0)
            ->with(['product_info.tags']);

        // Add conditions for product_id and tag_id if they are not null/empty
        if (!empty($this->product_id)) {
            $query->whereIn('product', $this->product_id);
        }

        if (!empty($this->tag_id)) {

            $tagIds = array_map('intval', $this->tag_id);

            $query->whereHas('product_info', function ($query) use ($tagIds) {
                $query->whereHas('tags', function ($query) use ($tagIds) {
                    $query->whereIn('tag_groups.id', $tagIds);
                });
            });
        }

        return $query->get()->map(function ($inventory) {
            $tags=[];
            $tags_title='';

                $tags_ids= \App\productTag::where('product_id', optional($inventory->product_info)->id)->pluck('tag_id')->toArray();
                $tags=\App\TagGroup::whereIn('id',$tags_ids)->get();



//            if (is_array($tags)) {

                foreach ($tags ?? [] as $tag) {
                    $tags_title = $tags_title . ',' . $tag->title;
                }
//            }


            return [
                'id' => $inventory->id,
                'product' => optional($inventory->product_info)->title,
                'color' => optional($inventory->color_info)->title,
                'size' => optional($inventory->size_info)->title,
                'qty' => $inventory->qty,
                'tags' => $tags_title,
            ];
        });
    }
    public function headings(): array
    {
        // Define column headers
        return [
            'id',
            'Product',
            'Color',
            'Size',
            'qty',
            'tags',
        ];
    }
}
