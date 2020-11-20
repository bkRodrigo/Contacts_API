# Dev Log
This file is dedicated to document the process that I went through in order to
develop this code challenge. I expect it to be a 3 day code challenge, so I hope
the last entry will say `Day 3` :-)

## The Plan
I'll use this area to document the different tasks I execute as I develop this
application.


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
 * Upon consulting with Kin + Carta, I was advised that this service will not
  have millions of contacts, I can assume that there should someday be thousands
  of contacts. Contacts must be persisted. This allows me to conclude the
  following;
   * Contacts data can be easily normalized, so we can go with an SQL database.
    I'm going to use MySQL.
   * If I'm not going to have millions of contacts to search through, then DB
    indexing will be enough, we don't need to think about using some sort of
    service for interacting with the database. In the interest of time, I and an
    objective lack of need, I won't implement a dedicated search service (such
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


### Tasks completed today
 * [x] Do an initial but complete project analysis
 * [x] Create the API boiler plate implementing a strategy for docker
  containerization 
