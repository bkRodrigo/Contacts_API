<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->json(
            'POST',
            '/address',
            ['street_address' => '63 Whipple Ave.']
        )->seeJson([
            'message' => 'The address was successfully created',
        ]);
        \App\Models\Address::count();
        $this->assertEquals(1, \App\Models\Address::count());
    }
}
