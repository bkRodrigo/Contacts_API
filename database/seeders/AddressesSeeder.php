<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\PostalCode;
use App\Models\State;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = rand(3000, 5000);
        echo 'Creating ' . $count . ' postal codes.' . PHP_EOL;
        $postalCodes = PostalCode::factory()->count($count)->make();
        foreach ($postalCodes as $postalCode) {
            PostalCode::firstOrCreate([
                'code' => $postalCode->code,
            ]);
        }
        $count = rand(1000, 2000);
        echo 'Creating ' . $count . ' cities.' . PHP_EOL;
        $cities = City::factory()->count($count)->make();
        foreach ($cities as $city) {
            City::firstOrCreate([
                'name' => $city->name,
            ]);
        }
        $count = rand(100, 300);
        echo 'Creating ' . $count . ' states.' . PHP_EOL;
        $states = State::factory()->count($count)->make();
        foreach ($states as $state) {
            State::firstOrCreate([
                'name' => $state->name,
            ]);
        }
        $count = rand(10, 30);
        echo 'Creating ' . $count . ' countries.' . PHP_EOL;
        $countries = Country::factory()->count($count)->make();
        foreach($countries as $country) {
            Country::firstOrCreate([
                'name' => $country->name,
            ]);
        }
        $i = 0;
        $totalPasses = 30;
        echo 'Creating Addresses' . PHP_EOL;
        $totalAddresses = 0;
        while($i < $totalPasses) {
            $i++;
            $count = rand(1, 50);
            echo '  Pass ' . $i . ' of ' . $totalPasses . PHP_EOL;
            echo '  - Creating ' . $count . ' addresses' . PHP_EOL;
            $totalAddresses += $count;
            $addresses = Address::factory()->count($count)->make();
            foreach ($addresses as $address) {
                $newAddress = new Address();
                $newAddress->street_address = $address->street_address;
                $newAddress->description = $address->description;
                $newAddress->latitude = $address->latitude;
                $newAddress->longitude = $address->longitude;
                // Generate associations
                $postalCode = PostalCode::where('id', $address->postalcode_id)->first();
                $city = City::where('id', $address->city_id)->first();
                $state = State::where('id', $address->state_id)->first();
                $country = Country::where('id', $address->country_id)->first();
                if (is_null($postalCode) ||
                    is_null($city) ||
                    is_null($state) ||
                    is_null($country)
                ) {
                    continue;
                }
                $newAddress->postalCode()->associate($postalCode);
                $newAddress->city()->associate($city);
                $newAddress->state()->associate($state);
                $newAddress->country()->associate($country);

                $newAddress->save();
            }
        }
        $doneMsg = 'Addresses seeding completed, a total of ' . $totalAddresses;
        $doneMsg .= ' addresses were created' . PHP_EOL;
        echo $doneMsg;
    }
}
