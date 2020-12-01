# API information
The API for this project is more or less straight forward, you can import the
collection of requests to postman by using the [api_postman.json](./api_postman.json)
in this directory.

I'm going to go through each individual endpoint and discuss what it does.

## App version endpoint
```php
$router->get('/', function () use ($router) {
    return env('APP_NAME', 'Contacts') . ': ' . $router->app->version();
});
```
This endpoint just gives the app version as specified in the environment file.

## General notes
### Search
All endpoints that have a `GET` request have search functionality. Search works
by adding a query to the endpoint. For example, for a `/contact` endpoint, you
could do `{{host}}/contact/?search=rod`: this will search for all contacts that
contain the "rod" string in the following fields;
 * 'first_name'
 * 'last_name'
 * 'email'
You can also search within one specific field by doing
`{{host}}/contact/?search=rod[first_name]`, or even multiple fields by doing a
comma separated list as shown here:
 `{{host}}/contact/?search=rod[first_name,last_name]`
For each resource, I'll show the available search fields.
### Pagination
All endpoints that have a `GET` request have pagination built in. Responses come
in the following format;
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 56,
    "per_page": 15,
    "to": 15,
    "total": 826
  }
}
```
In order to get a specific page from the result, you just have to add a `page`
query parameter to the request: `{{host}}/contact/?page=5`.
### Eager loading
All resources that have `GET` requests and can get a single item can be eager
loaded with their related data (if it has any). For example, a `Contact` may have
a `Company` associated to it; in order to also get the associated `Company`, you
can `{{host}}/contact/5?include=company`; you can also do it for the full get
request;  
`{{host}}/contact?page=5&include=company,address,phones,photo`  
*(please note that I included multiple relationships by separating them with a comma).*

All of the discussed features can be added together in a request whenever
applicable (as shown immediately below).
`{{host}}/contact/?page=2&include=company,address,phones,photo&search=ro[last_name]` 

## Contact Resource
This is the only full resource in the application.

Things to keep in mind
* The update endpoint is a `PUT` request, so it's destructive, you need to send
 all of the data you want to save as it will mass overwrite.
* Available search fields: `['first_name', 'last_name', 'email', 'city', 'state']`.
* Eager loads: `company,address,phones,photo`.
```php
$router->get('/contact', [
    'uses' => 'Contact\IndexContact',
]);
$router->get('/contact/{id}', [
    'uses' => 'Contact\ShowContact',
]);
$router->post('/contact', [
    'uses' => 'Contact\StoreContact',
]);
$router->put('/contact/{id}', [
    'uses' => 'Contact\UpdateContact',
]);
$router->delete('/contact/{id}', [
    'uses' => 'Contact\DeleteContact',
]);
```

## Address Resource
This is an almost complete resource; it's just missing the update endpoint.
Things to keep in mind
* Available search fields: Only searches by the `street_address` field.
* Eager loads: `postalcode,city,state,country`.
```php
$router->get('/address', [
    'uses' => 'Address\IndexAddress',
]);
$router->get('/address/{id}', [
    'uses' => 'Address\ShowAddress',
]);
$router->post('/address', [
    'uses' => 'Address\StoreAddress',
]);
$router->delete('/address/{id}', [
    'uses' => 'Address\DeleteAddress',
]);
```

## Company Resource
This resource only has the getters as they're automatically created when the
contact is created.
* Available search fields: Only searches by the `name` field.
* Eager loads: `contacts`.
```php
$router->get('/company', [
    'uses' => 'Company\IndexCompany',
]);
$router->get('/company/{id}', [
    'uses' => 'Company\ShowCompany',
]);
```

## Company Resource
This resource only has the getters as they're automatically created when the
contact is created.
* Available search fields: Only searches by the `name` field.
* Eager loads: none.
```php
$router->get('/location', [
    'uses' => 'Location\IndexLocation',
]);
$router->get('/location/{id}', [
    'uses' => 'Location\ShowLocation',
]);
```

## Photo uploads
This is a sole resource that's dedicated to uploading photos.
```php
$router->post('/photo', [
    'uses' => 'Photo\StorePhoto',
]);
```

## Fetching photos
The photo uploads actually write to a public directory, in order to fetch a photo,
just look at the contact photo entity and copy the value of name (example below);
req: `{{host}}/contact/825?include=photo`
```json
{
  "id": 825,
  "first_name": "Rodrigo",
  "last_name": "Rodrigo Cespedes",
  "email": "rodrigo@testmyfakeemail.com",
  "birthday": "1986-11-22",
  "company_id": 549,
  "address_id": 1,
  "photo_id": 5,
  "created_at": "2020-11-30T13:31:24.000000Z",
  "updated_at": "2020-11-30T14:08:18.000000Z",
  "photo": {
    "id": 5,
    "name": "images/TxC8k9C5ZoNcPl9PM98z3WFsps7q8Gp0YyqWSAlr.png",
    "created_at": "2020-11-30T13:27:03.000000Z",
    "updated_at": "2020-11-30T13:27:03.000000Z"
  }
}
```
Note that the photo has a name of `images/TxC8k9C5ZoNcPl9PM98z3WFsps7q8Gp0YyqWSAlr.png`

Image can be viewed at;
`{{host}}/images/TxC8k9C5ZoNcPl9PM98z3WFsps7q8Gp0YyqWSAlr.png`
