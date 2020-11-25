<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'avatar' => $this->faker->image('public/images', 400, 400, null, false),
            'email' => $this->faker->email,
            'birthday' => $this->faker->date(),
            'company_id' => function () {
                if (Company::all()->count() === 0) {
                    $company = Company::factory()->create();
                } else {
                    $company = Company::all()->random();
                }
                return $company->id;
            },
            'address_id' => function () {
                if (Address::all()->count() === 0) {
                    $address = Address::factory()->create();
                } else {
                    $address = Address::all()->random();
                }
                return $address->id;
            },
        ];
    }
}
