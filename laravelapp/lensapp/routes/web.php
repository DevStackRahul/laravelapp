<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
//   return view('welcome');
//});
// uploadPrescriptionFile
/* Route::post('/uploadPrescriptionFile', 'CollectionController@uploadPrescriptionFile'); */

Route::post('/uploadPrescriptionFile', 'CollectionController@uploadPrescriptionFile');
Route::post('/file-upload', 'FileUploadController@fileUpload');

Route::get('/', function () {
    return view('welcome');
})->middleware(['auth.shopify'])->name('home');

//This will redirect user to login page.
Route::get('/login', function () {
    if (Auth::user()) {
        return redirect()->route('home');
    }
    return view('login');
})->name('login');

//get collections route
Route::get('/fetchCollections', 'CollectionController@fetchCollections');

// save default two collection route
Route::get('/saveDefaultCollections', 'CollectionController@saveDefaultCollections');

// fecth fetchPrescriptionTypes
Route::get('/fetchPrescriptionTypes', 'CollectionController@fetchPrescriptionTypes');

//save saveLenes
Route::get('/saveLenes', 'CollectionController@saveLenes');

//addasset file route
Route::get('/addassetfile', 'CollectionController@addassetfile');

// delete collection
Route::get('/deleteCollections', 'CollectionController@deleteCollections');

//Route for sendEmail in cotact form
Route::get('/sendEmail', 'ContactController@sendEmail');

//list of product from shopify  route
Route::get('/fetchallproducts', 'CollectionController@fetchallproducts');

//editCollections route
Route::get('/editCollections', 'CollectionController@editCollections');

//updateCollectionsOne route
Route::get('/updateCollectionsOne', 'CollectionController@updateCollectionsOne');

//addProductTocollection route
Route::get('/addProductTocollection', 'CollectionController@addProductTocollection');


// lenssettings route
Route::get('/lenssettings', 'CollectionController@lenssettings');

// fetchsettings route
Route::get('/fetchsettings', 'CollectionController@fetchsettings');
