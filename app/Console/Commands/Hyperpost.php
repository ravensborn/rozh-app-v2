<?php

namespace App\Console\Commands;

use App\Http\Controllers\ForwarderController;
use App\Models\Forwarder;
use App\Models\Order;
use Illuminate\Console\Command;

class Hyperpost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hyperpost:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends all the pending orders to Hyperpost system.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $forwarderClient = new ForwarderController();

        //REFRESH
        $REFRESH_query = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_STATUS, Order::STATUS_FORWARDER_ERROR_REFRESHING])
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


        //SEND, Although paginating this does not make any difference as I understand, since we are making an API call for each order.

        $SEND_query = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_NO_STATUS, Order::STATUS_FORWARDER_ERROR_SENDING]);

        if ($SEND_query->count()) {

            $SEND_perPage = 100;
            $SEND_totalPages = $SEND_query->paginate($SEND_perPage)->lastPage();

            //Send new orders.
            for ($i = 1; $i <= $SEND_totalPages; $i++) {

                $SEND_orders = $SEND_query->paginate($SEND_perPage, ['*'], 'page', $i)->items();
                $forwarderClient->sendOrders($SEND_orders);
            }
        }

        $forwarderClient->sendLogToTelegram();

        return 0;
    }
}
