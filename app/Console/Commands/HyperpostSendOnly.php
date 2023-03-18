<?php

namespace App\Console\Commands;

use App\Http\Controllers\ForwarderController;
use App\Models\Forwarder;
use App\Models\Order;
use Illuminate\Console\Command;

class HyperpostSendOnly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hyperpost:send-only';

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

        $forwarderClient->writeLog("Sending orders to forwarder initiated.\n");

        $orders = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_NO_STATUS, Order::STATUS_FORWARDER_ERROR_SENDING])
        ->get();

        $forwarderClient->sendOrders($orders);

        $forwarderClient->writeLog('Orders scheduled to send: ' . $forwarderClient->totalNumberOfOrdersToSend . "\n");
        $forwarderClient->writeLog('Orders sent: ' . $forwarderClient->numberOfSentOrders . "\n");
        if (strlen($forwarderClient->ordersLevelLog) > 0) {
            $forwarderClient->writeLog("Error Log:\n" . $forwarderClient->ordersLevelLog);
        }
        $forwarderClient->writeLog("Task successfully finished.\n");
        $forwarderClient->writeLog("-----------\n");

        $forwarderClient->sendLogToTelegram();

        return 0;
    }
}
