<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
| Middleware options can be located in `app/Http/Kernel.php`
|
*/

// Homepage Route
Route::get('/', 'WelcomeController@welcome')->name('welcome');
Route::get('/change-city-name-to-id', 'UserScriptController@index')->name('changecitynametoid');
Route::get('/privacypolicy', 'WelcomeController@privacypolicy')->name('privacypolicy');
Route::post('/sendmail', 'WelcomeController@sendmail')->name('sendmail');
Route::get('/login', ['as' => 'activate', 'uses' => 'Auth\LoginController@showLoginForm'])->name('login')->where('name','login');
// Authentication Routes
Auth::routes();


// Public Routes
Route::get('/register', 'Auth\RegisterController@showregisterForm')->name('register');
Route::post('/register/create', 'Auth\RegisterController@create')->name('registercreate');
Route::group(['middleware' => ['web', 'activity']], function () {

    // Activation Routes
    

	//Route::get('admin/{name?}', ['as' => 'activate', 'uses' => 'Auth\LoginController@showLoginForm'])->where('name','login');
    Route::get('/activate', ['as' => 'activate', 'uses' => 'Auth\ActivateController@initial']);
    Route::get('/activate/{token}', ['as' => 'authenticated.activate', 'uses' => 'Auth\ActivateController@activate']);
    Route::get('/activation', ['as' => 'authenticated.activation-resend', 'uses' => 'Auth\ActivateController@resend']);
    Route::get('/exceeded', ['as' => 'exceeded', 'uses' => 'Auth\ActivateController@exceeded']);
    Route::get('/user/home', ['as' => 'activate-success.home',   'uses' => 'ActivatedsuccessController@index']);
    Route::get('/user/reset/home', ['as' => 'reset-success.home',   'uses' => 'ResetsuccessController@index']);
    // Socialite Register Routes
    Route::get('/social/redirect/{provider}', ['as' => 'social.redirect', 'uses' => 'Auth\SocialController@getSocialRedirect']);
    Route::get('/social/handle/{provider}', ['as' => 'social.handle', 'uses' => 'Auth\SocialController@getSocialHandle']);

    // Route to for user to reactivate their user deleted account.
    Route::get('/re-activate/{token}', ['as' => 'user.reactivate', 'uses' => 'RestoreUserController@userReActivate']);
	Route::get('/admin/home', ['as' => 'admin.home',   'uses' => 'LoginController@index']);
    Route::get('/vendor/home', ['as' => 'vendor.home',   'uses' => 'UserController@index']);
    Route::get('admin/home/get-chart-list/{id}','UserController@search');
    Route::get('admin/home/get-vendorchart-list/{id}','UserController@vendorsearch');
});

// Registered and Activated User Routes
Route::group(['middleware' => ['auth', 'activated', 'activity']], function () {

    // Activation Routes
    Route::get('/activation-required', ['uses' => 'Auth\ActivateController@activationRequired'])->name('activation-required');
    Route::get('/logout', ['uses' => 'Auth\LoginController@logout'])->name('logout');
});

// Registered and Activated User Routes
Route::group(['middleware' => ['auth', 'activated', 'activity', 'twostep']], function () {

	Route::get('/admin/home', ['as' => 'admin.home',   'uses' => 'UserController@index']);
    Route::post('admin/home/search-chart', 'UserController@search')->name('search-chart');
    //  Homepage Route - Redirect based on user role is in controller.
    Route::get('/home', ['as' => 'public.home',   'uses' => 'UserController@index']);


});


