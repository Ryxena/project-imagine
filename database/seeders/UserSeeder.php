<?php

namespace Database\Seeders;

use App\Models\Penghunian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
            {
                // 3 data user biasa
        $users = User::insert([
            [
                'image' => null,
                'name' => 'User 1',
                'email' => 'user1@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'aktif',
                'no_hp' => '081234567801',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'name' => 'User 2',
                'email' => 'user2@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'aktif',
                'no_hp' => '081234567802',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'name' => 'User 3',
                'email' => 'user3@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'aktif',
                'no_hp' => '081234567803',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Ambil 3 user biasa
        $users = User::whereIn('email', [
            'user1@gmail.com',
            'user2@gmail.com',
            'user3@gmail.com',
        ])->get();

        // Masukkan user biasa ke penghunian
        foreach ($users as $user) {
            Penghunian::create([
                'user_id' => $user->id,
            ]);
        }


        // 3 akun super admin
        User::insert([
            [
                'image' => null,
                'name' => 'Super Admin 1',
                'email' => 'admin1@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'aktif',
                'no_hp' => '081234567811',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'name' => 'Super Admin 2',
                'email' => 'admin2@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'aktif',
                'no_hp' => '081234567812',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => null,
                'name' => 'Super Admin 3',
                'email' => 'admin3@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'aktif',
                'no_hp' => '081234567813',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
