<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    private $malaysianStates = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 
        'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 
        'Sarawak', 'Selangor', 'Terengganu', 'W.P. Kuala Lumpur', 
        'W.P. Labuan', 'W.P. Putrajaya'
    ];

    public function definition(): array
    {
        // Generate Malaysian phone number
        $phoneRaw = $this->faker->randomElement([
            '01' . $this->faker->numerify('########'),  // Mobile
            '03' . $this->faker->numerify('#######'),   // KL/Selangor
            '04' . $this->faker->numerify('#######'),   // Penang
        ]);
        
        $phoneE164 = '+6' . $phoneRaw;

        return [
            'name' => $this->faker->name(),
            'phone_raw' => $phoneRaw,
            'phone_e164' => $phoneE164,
            'email' => $this->faker->optional(0.7)->safeEmail(),
            'address_line1' => $this->faker->optional(0.8)->streetAddress(),
            'address_line2' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $this->faker->optional(0.8)->city(),
            'postcode' => $this->faker->optional(0.8)->numerify('#####'),
            'state' => $this->faker->optional(0.8)->randomElement($this->malaysianStates),
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}