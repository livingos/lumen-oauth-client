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

$app->get('/', 'OAuthController@oauth');

$app->get('/callback', 'OAuthController@callback');

$app->get('/user', 'OAuthController@user');

$app->get('/refresh', 'OAuthController@refresh');


$app->get('/erudus', 'ErudusAPIController@oauth');
$app->get('/erudus/{id}', 'ErudusAPIController@product');
$app->get('/erudus/{id}/pdf', 'ErudusAPIController@pdf');