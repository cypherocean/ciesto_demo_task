<?php

    namespace Database\Seeders;
    use App\Models\User;

    use Illuminate\Database\Seeder;

    class UserSeeder extends Seeder{

        public function run(){
            User::create([
                'name' => 'Super Admin',
                'phone' => '1234567890',
                'email' => 'superadmin@mail.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => 'y',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => 1
            ]);
        }
    }
