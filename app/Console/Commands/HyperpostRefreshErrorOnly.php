<?php

namespace App\Console\Commands;

use App\Http\Controllers\ForwarderController;
use App\Models\Forwarder;
use App\Models\Order;
use Illuminate\Console\Command;

class HyperpostRefreshErrorOnly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hyperpost:refresh-error-only';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $forwarderClient = new ForwarderController();



        //REFRESH
        $forwarderClient->writeLog("Updating orders with hyperpost initiated (refresh errors only).\n");
        $REFRESH_query = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_ORDER_DOESNT_EXIST, Order::STATUS_FORWARDER_ERROR_REFRESHING])
            ->where('forwarder_order_id', '!=', null); //Unnecessary just to make sure we aren't sending empty.

        if ($REFRESH_query->count()) {

            $REFRESH_perPage = 50;
            $REFRESH_totalPages = $REFRESH_query->paginate($REFRESH_perPage)->lastPage();

            for ($i = 1; $i <= $REFRESH_totalPages; $i++) {

                $REFRESH_orders = $REFRESH_query->paginate($REFRESH_perPage)->items();
                $REFRESH_orders = collect($REFRESH_orders)
                    ->map(function ($order) {
                        return $order->forwarder_order_id;
                    })->toArray();

                $forwarderClient->refreshHyperpostOrders($REFRESH_orders);
            }
        }

        $forwarderClient->sendLogToTelegram();

        return 0;
    }
}
