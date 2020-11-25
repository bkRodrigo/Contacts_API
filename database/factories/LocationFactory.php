<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->generateLocationName(),
        ];
    }

    /**
     * Generate a unique location name.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generateLocationName(int $attempts = 0)
    {
        $locationName = $this->faker->unique($attempts === 0)->word;
        $locationName .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(Location::where('name', $locationName)->first())) {
            return $locationName;
        }

        return $this->generateLocationName($attempts + 1);
    }
}
