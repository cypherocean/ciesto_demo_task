<?php

namespace App\Exports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShopExport implements FromCollection, WithHeadings,WithStyles ,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Shop::select('name' ,'address' ,'email' ,'status')->get();
    }

    public function headings() :array{
        return ["Name" , "Address" ,"Email" ,"Status"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
