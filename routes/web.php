<?php

use App\Http\Controllers\OrderController;

use App\Http\Livewire\Pages\Home;
use App\Http\Livewire\Pages\Users\Index as UserIndex;
use App\Http\Livewire\Pages\Users\Create as UserCreate;
use App\Http\Livewire\Pages\Users\Edit as UserEdit;
use App\Http\Livewire\Pages\Orders\Index as OrderIndex;
use App\Http\Livewire\Pages\Orders\Create as OrderCreate;
use App\Http\Livewire\Pages\Orders\Show as OrderShow;
use App\Http\Livewire\Pages\Orders\Edit as OrderEdit;

use App\Http\Livewire\Pages\Orders\Items\Index as OrderItemIndex;

use App\Http\Livewire\Pages\Orders\Statistic as OrderStatistic;
use App\Http\Livewire\Pages\Orders\QuickFind as OrderQuickFind;

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


Auth::routes(['register' => false, 'logout' => false]);

Route::middleware('auth')->group(function () {


    Route::group(['middleware' => ['role:admin']], function () {

        Route::get('/users/index', UserIndex::class)->name('users.index');
        Route::get('/users/create', UserCreate::class)->name('users.create');
        Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');
        Route::get('/orders/statistics', OrderStatistic::class)->name('orders.statistic');
        Route::get('/orders/quick-find', OrderQuickFind::class)->name('orders.quick-find');

    });


    Route::get('/', Home::class)->name('home');
    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/create', OrderCreate::class)->name('orders.create');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
    Route::get('/orders/{order}/edit', OrderEdit::class)->name('orders.edit');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    Route::get('/orders/{order}/items', OrderItemIndex::class)->name('orders.items.index');
});

//
//Route::get('/test', function () {
//
//
//})->name('test');

Route::get('/logout', function () {
    auth()->logout();
    return redirect()->route('login');

})->name('logout');
