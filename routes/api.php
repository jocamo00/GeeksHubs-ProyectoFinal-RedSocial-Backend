<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Rutas del controlador de usuarios
Route::apiResource('/user', 'UserController');
Route::put('/user/update', 'UserController@update');
Route::post('/login', 'UserController@login');
Route::post('/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/user/avatar/{filename}', 'UserController@getImage');
Route::get('/user/detail/{id}', 'UserController@detail');

// Rutas del controlador de posts
Route::apiResource('post', 'PostController');
Route::post('/post/upload', 'PostController@upload');
Route::get('/post/image/{filename}', 'PostController@getImage');
Route::get('/post/user/{id}', 'PostController@getPostsByUser');

// Rutas del controlador de comentario
Route::apiResource('/comment', 'CommentController');
