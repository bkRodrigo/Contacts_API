<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->generateCompanyName(),
        ];
    }

    /**
     * Generate a unique company name.
     *
     * @param int $attempts number of attempts to generate a result
     *
     * @return string
     */
    private function generateCompanyName(int $attempts = 0)
    {
        $companyName = $this->faker->unique($attempts === 0)->company;
        $companyName .= $attempts > 10 ? '_(' . strval($attempts) . ')' : '';
        if (is_null(Company::where('name', $companyName)->first())) {
            return $companyName;
        }

        return $this->generateCompanyName($attempts + 1);
    }
}
