<?php

use App\Http\Controllers\OrderController;

use App\Http\Livewire\Pages\Home;

use App\Http\Livewire\Pages\Users\Index as UserIndex;
use App\Http\Livewire\Pages\Users\Create as UserCreate;
use App\Http\Livewire\Pages\Users\Edit as UserEdit;

use App\Http\Livewire\Pages\ExpenseItems\Index as ExpenseItemIndex;
use App\Http\Livewire\Pages\ExpenseItems\Create as ExpenseItemCreate;
use App\Http\Livewire\Pages\ExpenseItems\Edit as ExpenseItemEdit;

use App\Http\Livewire\Pages\Orders\Index as OrderIndex;
use App\Http\Livewire\Pages\Orders\Create as OrderCreate;
use App\Http\Livewire\Pages\Orders\Show as OrderShow;
use App\Http\Livewire\Pages\Orders\Edit as OrderEdit;

use App\Http\Livewire\Pages\Orders\Items\Index as OrderItemIndex;

use App\Http\Livewire\Pages\Calculations\Index as CalculationIndex;

use App\Http\Livewire\Pages\Orders\Statistic as OrderStatistic;
use App\Http\Livewire\Pages\Orders\QuickFind as OrderQuickFind;
use App\Http\Livewire\Pages\Orders\QuickAccess as OrderQuickAccess;

use App\Http\Livewire\Pages\Orders\ReturnList as OrderReturnList;

use App\Http\Livewire\Pages\BlockList;

use App\Models\Forwarder;
use App\Models\Order;
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

        Route::get('/expenses/index', ExpenseItemIndex::class)->name('expense-items.index');
        Route::get('/expenses/create', ExpenseItemCreate::class)->name('expense-items.create');
        Route::get('/expenses/{expenseItem}/edit', ExpenseItemEdit::class)->name('expense-items.edit');

        Route::get('/calculations/index', CalculationIndex::class)->name('calculations.index');

        Route::get('/orders/statistics', OrderStatistic::class)->name('orders.statistic');
        Route::get('/orders/quick-find', OrderQuickFind::class)->name('orders.quick-find');
        Route::get('/orders/quick-access', OrderQuickAccess::class)->name('orders.quick-access');

    });


    Route::get('/', Home::class)->name('home');
    Route::get('/orders', OrderIndex::class)->name('orders.index');
    Route::get('/orders/create', OrderCreate::class)->name('orders.create');
    Route::get('/orders/return-list', OrderReturnList::class)->name('orders.return-list');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
    Route::get('/orders/{order}/edit', OrderEdit::class)->name('orders.edit');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    Route::get('/orders/{order}/items', OrderItemIndex::class)->name('orders.items.index');

    Route::get('/block-list', BlockList::class)->name('block-list');


});

//
//Route::get('/test', function () {
//
//    $orders = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
//        ->whereIn('status', [Order::STATUS_FORWARDER_NO_STATUS, Order::STATUS_FORWARDER_ERROR_SENDING])->get();
//
//    $c = new \App\Http\Controllers\ForwarderNewController();
//
//    $c->sendOrders($orders);
//    $c->refreshHyperpostOrders($orders->pluck('forwarder_order_id'));
//
//
//
//    $c->sendLog();
//
//})->name('test');

Route::get('/logout', function () {
    auth()->logout();
    return redirect()->route('login');

})->name('logout');
