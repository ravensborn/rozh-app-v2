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

        $forwarderClient = new ForwarderController;

        $appName = config('envAccess.APP_NAME');
        $telegramMessage = "$appName - Reporting\n";
        $telegramMessage .= "Timestamp: " . now()->format('d-m-Y / h:i A') . "\n";

        //REFRESH
        $REFRESH_query = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_STATUS, Order::STATUS_FORWARDER_ERROR_REFRESHING])
            ->where('forwarder_order_id', '!=', null); //Unnecessary just to make sure we aren't sending empty.

        if ($REFRESH_query->count()) {

            $REFRESH_perPage = 100;
            $REFRESH_totalPages = $REFRESH_query->paginate($REFRESH_perPage)->lastPage();

            $REFRESH_totalNumberOfOrders = 0;
            $REFRESH_errorArray = [];


            for ($i = 1; $i <= $REFRESH_totalPages; $i++) {

                $REFRESH_orders = $REFRESH_query->paginate($REFRESH_perPage)->items();
                $REFRESH_orders = collect($REFRESH_orders)
                    ->map(function ($order) {
                        return [
                            'sender_track' => $order->forwarder_order_id,
                        ];
                    })
                    ->toArray();

                //Refresh previous orders.
                $REFRESH_response = $forwarderClient->refreshHyperpostOrders($REFRESH_orders);
                $REFRESH_totalNumberOfOrders += $REFRESH_response['refresh_count'];
                $REFRESH_errorArray = array_merge($REFRESH_errorArray, $REFRESH_response['error_array']);
            }

            $telegramMessage .= "\n";
            $telegramMessage .= "Refresh status\n";
            $telegramMessage .= "Pages: $REFRESH_totalPages\n";
            $telegramMessage .= "Total # items: $REFRESH_totalNumberOfOrders\n";

            if (count($REFRESH_errorArray)) {
                $telegramMessage .= "Errors:\n";
                foreach ($REFRESH_errorArray as $errorMessage) {
                    $telegramMessage .= " - $errorMessage\n";
                }
            }

        }


        //SEND
        $SEND_query = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
            ->whereIn('status', [Order::STATUS_FORWARDER_NO_STATUS, Order::STATUS_FORWARDER_ERROR_SENDING]);

        if ($SEND_query->count()) {

            $SEND_perPage = 100;
            $SEND_totalPages = $SEND_query->paginate($SEND_perPage)->lastPage();

            $SEND_totalNumberOfOrders = 0;
            $SEND_errorArray = [];

            //Send new orders.
            for ($i = 1; $i <= $SEND_totalPages; $i++) {

                $SEND_orders = $SEND_query->paginate($SEND_perPage, ['*'], 'page', $i)->items();

                $SEND_response = $forwarderClient->sendOrders($SEND_orders);
                $SEND_totalNumberOfOrders += $SEND_response['sent_count'];
                $SEND_errorArray = $SEND_response['error_array'];
            }

            $telegramMessage .= "\n";
            $telegramMessage .= "Forwarding status\n";
            $telegramMessage .= "Pages: $SEND_totalPages\n";
            $telegramMessage .= "Total # items: $SEND_totalNumberOfOrders\n";
            if (count($SEND_errorArray)) {
                $telegramMessage .= "Errors:\n";
                foreach ($SEND_errorArray as $errorMessage) {
                    $telegramMessage .= " - $errorMessage\n";
                }
            }
        }


        $telegramController = new TelegramBotController();
        $telegramController->sendMessage('1745934868', $telegramMessage);

        //$this->line('Hello world testing.');
        return 0;
    }
}
