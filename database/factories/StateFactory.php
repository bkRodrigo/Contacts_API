<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = State::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->generateStateName(),
        ];
    }

    /**
     * Generate a unique State name.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generateStateName(int $attempts = 0)
    {
        $stateName = $this->faker->unique($attempts === 0)->state;
        $stateName .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(State::where('name', $stateName)->first())) {
            return $stateName;
        }

        return $this->generateStateName($attempts + 1);
    }
}
