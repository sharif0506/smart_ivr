<?php
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
    return "Welcome To Smart IVR";
});

$router->post('/login/phone', 'AuthController@loginWithPhone');
$router->post('/login/pin', 'AuthController@loginWithPin');
$router->post('/login/auth', 'AuthController@loginWithAuthCode');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/vivr-data', 'PageDataController@getPageData');
    $router->post('/logout', function () use ($router) {
        return $router->app->version();
    });
});
$router->post('/ice', 'IceFeedbackController@storeIceFeedback');
