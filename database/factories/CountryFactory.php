<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->generateCountryName(),
        ];
    }

    /**
     * Generate a unique country name.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generateCountryName(int $attempts = 0)
    {
        $countryName = $this->faker->unique($attempts === 0)->country;
        $countryName .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(Country::where('name', $countryName)->first())) {
            return $countryName;
        }

        return $this->generateCountryName($attempts + 1);
    }
}
