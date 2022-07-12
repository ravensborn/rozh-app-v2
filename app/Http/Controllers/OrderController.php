<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function invoice(Order $order) {

        return view('pages.orders.invoice', [
            'order' => $order,
        ]);
    }
}
