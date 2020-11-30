<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Location;
use App\Models\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = rand(400, 800);
        echo 'Creating ' . $count . ' companies.' . PHP_EOL;
        $companies = Company::factory()->count($count)->make();
        foreach ($companies as $company) {
            Company::firstOrCreate([
                'name' => $company->name,
            ]);
        }
        echo 'Creating available phone number locations (home and work)' . PHP_EOL;
        Location::firstOrCreate([
            'name' => 'home',
        ]);
        Location::firstOrCreate([
            'name' => 'work',
        ]);
        $count = rand(400, 800);
        echo 'Creating ' . $count . ' phone numbers.' . PHP_EOL;
        $phones = Phone::factory()->count($count)->make();
        foreach ($phones as $phone) {
            $newPhone = new Phone();
            $newPhone->number = $phone->number;
            $newPhone->description = $phone->description;
            $newPhone->location()->associate($phone->location);

            $newPhone->save();
        }
        $i = 0;
        $totalPasses = 60;
        echo 'Creating Contacts' . PHP_EOL;
        $totalContacts = 0;
        while ($i < $totalPasses) {
            $i++;
            $count = rand(1, 25);
            echo '  Pass ' . $i . ' of ' . $totalPasses . PHP_EOL;
            $company = Company::all()->random();
            echo '  - Creating ' . $count . ' contacts for company ' . $company->name . PHP_EOL;
            $totalContacts += $count;
            $contacts = Contact::factory()->count($count)->make();
            foreach ($contacts as $contact) {
                $phones = Phone::all()->random(rand(1, 10));
                $newContact = new Contact();
                $newContact->first_name = $contact->first_name;
                $newContact->last_name = $contact->last_name;
                $newContact->email = $contact->email;
                $newContact->birthday = $contact->birthday;
                $newContact->company()->associate($company);
                $newContact->address()->associate($contact->address);
                $newContact->photo()->associate($contact->photo);

                $newContact->save();

                foreach ($phones as $phone) {
                    $newContact->phones()->attach($phone);
                }
            }
        }
        $doneMsg = 'Contact seeding completed, a total of ' . $totalContacts;
        $doneMsg .= ' contacts were created' . PHP_EOL;
        echo $doneMsg;
    }
}
