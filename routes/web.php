<?php

use Illuminate\Support\Facades\Cache;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');


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
    Cache::flush();
    return $router->app->version();
});

$router->options('*',function (){
    dd("hello");
    return response()->isOk();
});

$router->group(["prefix"=>"api/v1"], function () use($router){
    $router->get('user/{id}', function ($id) {
        return 'User '.$id;
    });
    $router->group(["middleware" => ['role:super-admin']],function () use ($router){

    });


    $router->get('/key', function() {
        return \Illuminate\Support\Str::random(32);
    });

    $router->post('/register', 'UserController@register');
    $router->post('/login', 'UserController@login');
    $router->get('/me', ['middleware' => 'auth.jwt', 'uses' => 'UserController@me']);
});


