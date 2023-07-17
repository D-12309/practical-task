<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fetch-lists', [CrudController::class, 'fetchLists']);
Route::post('/search-lists', [CrudController::class, 'searchLists']);
Route::post('/create-list', [CrudController::class, 'createList']);
Route::get('/get-list/{id}', [CrudController::class, 'getList']);
Route::post('/update-list/{id}', [CrudController::class, 'updateList']);

