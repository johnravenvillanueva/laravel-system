<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Regular registered customers
        Customer::create([
            'email'         => 'dan@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Dan',
            'last_name'     => 'Tan',
            'phone'         => '0917 444 4444',
            'address'       => 'Pinaod, San Ildefonso, Bulacan',
            'is_guest'      => false,
            'is_active'     => true,
        ]);

        Customer::create([
            'email'         => 'ella@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Ella',
            'last_name'     => 'Santos',
            'phone'         => '0917 555 5555',
            'address'       => 'Poblacion, San Ildefonso, Bulacan',
            'is_guest'      => false,
            'is_active'     => true,
        ]);

        Customer::create([
            'email'         => 'frank@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Frank',
            'last_name'     => 'Ramos',
            'phone'         => '0917 666 6666',
            'address'       => 'Sto. Cristo, San Ildefonso, Bulacan',
            'is_guest'      => false,
            'is_active'     => true,
        ]);

        // Inactive customer (for testing deactivation)
        Customer::create([
            'email'         => 'inactive@issang.test',
            'password_hash' => Hash::make('password'),
            'first_name'    => 'Inactive',
            'last_name'     => 'User',
            'phone'         => '0917 777 7777',
            'is_guest'      => false,
            'is_active'     => false,
        ]);

        // Guest customer (no email, no password — created at guest checkout)
        Customer::create([
            'email'         => null,
            'password_hash' => null,
            'first_name'    => 'Guest',
            'last_name'     => 'Walker',
            'phone'         => '0917 888 8888',
            'is_guest'      => true,
            'is_active'     => true,
        ]);
    }
}