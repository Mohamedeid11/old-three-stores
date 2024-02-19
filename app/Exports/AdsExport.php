<?php

namespace App\Exports;

use App\Ad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class AdsExport implements FromCollection, WithHeadings
{
    public function __construct()
    {
    }

    public function collection()
    {
        // Fetch users data based on the received $roomId
        $ads =Ad::select('id','platform_id','date','ad_number','result','cost_per_result','status')->get();
        return $ads;
    }

    public function headings(): array
    {
        // Define column headers
        return [
            'id','platform_id','date','ad_number','result','cost_per_result','status','products'
            // Add more column names as needed
        ];
    }
}
