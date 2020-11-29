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
    'uses' => 'Contact\IndexContact',
]);
$router->get('/contact/{id}', [
    'uses' => 'Contact\ShowContact',
]);
/*
$router->post('/contact', [
    'uses' => 'Contact\StoreContact,
]);
$router->post('/contact/{id}/avatar', [
    'uses' => ''Contact\AssociateAvatarContact',
]);
$router->post($uri, $callback);
$router->put($uri, $callback);
$router->patch($uri, $callback);
$router->delete($uri, $callback);
$router->options($uri, $callback);
*/
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

$router->get('/company', [
    'uses' => 'Company\IndexCompany',
]);
$router->get('/company/{id}', [
    'uses' => 'Company\ShowCompany',
]);
