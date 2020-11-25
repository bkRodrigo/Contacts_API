<?php

namespace Database\Factories;

use App\Models\PostalCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostalCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostalCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->generatePostalCode(),
        ];
    }

    /**
     * Generate a unique postal code.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generatePostalCode(int $attempts = 0)
    {
        $postalCode = $this->faker->unique($attempts === 0)->postcode;
        $postalCode .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(PostalCode::where('code', $postalCode)->first())) {
            return $postalCode;
        }

        return $this->generatePostalCode($attempts + 1);
    }
}
