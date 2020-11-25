<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->generateCityName(),
        ];
    }

    /**
     * Generate a unique city name.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generateCityName(int $attempts = 0)
    {
        $cityName = $this->faker->unique($attempts === 0)->city;
        $cityName .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(City::where('name', $cityName)->first())) {
            return $cityName;
        }

        return $this->generateCityName($attempts + 1);
    }
}
