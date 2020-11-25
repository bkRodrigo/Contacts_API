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
        $imageNames = [
            '87a83a4a-ff97-4fd7-b5cb-3835fb82e2f7.jpg',
            '3a60792c-1006-4b67-8c38-053602ddfb49.jpg',
            'dec88854-a8a3-41de-a072-56c9b27f2b5e.jpg',
        ];
        $imageName = $imageNames[rand(0, 2)];

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'avatar' => $imageName,
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
