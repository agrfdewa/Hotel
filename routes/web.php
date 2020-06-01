<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => true]);
// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Rooms
    Route::delete('rooms/destroy', 'RoomsController@massDestroy')->name('rooms.massDestroy');
    Route::resource('rooms', 'RoomsController');

    // Events
    Route::delete('events/destroy', 'EventsController@massDestroy')->name('events.massDestroy');
    Route::resource('events', 'EventsController');

    Route::get('system-calendar', 'SystemCalendarController@index')->name('systemCalendar');

    Route::get('search-room', 'BookingsController@searchRoom')->name('searchRoom');
    Route::post('book-room', 'BookingsController@bookRoom')->name('bookRoom');

    Route::get('my-credits', 'BalanceController@index')->name('balance.index');
    Route::post('add-balance', 'BalanceController@add')->name('balance.add');

    Route::resource('transactions', 'TransactionsController')->only(['index']);
});

Route::get('/user', function(){
    return view('user');
});

Route::post('/upateaccount', '\App\Http\Controllers\Admin\UsersController@postSaveUser')->name('account.save');

Route::get('/user', [
    'uses' => 'UserController@getUser',
    'as' => 'user'
]);

Route::get('user', '\App\Http\Controllers\Admin\UsersController@profile');
Route::post('user', '\App\Http\Controllers\Admin\UsersController@update_avatar'); 