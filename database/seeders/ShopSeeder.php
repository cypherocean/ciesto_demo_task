<?php

    namespace Database\Seeders;
    use App\Models\Shop;

    use Illuminate\Database\Seeder;

    class ShopSeeder extends Seeder{
        public function run(){
            for($i = 0; $i < 100 ; $i++){
                Shop::create([
                    'name' => 'Shop - '.$i,
                    'image' => 'default.png',
                    'address' => 'address - '.$i,
                    'email' => 'email'.$i.'@mail.com',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 1
                ]);        

            }
        }
    }
