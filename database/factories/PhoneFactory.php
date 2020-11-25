<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Phone::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->generatePhoneNumber(),
            'description' => $this->faker->optional()->text(80),
            'location_id' => function () {
                if (Location::all()->count() === 0) {
                    $location = Location::factory()->create();
                } else {
                    $location = Location::all()->random();
                }
                return $location->id;
            },
        ];
    }

    /**
     * Generate a unique phone number.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generatePhoneNumber(int $attempts = 0)
    {
        $phone = $this->faker->unique($attempts === 0)->e164PhoneNumber;
        $phone = substr($phone, 1);
        $phone .= $attempts > 10 ? strval($attempts) : '';
        if (is_null(Phone::where('number', $phone)->first())) {
            return $phone;
        }

        return $this->generatePhoneNumber($attempts + 1);
    }
}
