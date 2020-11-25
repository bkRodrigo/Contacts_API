<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\PostalCode;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'street_address' => $this->faker->streetAddress,
            'description' => $this->faker->optional()->text(150),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'postalcode_id' => function () {
                if (PostalCode::all()->count() === 0) {
                    $postalCode = PostalCode::factory()->create();
                } else {
                    $postalCode = PostalCode::all()->random();
                }
                return $postalCode->id;
            },
            'city_id' => function () {
                if (City::all()->count() === 0) {
                    $city = City::factory()->create();
                } else {
                    $city = City::all()->random();
                }
                return $city->id;
            },
            'state_id' => function () {
                if (State::all()->count() === 0) {
                    $state = State::factory()->create();
                } else {
                    $state = State::all()->random();
                }
                return $state->id;
            },
            'country_id' => function () {
                if (Country::all()->count() === 0) {
                    $country = Country::factory()->create();
                } else {
                    $country = Country::all()->random();
                }
                return $country->id;
            },
        ];
    }
}
