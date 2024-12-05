<?php

use App\Http\Controllers\SnsNotificationController;
use App\Services\EmailRenderService;
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
Route::get('/campaign-page/{id}', function() {
    return view('welcome');
})->where('id', '.*');
Route::post('/tracking/notification', SnsNotificationController::class);

Route::post('/recipients/bulk', 'App\Http\Controllers\RecipientController@bulk');
Route::get('/recipients/filter', 'App\Http\Controllers\RecipientController@byFilter');

Route::get('/recipients', [App\Http\Controllers\RecipientController::class ,'index']);
Route::post('/recipients', 'App\Http\Controllers\RecipientController@store');
Route::get('/recipients/{id}', 'App\Http\Controllers\RecipientController@show');
Route::put('/recipients/{id}', 'App\Http\Controllers\RecipientController@update');
Route::delete('/recipients/{id}', 'App\Http\Controllers\RecipientController@destroy');

Route::get('/campaigns', 'App\Http\Controllers\CampaignController@index');
Route::post('/campaigns', 'App\Http\Controllers\CampaignController@store');
Route::get('/campaigns/{id}', 'App\Http\Controllers\CampaignController@show');
Route::put('/campaigns/{id}', 'App\Http\Controllers\CampaignController@update');
Route::delete('/campaigns/{id}', 'App\Http\Controllers\CampaignController@destroy');

Route::get('/assets/{asset_id}', [App\Http\Controllers\AssetController::class,'show']);
Route::get('/assets/search/{term}', \App\Http\Controllers\EmailSearchController::class);

Route::get('/campaigns/{campaign_id}/recipients', 'App\Http\Controllers\CampaignRecipientController@index');

Route::put('/campaigns/{campaign_id}/recipients/{recipient_id}/delivered', 'App\Http\Controllers\CampaignRecipientController@delivered');
Route::put('/campaigns/{campaign_id}/recipients/{recipient_id}/opened', 'App\Http\Controllers\CampaignRecipientController@opened');
Route::put('/campaigns/{campaign_id}/recipients/{recipient_id}/failed', 'App\Http\Controllers\CampaignRecipientController@failed');
Route::put('/campaigns/{campaign_id}/recipients/{recipient_id}/clicked', 'App\Http\Controllers\CampaignRecipientController@clicked');

//send scheduled emails
Route::get('/send-scheduled-emails', 'App\Http\Controllers\CampaignController@sendScheduledEmails');



