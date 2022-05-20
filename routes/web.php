<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Facebook\FacebookFeedController;
use App\Http\Controllers\Instagram\InstagramFeedController;
use App\Http\Controllers\Linkedin\LinkedinFeedController;
use App\Http\Controllers\SocialConnectController;
use App\Http\Controllers\SchedulePostController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * 
 * ==========================================
 *  User Authentication
 * ==========================================
 * 
 * show view page for user login
 * Route::get('/login')
 * 
 * user login process
 * Route::post('/login')
 * 
 * user logout process
 * Route::post('/logout')
 * 
 */

Route::get('/login', [AuthenticationController::class, 'showLoginPage']);
Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

/**
 * ==========================================
 * Schedule Post
 * ==========================================
 * 
 * show admin dashboard
 * Route::get('/dashboard')
 * 
 * show view page for social connection
 * Route::get('social/connection')
 * 
 * show view page for create schedule post
 * Route::get('schedule/post/create')
 * 
 * store schedule post into DB
 * Route::post('schedule/post/save')
 * 
 * show list of schedule post
 * Route::get('schedule/post/all')
 * 
 * delete single schedule post by post_id
 * Route::post('schedule/post/{post_id}/delete')
 * 
 * publish all unpublished schedule post which status is 0
 * Route::post('schedule-post-publish')
 * 
 */

Route::group(['middleware' => ['auth']], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('social/connection', [SocialConnectController::class, 'socialConnection'])->name('social.connection');
});

Route::group(['middleware' => ['auth'],'prefix'=>'schedule/post'], function () {

    Route::get('/create', [SchedulePostController::class, 'schedulePostCreate'])->name('schedule.post.create');
    Route::post('/save', [SchedulePostController::class, 'schedulePostSave'])->name('schedule.post.save');
    Route::get('/all', [SchedulePostController::class, 'schedulePostList']);
    Route::delete('/{post_id}/delete', [SchedulePostController::class, 'deletePost'])->name('schedule.post.delete');
});

Route::get('schedule-post-publish', [SchedulePostController::class, 'schedulePostPublish'])->name('schedule.post.publish');


/**
 * ------------------------------------------------------
 * Facebook Graph API
 * ------------------------------------------------------
 * 
 *  #user can connect via facebook 
 *  Route::get('facebook/connect')
 * 
 *  #an unique access token will be generate
 *  Route::get('facebook/accessToken') 
 * 
 *  #who have login with facebook will be store on database
 *  Route::get('facebook/profile/store')
 * 
 *  #user accounts details will be show with all of the page informaaton
 *  Route::get('facebook/accounts/{user_id}/details')
 * 
 *  #a view page will show for create post on facebook page
 *  Route::get('facebook/postPage/show')
 * 
 *  #post will be published on all of the facebook pages of user 
 *  Route::post('facebook/post/publish')
 * 
 */

Route::group(['prefix' => 'facebook', 'as' => 'facebook.'], function () {
    Route::get('/connect', [FacebookFeedController::class, 'facebookConnect'])->name('connect');
    Route::get('/accessToken', [FacebookFeedController::class, 'generateAccessToken'])->name('generate.accessToken');
    Route::get('/profile/store', [FacebookFeedController::class, 'storeFbProfileInformation'])->name('store.profile');
    Route::get('/accounts/{user_id}/details', [FacebookFeedController::class, 'fbAccountDetails'])->name('show.accounts.details');
    Route::post('/post/publish', [FacebookFeedController::class, 'postOnfacebookPages'])->name('publish.post');
});


Route::get('/facebook/logout/{user_id}/{user_access_token}',[FacebookFeedController::class,'facebookLogout']);


Route::get('/webhook',function(){

      return "success";
});



/**
 * -----------------------------------------------------------------
 * Linkedin API
 * -----------------------------------------------------------------
 * 
 *  #user can login with linkedin account
 *  Route::get('linkedin/login')
 * 
 *  #an unique access token will be genarated
 *  Route::get('linkedin/accessToken')
 * 
 *  #linkedin profile informatino will be store on DB
 *  ulr('linkedin//{user_id}/accounts/')
 * 
 *  #post will be published on linkedin feed
 *  Route::post('linkedin/post/publish')
 * 
 */

Route::group(['prefix' => 'linkedin', 'as' => 'linkedin.'], function () {

    Route::get('/connect', [LinkedinFeedController::class, 'linkedinConnect'])->name('connect');
    Route::get('/accessToken', [LinkedinFeedController::class, 'genereateAccessToken'])->name('generate.accessToken');
    Route::get('/{user_id}/accounts/', [LinkedinFeedController::class, 'storeLinkedinProfileInfo'])->name('store.profile');
    Route::get('/post/publish', [LinkedinFeedController::class, 'postOnLinkedinFeed'])->name('publish.post');
});

/**
 * ---------------------------------------------------------------------
 * Instrgram API
 * ---------------------------------------------------------------------
 * 
 *  #post will be published on instrgram feed
 *  url('instrgram/post/publish')
 *
 */

Route::group(['prefix' => 'instrgram', 'as' => 'instrgram.'], function () {
    Route::post('/post/publish', [InstagramFeedController::class, 'postOnInstagramFeed'])->name('post.publish');
});





Route::get('post-on-page',[FacebookFeedController::class,'postOnfacebookPages2']);