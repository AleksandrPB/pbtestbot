<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\SettingController;

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

Route::middleware(['auth'])->prefix('admin')->namespace('Backend')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting/store', [SettingController::class, 'store'])->name('setting.store');
    Route::post('setting/setwebhook', [SettingController::class, 'setwebhook'])->name('setting.setwebhook');
    Route::post('setting/getwebhookinfo', [SettingController::class, 'getwebhookinfo'])->name('setting.getwebhookinfo');
}
);

Auth::routes();

////  routes that match methods post/get and registration path
////  execute function at attempt to access certain route
////  use Facade auth and logout method
////  and return to main page
//Route::match(['post', 'get'], 'register', function () {
//    \Illuminate\Support\Facades\Auth::logout();
//    return redirect('/');
//})->name('register');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
