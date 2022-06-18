<?php

namespace App\Imports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ShopImport implements ToModel ,WithStartRow
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
        return Shop::create([
            'name' => $row[1],
            'image' => $row[4],
            'address' => $row[3],
            'email'    => $row[2],
            'status' => 'active',
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