// Registered, activated, and is admin routes.
Route::group(['middleware' => ['auth', 'activated', 'role:admin', 'activity', 'twostep']], function () {
    Route::get('admin/profile','ProfilesAdminController@index');
    Route::put('admin/profile/profileupdate/{id}','ProfilesAdminController@update')->name('profileupdateadmin');
    Route::post('admin/profile/changepass/{id}','ProfilesAdminController@changepass')->name('changepasswordadmin');
    Route::post('admin/changepass/{id}','ChangePasswordController@update')->name('changepassworduser');
    Route::resource('/admin/user','CustomerController');
    Route::get('/admin/user/block/{id}', 'CustomerController@block')->name('block');
    Route::get('/admin/user/unblock/{id}','CustomerController@unblock')->name('unblock');

    Route::resource('/admin/vendors','VendorController');
    Route::get('/admin/vendors/block/{id}', 'VendorController@block')->name('block-vendor');
    Route::get('/admin/vendors/unblock/{id}','VendorController@unblock')->name('unblock-vendor');
    Route::get('admin/search-vendors', 'VendorController@search')->name('search-vendors');

    Route::resource('/admin/categories','CategoryController');
    Route::post('admin/category/delete', 'CategoryController@destroy')->name('category-delete');
    Route::get('admin/search-categories', 'CategoryController@search')->name('search-categories');

    Route::resource('/admin/banner','BannerController');
    Route::resource('/admin/product_attribute_type','ProductAttributeTypeController');
    Route::get('admin/search-attribute_type', 'ProductAttributeTypeController@search')->name('search-attribute_type');
    Route::resource('/admin/product_attribute','ProductAttributeController');
    Route::get('admin/search-attribute', 'ProductAttributeController@search')->name('search-attribute');
    Route::resource('/admin/products','ProductController');
    Route::get('admin/search-product', 'ProductController@search')->name('search-products');
    Route::post('/admin/blukproduct/','ProductController@import')->name('blukproduct');


Route::get('admin/get-subcategory/{id}','ProductController@getcategory')->name('get-subcategory');

    Route::post('images-upload', 'ProductController@storeimage')->name('product.storeMedia');

    Route::get('admin/settings','SettingsController@index')->name('setting-index');
    Route::post('admin/settingsupdate','SettingsController@update')->name('updatesettings');
    
    Route::get('/admin/products/addcomment/{id}', 'ProductController@addcomment')->name('product-addcomment');
    Route::post('/admin/products/storecomment/{id}', 'ProductController@storecomment')->name('product-storecomment');
    Route::get('/admin/products/editcomment/{id}', 'ProductController@editcomment')->name('product-editcomment');
    Route::post('/admin/products/updatecomment/{id}', 'ProductController@updatecomment')->name('product-updatecomment');
    Route::delete('/admin/products/deletecomment/{id}', 'ProductController@destroycomment')->name('comment-destroycomment');

    Route::delete('images-upload/{id}', 'ProductController@destroyimage')->name('images-upload.destroy');
    Route::resource('/admin/faq','FAQController');
    Route::get('admin/search-faq', 'FAQController@search')->name('search-faq');
    Route::get('admin/search_attribute/{id}', 'ProductController@searchattribute')->name('search_attribute');
    Route::resource('/admin/notification','NotificationController');

    Route::resource('/admin/state','StateController');
    Route::get('admin/search-state', 'StateController@search')->name('search-state');

    Route::resource('/admin/district','DistrictController');
    Route::get('admin/search-district', 'DistrictController@search')->name('search-district');
    Route::get('admin/get-state-list/{id}','DistrictController@getStateList');

    Route::resource('/admin/city','CityController');
    Route::get('admin/search-city', 'CityController@search')->name('search-city');
    Route::get('admin/get-district-list/{id}','CityController@getdistrictList');


Route::resource('/admin/shipping','ShippingController');
    Route::get('admin/search-shipping', 'ShippingController@search')->name('search-shipping');
   // Route::get('admin/get-district-list/{id}','CityController@getdistrictList');



    Route::resource('/admin/location','LocationController');
    Route::get('admin/search-location', 'LocationController@search')->name('search-location');
    Route::get('admin/get-city-list/{id}','LocationController@getcityList');

    Route::resource('/payment/success','PaymentController');

    Route::resource('/admin/pages','PagesController');
    Route::resource('/admin/neighbourhood','NeighbourhoodController');
     Route::get('admin/search-neighbourhood', 'NeighbourhoodController@search')->name('search-neighbourhood');
    
    Route::resource('/admin/cart','CartController');
    Route::get('admin/search-cart', 'CartController@search')->name('search-cart');

    Route::resource('/admin/order','OrderController');
    Route::get('admin/order-status/{id}/{order_id}','OrderController@changeorderstatus');
    Route::post('admin/order-estimate_date/{order_id}','OrderController@changestimatedate')->name('order-estimate_date');
    Route::get('admin/search-order', 'OrderController@search')->name('search-order');

    Route::get('admin/orderhistory','OrderController@orderhistory');
    Route::get('admin/orderhistory/{id}','OrderController@orderhistoryshow');
    Route::get('admin/search-order-history', 'OrderController@searchistory')->name('search-order-history');

    Route::resource('/users/deleted', 'SoftDeletesController', [
        'only' => [
            'index', 'show', 'update', 'destroy',
        ],
    ]);

    
    Route::get('/search-users', 'CustomerController@search')->name('search-users');






});
Route::group(['middleware' => ['auth', 'activated', 'role:vendor', 'activity', 'twostep']], function () {
    Route::get('vendor/profile','ProfilesController@index');
    Route::put('vendor/profile/profileupdate/{id}','ProfilesController@update')->name('profileupdate');
    Route::post('vendor/profile/changepass/{id}','ProfilesController@changepass')->name('changepassword');
    Route::resource('/vendor/product','VendorProductController');
    Route::get('vendor/search-product', 'VendorProductController@search')->name('search-products-vendor');
    Route::post('vendor/images-upload', 'VendorProductController@storeimage')->name('vendorproduct.storeMedia');
    Route::delete('vendor/images-upload/{id}', 'VendorProductController@destroyimage')->name('vendorimages-upload.destroy');
    Route::get('vendor/search_attribute/{id}', 'VendorProductController@searchattribute')->name('vendor-search_attribute');

    Route::get('/vendor/products/addcomment/{id}', 'VendorProductController@addcomment')->name('vendor-product-addcomment');
    Route::post('/vendor/products/storecomment/{id}', 'VendorProductController@storecomment')->name('vendor-product-storecomment');
    Route::get('/vendor/products/editcomment/{id}', 'VendorProductController@editcomment')->name('vendor-product-editcomment');
    Route::post('/vendor/products/updatecomment/{id}', 'VendorProductController@updatecomment')->name('vendor-product-updatecomment');
    Route::delete('/vendor/products/deletecomment/{id}', 'VendorProductController@destroycomment')->name('vendor-comment-destroycomment');

Route::get('vendor/vendor-get-subcategory/{id}','VendorProductController@getcategory')->name('vendor-get-subcategory');


    Route::resource('/vendor/order','VendorOrderController');
    Route::get('vendor/order-status/{id}/{order_id}','VendorOrderController@changeorderstatus');
    Route::get('vendor/search-order', 'VendorOrderController@search')->name('vendor-search-order');
    Route::post('vendor/order-estimate_date/{order_id}','VendorOrderController@changestimatedate')->name('vendor-order-estimate_date');

    Route::get('vendor/orderhistory','VendorOrderController@orderhistory');
    Route::get('vendor/orderhistory/{id}','VendorOrderController@orderhistoryshow');
    Route::get('vendor/search-order-history', 'VendorOrderController@searchistory')->name('vendor-search-order-history');
    Route::post('vendor/vendor-blukproduct/','VendorProductController@import')->name('vendor-blukproduct');


    Route::resource('/vendor/cart','VendorCartController');
    Route::get('vendor/search-cart', 'VendorCartController@search')->name('vendor-search-cart');
});


Route::get('/clear-cache', function() {
    Artisan::call('config:cache');
    return "Cache is cleared";
});

Route::get('admin/testing', 'TestController@index');
Route::get('admin/testingemail', 'TestController@emailtest');