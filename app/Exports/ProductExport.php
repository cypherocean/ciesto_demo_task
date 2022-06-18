<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromCollection, WithHeadings,WithStyles ,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::select('shop.name AS shop_name' ,'products.name' ,'products.price' ,'products.stock' ,'products.status')->leftjoin('shop' ,'products.shop_id' ,'shop.id')->get();
    }

    public function headings() :array{
        return ["Shop Name" , "Name" , "Price" ,"Stock" ,"Status"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
