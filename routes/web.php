<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return env('APP_NAME', 'Contacts') . ': ' . $router->app->version();
});

$router->get('/contact', [
    'uses' => 'ContactController@index',
]);
$router->get('/contact/{id}', [
    'uses' => 'ContactController@show',
]);
$router->post('/contact', [
    'uses' => 'ContactController@store',
]);
$router->post('/contact/{id}/image', [
    'uses' => 'ContactController@storeImage',
]);
/*
$router->post($uri, $callback);
$router->put($uri, $callback);
$router->patch($uri, $callback);
$router->delete($uri, $callback);
$router->options($uri, $callback);
*/
$router->get('/address', [
    'uses' => 'AddressController@index',
]);
$router->get('/address/{id}', [
    'uses' => 'AddressController@show',
]);
$router->post('/address', [
    'uses' => 'AddressController@store',
]);

$router->get('/company', [
    'uses' => 'CompanyController@index',
]);
$router->get('/company/{id}', [
    'uses' => 'CompanyController@show',
]);
