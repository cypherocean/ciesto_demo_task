<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Shop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductImport implements ToModel ,WithStartRow
{
      /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $shop = Shop::select('id')->where(['name' => $row[0]])->first();
        if(!empty($shop)){
            return Product::create([
                'shop_id' => $shop->id,
                'name' => $row[1],
                'price' => $row[2] ?? NULL,
                'stock' => $row[3] ?? NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => auth()->user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id
            ]);
        }else{
            return null;
        }
    }
}
