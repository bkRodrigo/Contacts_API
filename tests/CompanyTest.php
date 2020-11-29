<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Address;
use App\Models\PostalCode;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class CompanyTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * /address [GET]
     */
    public function testShouldReturnAllAddresses()
    {
        $this->createAddresses(5);
        $res = $this->json(
            'GET',
            '/address',
            []
        );
        $jsonRes = json_decode($res->response->content());

        $this->assertEquals(5, $jsonRes->meta->total);
        $this->assertEquals(5, Address::count());
    }

    /**
     * /address/{id} [GET]
     */
    public function testShouldReturnAnAddresses()
    {
        $address = $this->createAddresses(2)->last();
        $res = $this->json(
            'GET',
            '/address/' . $address->id,
            []
        )->seeJson([
            'id' => $address->id,
            'street_address' => $address->street_address,
        ]);
    }

    /**
     * /address [POST]
     */
    public function testShouldCreateAnAddress()
    {
        $addresses = $this->makeAddresses(2);
        $res = $this->json(
            'POST',
            '/address',
            [ 'street_address' => $addresses->first()->street_address ],
        );

        $res->seeJson([
            'message' => 'The address was successfully created',
        ]);

        $res = $this->json(
            'POST',
            '/address',
            [
                'street_address' => $addresses->last()->street_address,
                'latitude' => $addresses->last()->latitude,
                'longitude' => $addresses->last()->longitude,
                'postal_code' => $addresses->last()->postalCode->code,
                'city' => $addresses->last()->city->name,
                'state' => $addresses->last()->state->name,
                'country' => $addresses->last()->country->name,
            ],
        );

        $res->seeJson([
            'message' => 'The address was successfully created',
        ]);

        $this->assertEquals(2, Address::count());
    }

    /**
     * /address/{id} [DELETE]
     */
    public function testShouldDeleteAnAddresses()
    {
        $address = $this->createAddresses(1)->first();

        $res = $this->json(
            'DELETE',
            '/address/' . $address->id,
            []
        )->seeJson([
            'message' => 'The address was successfully deleted',
        ]);

        $this->assertEquals(0, Address::count());
    }

    private function makeAddresses(int $count)
    {
        $addresses = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $postalCode = PostalCode::factory()->make();
            $city = City::factory()->make();
            $state = State::factory()->make();
            $country = Country::factory()->make();
            $address = Address::factory()->make();
            $address->postalCode()->associate($postalCode);
            $address->city()->associate($city);
            $address->state()->associate($state);
            $address->country()->associate($country);

            $addresses->add($address);
        }

        return $addresses;
    }

    private function createAddresses(int $count)
    {
        $addresses = collect([]);
        for ($i = 0; $i < $count; $i++) {
            $postalCode = PostalCode::factory()->create();
            $city = City::factory()->create();
            $state = State::factory()->create();
            $country = Country::factory()->create();
            $address = Address::factory()->make();
            $address->postalCode()->associate($postalCode);
            $address->city()->associate($city);
            $address->state()->associate($state);
            $address->country()->associate($country);
            $address->save();
            $addresses->add($address);
        }

        return $addresses;
    }
}
