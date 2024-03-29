<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElasticSearchController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/search', function () {
    return view('admin/search');
})->name('search');

Route::post('/searchElastic', [ElasticSearchController::class, 'searchItemFromElastic'])->name('searchElastic');

Route::get('/searchElastic', [ElasticSearchController::class, 'searchItemFromElastic'])->name('searchElastic');

require __DIR__.'/auth.php';
