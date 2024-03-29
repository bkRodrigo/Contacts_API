# Code Challenge
## Contacts API

Thank you for taking the time to look at this project! Please note that there
is a `documentation` directory on this repository. In the `documentation` directory
you'll find
* [my Devlog](./documentation/devlog.md): In my devlog you'll see what I did during
 each of the 4 full days of work
* [local env](./documentation/deploy/local.md): Here you'll find detailed instructions
 on getting the dev env running. Doing a production version of this deploy would
 be very simple with anything like Ansible to run scripts and cleaning up the
 `docker-compose` stuff
* [api docs](./documentation/api.md): Here you'll get some detailed information for
 interacting with the API (you also get a Postman config [here](documentation/api_postman.json)!)
 

The front end of this project isn't very well documented, the goal of that project
was just to show a functional client working with this API. The idea of this API
is that it runs in a private network as a microservice and would never be publicly
available. Other services would interact with the microservice and implement their
own token and handshake strategies.

## Regarding the requirements
Search contact by email
`{{host}}/contact/?search=email@gmail.com[email]`
Search contact by phone number
`{{host}}/contact/?search=555-5555[phones]`
Search contact by city
`{{host}}/contact/?search=Cordoba[city]`
Search contact by state
`{{host}}/contact/?search=Cordoba[state]`

There is more informationon the [api docs](./documentation/api.md), this API does a
lot more!

I hope you enjoy this project!

also, take a look at the client in action (front end project) [here](contacts.brewkrafts.com)!.
