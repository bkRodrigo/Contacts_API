<?php

namespace App\Http\Controllers\Contact;

use App\Models\Address;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Location;
use App\Models\Phone;
use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait ContactTrait
{
    protected function updateContact(?Contact $contact, Request $request, string $successMsg) : JsonResponse
    {
        // We assume the values have already been validated
        if (is_null($contact)) {
            $contact = new $this->model();
        }
        // Update the contact
        $contact->first_name = $request->input('first_name');
        $contact->last_name = $request->input('last_name');
        $contact->email = $request->input('email', null);
        $birthday = null;
        if (!is_null($request->input('birthday', null))) {
            $birthday = Carbon::parse($request->input('birthday'));
        }
        $contact->birthday = $birthday;
        // Resolve company data
        $companyName = $request->input('company', null);
        if (!is_null($companyName) && strlen($companyName) > 3) {
            $contact->company()->associate(Company::firstOrCreate(
                [ 'name' => $companyName]
            ));
        }
        // Resolve address id
        $addressId = $request->input('address_id', null);
        $address = null;
        if (!is_null($addressId)) {
            $address = Address::where('id', $addressId)->first();
        }
        if (!is_null($address)) {
            $contact->address()->associate($address);
        }
        // Resolve photo id
        $photoId = $request->input('photo_id', null);
        $photo = null;
        if (!is_null($photoId)) {
            $photo = Photo::where('id', $photoId)->first();
        }
        if (!is_null($photo)) {
            $contact->photo()->associate($photo);
        }
        // Store the contact
        $contact->save();
        // Associate available phones to the new contact
        $phones = $this->testForPhoneNumbers($request->input('phones', null));
        foreach ($phones as $phone) {
            $contact->phones()->attach($phone);
        }

        return response()->json([
            'contact' => $contact,
            'message' => $successMsg,
        ]);
    }

    /**
     * Gets a list of phone numbers that were sent in the request, validates
     * that they're correctly formatted and finally creates or updates them.
     * The method will return an empty collection if there are no valid proposed
     * phones.
     *
     * @param ?array $proposedPhones
     *
     * @return Collection
     */
    private function testForPhoneNumbers(?array $proposedPhones) : Collection
    {
        $phones = collect([]);
        if (is_null($proposedPhones)) {
            return $phones;
        }
        foreach ($proposedPhones as $proposedPhone) {
            if (gettype($proposedPhone) !== 'array') {
                continue;
            }
            $number = '';
            $location = '';
            $description = '';
            if (array_key_exists('number', $proposedPhone)) {
                $number = $proposedPhone['number'];
            }
            if (array_key_exists('location', $proposedPhone)) {
                $location = $proposedPhone['location'];
            }
            if (array_key_exists('description', $proposedPhone)) {
                $description = $proposedPhone['description'];
            }
            $phone = $this->createPhone($number, $location, $description);
            if (!is_null($phone)) {
                $phones->add($phone);
            }
        }

        return $phones;
    }

    /**
     * Takes the data associated to a potential phone number, searches for a
     * matching phone or creates a new one with the data.
     *
     * @param string $number
     * @param string $location
     * @param string $description
     *
     * @return ?Phone
     */
    private function createPhone(
        string $number, string $location, string $description
    ) : ?Phone
    {
        if (strlen($number) === 0) {
            return null;
        }
        $searchCriteria = [
            ['number', $number],
            ['description', $description],
        ];
        $loc = null;
        if (strlen($location) > 0) {
            $loc = Location::firstOrCreate(['name' => $location]);
            $searchCriteria[] = ['location_id', $loc->id];
        }
        $phone = Phone::where($searchCriteria)->first();
        if (is_null($phone)) {
            $phone = new Phone();
            $phone->number = $number;
            $phone->description = $description;
            if (!is_null($loc)) {
                $phone->location()->associate($loc);
            }
            $phone->save();
        }

        return $phone;
    }
}
