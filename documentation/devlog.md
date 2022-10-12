# Dev Log
This file is dedicated to document the process that I went through in order to
develop this code challenge. I expect it to be a 3 day code challenge, so I hope
the last entry will say `Day 3` :-)

## The Plan
I'll use this area to document the different tasks I execute as I develop this
application.
 * [x] High level analysis of the project [Completed on Day 1](devlog.md#day-1)
 * [x] Create API boilerplate [Completed on Day 1](devlog.md#day-1)
 * [x] Document local develement process (Back end)  [Completed on Day 2](devlog.md#day-2)
 * [x] (sneaky extra task for the day) Create front end boilerplate [Completed on Day 2](devlog.md#day-2)
 * [x] Analyze database structure and migrations [Completed on Day 2](devlog.md#day-2)
 * [x] Create data models [Completed on Day 3](devlog.md#day-3)
 * [x] Create required endpoints [Completed on Day 3](devlog.md#day-3)
 * [x] Create tests for the endpoints [Completed on Day 3](devlog.md#day-3)
 * [x] Create front end client UI (done-ish) [Completed on Day 3](devlog.md#day-3)
 * [x] Refine the front end so that it's more beautiful [Completed on Day 4](devlog.md#day-4)
 * [x] Update documentation all over the project [Completed on Day 4](devlog.md#day-4)
 * [x] Deploy the project so that we can have a demo version
        of the project [Completed on Day 4](devlog.md#day-4)

## Day 1
This day is about getting to grips with the problem and determining the best
course of action to follow.

### Analysis
**Desired Result**  
Create an API that does the following;
 * Create a contact record
 * Retrieve a contact record
 * Update a contact record
 * Delete a contact record
 * Search for a record by email or phone number
 * Retrieve all records from the same state or city
 
(Extra credit) Create a UI that consumes this API

**High level things to think about**  
On a high level, I'd like to achieve the following;
 * An easily deployable API. In order to have an agnostic target, I'm thinking
  that maybe I deploy to docker.
 * Our API should be focused and contain only what's needed for it to function.
  Our API should be a micro-service.
 * Due to time constraints, I'm going to try to reduce scope a bit here; I'm
  going assume that this API is not publicly accessible and I therefore feel
  reasonably comfortable foregoing any type of implementation for credentials
  or API keys; the API should exist in a private network (not publicly
  accessible)
   * *Please Note*: Regardless of public / private API, all APIs should be
   implemented with at least an `api_key` so that only valid clients can talk to
   it. Given time constraints, I've chosen to assume that creating logic for an
   `api_key` is not the focus of this exercise and that knowing the "best
   practices" should suffice for this project.
 * Since we're only creating an API, I'm going to use Laravel Lumen for this
  project; it's in line with the micro-service idea we're pursuing.
 * This service will not
  have millions of contacts, I can assume that there should someday be thousands
  of contacts. Contacts must be persisted. This allows me to conclude the
  following;
   * Contacts data can be easily normalized, so we can go with an SQL database.
    I'm going to use MySQL.
   * If I'm not going to have millions of contacts to search through, then DB
    indexing will be enough, we don't need to think about using some sort of
    service for interacting with the database. In the interest of time _and an
    objective lack of need_, I won't implement a dedicated search service (such
    as Elastic Search); my searches will go directly to the database.
   * Searching brings with it some interesting challenges. Fuzzy searches come
    to mind. In order to implement a fuzzy search I can go one of three routes;
     * **Route #1**: *Rethink my search service requirement and use
      ElasticSearch as it has many useful services for quick fuzzy searching*:
      Implementing something like ElasticSearch would also let me leverage
      Laravel Scout (which is fun). Given the time constraints however, the only
      effective way I can do this is if I use the Laravel Scout driver with an
      existing service such as Algolia; this doesn't seem to be very useful for
      a code challenge.
     * **Route #2**: *Use a PHP library that implements fuzzy searches with the
      DB*: I don't think it's a good use of my time to implement libraries for
      this challenge; I don't want to prove that I can read documentation and
      implement stuff... I don't think this is the spirit of a code challenge.
     * **Route #3**: *Implement a fuzzy search algorithm myself*: Thinking about
      this is very interesting. There's various known approaches I can take;
       * Levenshtein distance
       * Something implementing a Phonetic algorithm
       * My own invention using a living binary tree of all combinations of
        registered words such that I can approximate to the closest word match
        (similar to an implementation of the Levenshtein distance).
       * Given all of the above, I think I'm just going to steer clear from this
        as 30 minutes of thought in this has shown that implementing my own fuzzy
        search algorithm would likely take me at least several days.
     * **Hidden route #4**: *Using the database's native `SOUNDEX` algorithm*:
      This is the poor man's option to fuzzy searching... it has some caveats;
       * It's an algorithm that uses phonetics in order to find matches.
        Unfortunately, it uses only English phonetics so this makes it a bit
        restricting. This method will however find matches in any language, it
        just won't find incorrectly spelled words in other languages.
       * It's completely DB driven, so it's barely an implementation for the
        dev.
   * Conclusions regarding Contact search: Although I'm not sure if fuzzy search
    is going to be implemented in this challenge at all, I'll take *hidden
    route #4* into account when implementing the search endpoint :-).
 * Using DB transactions... I'm not ready to take a decision on this item quite
  yet. I think I'll think about whether or not I'll use transactions when I get
  to the problem itself. My high level thought is that we're not updating often,
  we're searching often... so transactions might be overkill for a small app.
 * We're going to want to do the extra credit item of creating a UI in for
  this challenge. In order to do this I'll use the gateway pattern; since the
  API will be in a private IP, my client will not have direct access to it.
  Using gateways brings with it the added benefit that we won't need to worry
  about issues with CORS.

Given the above, I think we're ready to start working. We're using Laravel Lumen
so the goal here is to create a basic Lumen app that's deployable to docker and
then implement the functionality; I'm taking this route because I already know
what resources my app is going to consume and I also want to get the tedium out
of the way from the start!

### Let's get the boilerplate going
Upon creating a new Lumen app, I noticed that there is no
`php artisan key:generate` command, so I quickly created a command to create a
key for the `.env`; it's just a quality of life command that doesn't write
anywhere, it just echos out a key for use where needed (`php artisan key:create`).

With that out of the way, I move on to getting the repo ready for docker.
#### Getting the repo ready for docker
I won't go into detail on preparing the repo for docker. I can say that it was
successfully finished, we can review this in the dedicated documentation to get
the [local environment running](./deploy/local.md)

### Tasks completed today
 * [x] Do an initial but complete project analysis
 * [x] Create the API boiler plate implementing a strategy for docker
  containerization

## Day 2
My objectives for today
 - [ ] Document local development process (Back end)
 - [ ] Analyze database structure
 - [ ] Create required endpoints
 - [ ] Create tests for the endpoints

### Document local environment process
I did this in a quick and dirty fashion, it can now be found [here](./deploy/local.md)
(I basically updated the existing file with the new data).

### Create database structure (first try)
The first thing I thought about here is what data am I capturing and how can I
validate it?
A contact should have;
 * A Name: This is easy to validate, it's just two strings, a first name and a
  last name.
 * A Company: This is also easy to validate, it's just a string (I won't try to
  correct input for a company with a similar name as this brings with it too
  much complexity).
 * Profile Image: This is a file... I need to validate it's an image. I'd like to
  also store images that are at least 400px x 400px. I've randomly chosen this
  resolution, but square images are convenient.
 * An email: This is just a regex check of type email.
 * A birth date: This is just a date, nothing complicated here.
 * A phone number: This can be complicated if I want it to be. I'll choose to make
  it simple, it should have at least 6 numbers.
   * With a phone number type
 * An address: This is complicated. I really don't want to make a complex database
  here... I also don't want to validate country names / city names (never mind
  street names!). I think I'll delegate the responsibility to procure a street
  address to the front end in this case. Since my front end is consuming a gateway,
  I can plug a service to that gateway (for example, Google Maps API). Taking a
  look at [schema.org addresses](https://schema.org/address) and
  [schema.org Geo Coordinates](https://schema.org/GeoCoordinates), I came up with
  the following address properties
   * Street Address (`string`)
   * Postal code (`string`)
   * City (`string`)
   * State or Province (`string`)
   * Country (`string`)
   * latitude (`number`)
   * longitude (`number`)
  
I'm going to place my backend effort on pause for now as I want to delve into what
I can do with the Google maps API. It's not always a great practice to integrate
to one specific provider for data, I'm going to try to avoid that, but I definitely
want my implementation to be compatible with what I obtain from the maps api.

### Integrate Google maps services for address validation
So, if I'm going to delegate the Google services to the front end, I'll just build
the front end boilerplate now.

After a couple of hours, I've created a boilerplate that builds a structure where
we have a gateway and front end build. It took me longer than I wanted, but we at
least have that extra effort done. I won't go into detail with regards to the FE
code as that's a bit out of the scope of this code challenge; generally speaking,
the gateway is using `Express`, and the FE project is using `vue` with
`tailwindcss` for styling (linting with ESLint). I would've normally used something
like `AlpineJS` for this project, but the interviewer recommened I use `vue` in
this case :). We're not going to use any test framework for this as the main
challenge is in the backend. Maybe on Day 3, I'll dockerize the front end also in
order to make it easy to deploy.
 
With the boilerplate done, I can try to integrate google services to this. I'm
going to use the Google Places API by following instructions from [this page](https://developers.google.com/maps/documentation/javascript/places-autocomplete).
I reviewed the API and implementing it with `vue` was easy. I haven't completely
finished the front end code at this point as we wanted to verify that the Places
API can give us all the data we need in order to add addresses to our database.
With my demo code complete, I successfully managed to procure all of the data we
need, YAY!

### Create database structure (second try)
With the above, I've whipped up a schema;

| companies |        |
|-----------|--------|
| id        | pk     |
| name      | string |

| contacts   |              |
|------------|--------------|
| id         | pk           |
| first_name | text         |
| last_name  | text         |
| company_id | fk_companies |
| avatar     | longtext     |
| email      | text         |
| birthday   | date         |
| address_id | fk_addresses |

| contact_phone |                  |
|---------------|------------------|
| contact_id    | fk_contacts (pk) |
| phone_id      | fk_phones (pk)   |

| phones      |              |
|-------------|--------------|
| id          | pk           |
| number      | text         |
| description | text         |
| location_id | fk_locations |

| locations |      |
|-----------|------|
| id        | pk   |
| name      | text |
*Used to tag for "home" or "work"*

| addresses      |                 |
|----------------|-----------------|
| id             | pk              |
| postal_code_id | fk_postal_codes |
| street_address | text            |
| description    | text            |
| city_id        | fk_cities       |
| state_id       | fk_states       |
| country_id     | fk_countries    |
| latitude       | number          |
| longitude      | number          |

| postal_codes |      |
|--------------|------|
| id           | pk   |
| code         | text |

| cities |      |
|--------|------|
| id     | pk   |
| name   | text |

| states |      |
|--------|------|
| id     | pk   |
| name   | text |

| countries |      |
|-----------|------|
| id        | pk   |
| name      | text |

I've gone ahead and created the migrations for these... that's going to be it for
today. I'm going to be honest here... the database discussed above is not very well
normalized at all; there should be a lot of relationships that aren't there
 * Relationship between `postal_codes`, `cities`, `states` and `countries`.
 * A company should also have an address; there's lots of additional data associated
  with a company that is not contemplated in this database.
 * I'm basically choosing to ignore this with the intent of saving time; suffice
  it to say that in a real application, this would not be my database schema.

### Tasks completed today
 - [x] Document local develement process (Back end)
 - [x] (sneaky extra task for the day) Create front end boilerplate
 - [x] Analyze database structure and migrations
 - [ ] Create required endpoints
 - [ ] Create tests for the endpoints

## Day 3
Today we're going to try to complete the tasks that were left over from yesterday
and also try to finish the API completely. With the API complete, we may very well
be with time to finish the front end also... I'm not sure we're going to be able
to tidy up the front end too much as it's a lot to do in one day, but I'd really
like to finish this code challenge on day 3. If there's a day 4 it'll be for testing

My objectives for today
 - [ ] Create data models
 - [ ] Create required endpoints
 - [ ] Create tests for the endpoints
 - [ ] Create front end client UI (done-ish)

### Data Models
Let's create all the required models for our database. This part was done without
much of a hitch, this is a focused microservice, it also isn't very complex, I
didn't do any complicated stuff like repository models; I went with normal models.

I then created all the factories that are associated for these models. This task
took me longer than expected because I wanted to use the factories to seed my
database; the php faker library has no way of creating unique pieces of content on
multiple sessions and this created problems when trying to run my database seeder
and finding database constraint errors all over the place. In order to solve this,
I went ahead and created dedicated functions for my constrained fields... getting
to this solution took me a little bit longer than expected as there were edge
cases (such as the faker library itself running out of fake names and going back
to repeated names).

### The endpoints
In order to create my endpoints, I had to think about whether or not I should use
closures for my endpoints. Even though this is a simple microservice, I really
didn't see this to be so incredibly simple, that I can write clean closures for
my endpoints; I decided to use controllers and references to my controllers from
the route. I also chose to use single action controllers as they offer me a great
deal of opportunity to have simple legible classes that aren't polluted by
unrelated actions that implement other dependencies. After some testing and
finding that there's actually a lot of shared logic between my controllers, I
created an abstract class that implements my reuseable code (eager loading logic,
pagination and even my index action). I finally went ahead and created a Postman
project with all my endpoints.
Endpoints;
* `/`: My base route that only returns the name of the application and the app
 version. Available methods below;
  * Get
* `/contact`: My contact resource that implements all required actions for this
 api. Available methods below;
  * Get, `GET /contact`
  * Show, `GET /contact/{id}`
  * Store, `POST /contact`
  * Update, `PUT /contact/{id}`
  * Delete `DELETE /contact/{id}`
* `/address`: I found that it was best to make a dedicated resource for addresses
 as it gave me the opportunity to isolate this particular problem. Addresses
 have a great deal of data associated to them (The address itself, latitude,
 longitude, postalcode, city, state/province, country). Many of an address's
 associated data lives in multiple tables. So the decision here was to create
 addresses through a dedicated resource that then gets associated to my the
 contact in question. Available methods below;
  * Get, `GET /address`
  * Show, `GET /address/{id}`
  * Store, `POST /address`
  * Delete `DELETE /address/{id}`
* `/company`: Even though creating a dedicated resource for companies is outside
 the scope of this challenge, I chose to create one. The reasoning here is that,
 like addresses, companies have an incredible amount of data associated to them.
 I will not implement all of the logic and data that can be associated to a company,
 but I find it great to start having a dedicated resource for it. In order to not
 increase complexity, this resource is read only. Available methods below;
  * Get, `GET /company`
  * Show, `GET /company/{id}`
* `/location`: This is just a convenience endpoint, very easy to create (no eager
 loading in this one).
* `/phone`: Given that I need to search for contacts based on phone numbers, I
 chose to create a read only resource for phones (just like company). Available
 methods below;
  * Get, `GET /phone`
  * Show, `GET /phone/{id}`
* `/photo`: Used only to store photos. The photos themselves are just hosted on
 a public directory that can be accessed by doing `/images/{media_id.ext}` (you
 obtain the media data when looking at the photo attribute of a contact);
  * Store, `POST /phone`
  
### Digging deeper with my endpoints
I chose to go a little bit further with the endpoints. Since they also all have
relationships, I wanted to be able to eagerload them. So I added some query strings
to the getters.
 * `include`: You can add an `include` query that allows you to specify what you'd
  like to eager load. For example, a contact can have the associated `Company` and
  `Address` eager loaded.
  
I also wanted to be able to search my resources. So I added some query strings to
the getters as well.
 * `search`: You can search for specific resource items by doing
  `search=search string`. All searches are using a "contains" strategy, so you
  don't need to search for exact matches.
 * The `Contact` resource is a special case as you can search based on specific
  related fields. `Contact`s can be searched based on their first name, last name
  and email. here's a few examples of how this query string works;
   * `search=spen`: Searches for "spen" in ['first_name', 'last_name', 'email']
   * `search=spen[email]`: Searches for "spen" in ['email']
   * `search=spen[first_name,last_name]`: Searches for "spen" in ['first_name', 'last_name']
   * `search=spen[]`: Searches for "spen" in ['first_name', 'last_name', 'email']
   * `search=spen[emails,first_name]`: Searches for "spen" in ['first_name']
   * `search=spen[emails]`: Searches for "spen" in ['first_name', 'last_name', 'email']

## The tests
At the end of the day, I'm finding that I'm just taking up too much time with this
code challenge. I created tests for the `\address` resource only. In a real world
project, I'd think deeply about how testing should take place in a project and
what structure should be followed for the testing directories and architecture.
However, if I dedicate all the time I want to dedicate to testing, I won't finish
this by day 4... so tests will have to stay at just doing light testing of the
`\address` endpoint.

## The frontend
After completing all of the above, I was left with very little time to complete
the front end effort. I never intended to create a full front end project as my
front end will not implement authentication or any type of tokens for requests
to the gateway (in a real project all of these security features would be required).
This code challenge is focused on the backend, so I whipped up my client quickly,
the general boilerplate was completed on Day 2 so this was just a matter of getting
the real functionality working. Unfortunately I'm a pefectionist and I'm not happy
with how my front end looks; it lacks a bit of heart and love.

### Tasks completed today
The goal on day 3 was to finish the challenge. Even though I did finish the
requirement, I'm going to add one more day to this challenge in order to get that
UI looking a bit better and also to get the documentation more complete.
 - [x] Create data models
 - [x] Create required endpoints
 - [x] Create tests for the endpoints
 - [x] Create front end client UI (done-ish)

## Day 4
There's three main objectives today. Today is the final day of development on this
project, we won't have a day 5, so everything needs to be completed today
 - [ ] Refine the front end so that it's more beautiful
 - [ ] Update documentation all over the project
 - [ ] Deploy the project so that we can have a demo version

## The frontend
This has been a fun but labor intensive job; I'm a perfectionist and I at least
want to create a demo that's pleasant to play with... I think I've achieved that.
I will be honest, my front end code is not super clean, it's missing refactors and
is missing tests... I'm not to worried about these misses as the central goal of
this code challenge is to do the backend; the client was just something extra that
enabled me to have some fun with vuejs. In the end, I didn't use Vue 3 for this
project as it's just too new and I didn't want to risk issues with the build
process; I would've liked to do the project taking advantage of the composition
API, but I'll have to leave that for next time.

## Documentation
Documentation is a beast that is never completely tamed. There's always more
documentation that can be written.

## Deploy the project
The project got deployed in a private droplet in digitalocean, the backend is
running behind private IPs (just like the recommended practice for this micro-service).
The front end is also running in a droplet. We're good, we're done!!

### Tasks completed today
The goal on day 3 was to finish the challenge. Even though I did finish the
requirement, I'm going to add one more day to this challenge in order to get that
UI looking a bit better and also to get the documentation more complete.
 - [x] Refine the front end so that it's more beautiful
 - [x] Update documentation all over the project
 - [x] Deploy the project so that we can have a demo version
