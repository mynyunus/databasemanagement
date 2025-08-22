<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Create customers with realistic Malaysian data
        Customer::factory(50)->create();

        // Create some specific test customers
        $testCustomers = [
            [
                'name' => 'Ahmad Ibrahim',
                'phone_raw' => '0123456789',
                'phone_e164' => '+60123456789',
                'email' => 'ahmad@gmail.com',
                'address_line1' => 'No. 123, Jalan Bukit Bintang',
                'city' => 'Kuala Lumpur',
                'postcode' => '50200',
                'state' => 'W.P. Kuala Lumpur',
            ],
            [
                'name' => 'Siti Aminah',
                'phone_raw' => '0198765432',
                'phone_e164' => '+60198765432',
                'email' => 'siti@hotmail.com',
                'address_line1' => 'Lot 456, Taman Melaka Raya',
                'city' => 'Melaka',
                'postcode' => '75000',
                'state' => 'Melaka',
            ],
        ];

        foreach ($testCustomers as $customer) {
            Customer::create($customer);
        }
    }
}