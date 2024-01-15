<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     $test = App\Models\ProductCategory::create([
//         'name' => 'ROOT',
//         'description' => 'category root',
//         'order' => 1,
//         'parent_id' => 0,
//         'image' => ' ',
//         'icon' => ' ',
//         'status' => 'published'
//     ]);
//     dd($test);
// });


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::post('delete_file_folder', ['uses' => 'App\Http\Controllers\Admin\CustomMediaController@delete', 'as' => 'delete_image_media']);
      
});
