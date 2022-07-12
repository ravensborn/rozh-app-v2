<?php

namespace App\Console\Commands;

use App\Http\Controllers\ForwarderController;
use App\Http\Controllers\TelegramBotController;
use App\Models\Forwarder;
use App\Models\Log;
use App\Models\Order;
use Http;
use http\Env;
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
        $ordersToSend = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_NO_STATUS, Order::STATUS_FORWARDER_ERROR_SENDING])
            ->get();

        $forwarderClient = new ForwarderController;

        //Send new orders.
        $numberOfOrdersToSend = $forwarderClient->sendOrders($ordersToSend);

        $ordersToRefresh = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_STATUS, Order::STATUS_FORWARDER_ERROR_REFRESHING])
            ->where('forwarder_order_id', '!=', null) //Unnecessary just to make sure we aren't sending empty.
            //->pluck('forwarder_order_id')
            ->get()
            ->map(function ($order) {
                return [
                    'sender_track' => $order->forwarder_order_id,
                ];
            })
            ->toArray();

        //Refresh previous orders.
        $numberOfOrdersToRefresh = $forwarderClient->refreshHyperpostOrders($ordersToRefresh);

        $telegramController = new TelegramBotController();
        $telegramController->sendMessage('1745934868', config('envAccess.APP_NAME') . " Reporting\nTimestamp: " . now()->format('d-m-Y / h:i A') . "\nOrders sent to forwarder: " . $numberOfOrdersToSend . "\nOrders refreshed with forwarder: " . $numberOfOrdersToRefresh);

        //$this->line('Hello world testing.');
        return 0;
    }
}
