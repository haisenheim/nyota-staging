<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('forgotpassword', 'API\UserController@forgotpassword');
Route::post('privacy-policy', 'API\PagesController@privacypolicy');
Route::post('about-us', 'API\PagesController@aboutus');
Route::post('getcity','API\AddressController@getcity');
Route::post('getaddress','API\AddressController@getaddress');
Route::post('addorder','API\PaymentController@addorder');

//Route::get('offerlist', 'API\OfferController@index');
Route::post('offerlist', 'API\OfferController@index');

Route::get('categorylist', 'API\CategoryController@index');
Route::post('subcategorylist', 'API\CategoryController@subcategory');
Route::post('categorydetail','API\CategoryController@detail');
Route::post('similarproduct','API\ProductController@similardetail');
Route::post('productdetail','API\ProductController@detail');
Route::post('search','API\ProductController@search');
Route::post('searchpage', 'API\SearchController@index');
Route::post('categorysearchpage', 'API\SearchController@categorysearchpage');
Route::post('setfilter','API\FilterController@filter');
Route::post('getshop','API\ShopController@index');
//Route::post('payment','API\PaymentController@createorder');



Route::group(['middleware' => 'auth:api'], function(){
Route::post('paymentcod', 'API\PaymentController@cashondelivery');
Route::post('language', 'API\LanguageController@index');
Route::get('details', 'API\UserController@details');
Route::get('neighbourhood', 'API\NeighbourhoodController@getneighbourhood');
Route::get('checkout', 'API\CheckoutController@checkout');
Route::post('updateprofile', 'API\UserController@userupdate');
Route::post('changepassword','API\UserController@changepassword');
Route::post('deleteuser','API\UserController@deleteuser');
  
Route::post('addcomment','API\ProductCommentController@addcomment');
Route::post('getcomment', 'API\ProductCommentController@getcomment');
Route::post('addwishlist','API\WishListController@addwishlist');
Route::post('deletewishlist','API\WishListController@deletewishlist');
 // Route::get('getwishlist','API\WishListController@getwishlist');
Route::post('getwishlist','API\WishListController@getwishlist');

  
  
  
  Route::post('addcart','API\CartController@addcart');
  Route::post('deletecart','API\CartController@deletecart');
  //Route::get('getcart','API\CartController@getcart');

 Route::post('getcart','API\CartController@getcart');

  Route::get('orderhistory','API\OrderController@gethistory');  
  Route::post('cancelorder','API\OrderController@cancelorder');
  //Route::get('currentorder','API\OrderController@currentorder');
Route::post('currentorder','API\OrderController@currentorder');

  Route::get('clearhistory','API\OrderController@clearorderhistory');
  Route::get('getuseraddress','API\AddressController@getuseraddress');
  Route::post('addaddress','API\AddressController@addaddress');
  Route::post('deleteaddress','API\AddressController@deleteaddress');
  Route::post('updateaddress', 'API\AddressController@editaddress');
  Route::get('notification', 'API\NotificationController@index');

  
  
  Route::get('getsettings','API\SettingsController@getsettings');
  Route::post('updatesettings','API\SettingsController@updatesettings');  
  
  
  Route::post('temppeyment','API\PaymentController@temppeyment');
 
  Route::post('addneworder','API\OrdernewController@addneworder'); 

//Route::post('modificationorder','API\OrderController@modificationorder'); 
  Route::post('modifyorder','API\OrderController@modifyorder'); 

  
  
  
});

//Route::get('newcreateorder', 'API\PaymentnewController@newcreateorder');
Route::get('success', 'API\PaymentController@createorder');
//Route::get('successnew', 'API\PaymentController@createneworder');

