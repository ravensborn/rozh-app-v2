<?php

namespace App\Http\Controllers;

use App\Models\Token;

use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Forwarder;
use App\Models\ForwarderLocation;
use App\Models\ForwarderStatus;

class ForwarderController extends Controller
{
    private string $host = '';
    private string $email = '';
    private string $password = '';

    private string $token = '';
    private bool $tokenValidated = false;
    private int $tokenGenerationCounter = 0;

    private TelegramBotController $botController;

    private string $log = '';
    public string $ordersLevelLog = '';

    public int $totalNumberOfOrdersToSend = 0;
    public int $numberOfSentOrders = 0;
    public int $totalNumberOfOrdersToRefresh = 0;
    public int $numberOfRefreshedOrders = 0;
    public int $sendOrdersBatchCounter = 0;
    public int $updateOrdersBatchCounter = 0;


    private array $headers = [
        'accept' => 'application/json'
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->host = config('envAccess.HYPERPOST_API_URL');
        $this->email = config('envAccess.HYPERPOST_USER_EMAIL');
        $this->password = config('envAccess.HYPERPOST_USER_PASSWORD');

        $this->botController = new TelegramBotController();

        $appName = config('envAccess.APP_NAME');

        $this->writeLog("$appName - Reporting\n");
        $this->writeLog("Timestamp: " . now()->format('d-m-Y / h:i A'));
        $this->writeLog("\n-----------\n");

        $this->prepareToken();
    }

    public function writeLog($string)
    {
        $this->log .= $string;
    }

    public function generateToken()
    {

        $http = Http::withHeaders($this->headers)->post($this->host . '/api/v1/sender-api/auth/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($http->successful()) {

            $this->writeLog("Retrieving token from API.\n");

            $this->token = $http->json()['data']['token'];

            Token::updateOrCreate(['key' => 'hyperpost_token_' . $this->email], ['value' => $this->token]);

            $this->writeLog("Retrieved token from API and saved to database.\n");

            $this->validateToken();

        } else {

            $this->writeLog("Failed while retrieving token from API.");
            $this->writeLog("\n-----------\n");

        }


    }

    public function prepareToken()
    {

        $this->writeLog("Preparing token...\n");

        $token = Token::where('key', 'hyperpost_token_' . $this->email)->first();

        if ($token) {

            $this->writeLog("Retrieved token from database.\n");

            $this->token = $token->value;

            $this->validateToken();

        } else {

            $this->generateToken();
        }

    }

    public function validateToken()
    {

        $http = Http::withHeaders($this->headers)
            ->withToken($this->token)
            ->get($this->host . '/api/v1/sender-api/get-statuses');

        if ($http->successful()) {

            $this->tokenValidated = true;

            $this->writeLog("Token verification successful.");
            $this->writeLog("\n-----------\n");

        } else {

            if ($this->tokenGenerationCounter == 0) {

                $this->writeLog("Token verification failed, generating new token.\n");
                $this->tokenGenerationCounter += 1;
                $this->generateToken();

            } else {

                $this->writeLog("Token verification failed.\n");
                $this->writeLog("-----------\n");
            }
        }

    }

    public function checkToken(): bool
    {
        if (!$this->tokenValidated) {

            $this->writeLog("Error with token, task terminated.\n");

            return false;
        }
        return true;
    }

    public function sendLogToTelegram()
    {
        $this->botController->sendMessage($this->botController->defaultChatId, $this->log);
        $this->log = '';

    }

    //This function can be modified to work with other forwarders, currently supported, No-forwarder & Hyperpost.
    public function sendOrders($orders)
    {

        $this->sendOrdersBatchCounter += 1;


        if (!count($orders)) {

            $this->writeLog("Task skipped, orders array had no elements.\n");

            return false;
        }

        $this->numberOfSentOrders = 0;

        $this->ordersLevelLog = '';

        foreach ($orders as $order) {
            //Hyperpost Forwarder
            if ($order->forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

                $total = $order->total();

                if ($total <= 0) {

                    $order->setStatus(Order::STATUS_FORWARDER_ERROR_SENDING);

                    $this->ordersLevelLog .= "Order total was zero for " . $order->number . "\n";

                    continue;
                }

                $data = [
                    'total_price' => $total + $order->delivery_price,
//                    'total_price' => $total,
                    'location_id' => $order->forwarder_location_id,
                    'receiver_phone_number_1' => $order->customer_primary_phone,
                    'detail_location' => $order->delivery_address,
                    'note' => env('APP_NAME') . ' Reference: ' . $order->number,
                ];

                if ($order->customer_secondary_phone) {
                    $data['receiver_phone_number_2'] = $order->customer_secondary_phone;
                }

                $http = Http::withHeaders($this->headers)->withToken($this->token)->post($this->host . '/api/v1/sender-api/add-track', $data);

                if ($http->successful()) {

                    $response = $http->json();

                    $order->setProperty('log', '');

                    if ($response['status']) {

                        $track = $response['data']['track'];

                        $order->status = Order::STATUS_FORWARDER_STATUS;
                        $order->forwarder_status_id = $track['status_id'];
                        $order->forwarder_order_id = $track['id'];
                        $order->forwarder_refresh_timestamp = now();
                        $order->save();

                        $order->setProperty('delivery_price_calculated_from_hyperpost', $track['delivery_price']);

                        ++$this->numberOfSentOrders;

                    } else {

                        $ordersLevelLog = "Issue while sending order " . $order->number . "\n";
                        $order->setProperty('log', $response);

                    }
                } else {
                    $ordersLevelLog = "Issue while sending order " . $order->number . "\n";
                    $order->setProperty('log', ['request' => $data, 'response' => $http]);
                }

            }

            ++$this->totalNumberOfOrdersToSend;
        }


    }

    //Implement different function for other forwarders. or make one to be compatible with all.
    public function refreshHyperpostOrders($orders)
    {

        $this->updateOrdersBatchCounter += 1;
        $this->ordersLevelLog = '';

        if (!count($orders)) {

            $this->writeLog("Task skipped, orders array had no elements.\n");

            return false;
        }

        $data = json_encode([
            'track_ids' => $orders
        ]);

        foreach ($orders as $order) {

            $order = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
                ->where('forwarder_order_id', '=', $order)
                ->first();

            $order->update([
                'forwarder_status_id' => null,
                'status' => Order::STATUS_FORWARDER_ORDER_DOESNT_EXIST
            ]);

        }

        $http = Http::withHeaders($this->headers)
            ->withToken($this->token)
            ->withBody($data, 'application/json')
            ->post($this->host . '/api/v1/sender-api/get-multiple-tracks-info');


        if ($http->successful()) {

            $tracks = $http->json()['data']['tracks']['data'];

            foreach ($tracks as $track) {

                $trackId = $track['id'];
                $statusId = $track['status']['id'];

                $order = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
                    ->where('forwarder_order_id', '=', $trackId)
                    ->first();

                if ($order) {

                    $setStatus = Order::STATUS_FORWARDER_STATUS;

                    if ($statusId == 10) {
                        $setStatus = Order::STATUS_FORWARDER_RETURNED;
                    }

                    if ($statusId == 8) {
                        $setStatus = Order::STATUS_FORWARDER_ORDER_FULFILLED;
                    }

                    $order->update([
                        'status' => $setStatus,
                        'forwarder_status_id' => $statusId,
                        'forwarder_refresh_timestamp' => now(),
                    ]);

                    $this->numberOfRefreshedOrders++;

                } else {

                    $order->update([
                        'status' => Order::STATUS_FORWARDER_ERROR_REFRESHING,
                    ]);

                    $ordersLevelLog .= "Order with track " . $trackId . " was not found in local database.\n";
                }

                ++$this->totalNumberOfOrdersToRefresh;

            }
        } else {
            $this->ordersLevelLog .= "Failed making request to hyperpost api on batch: " . $this->updateOrdersBatchCounter . "\n";
        }


    }

    public function refreshForwarderStatuses($forwarder_id)
    {

        $forwarder = Forwarder::findOrFail($forwarder_id);

        if ($forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

            $this->writeLog("Preparing to refresh hyperpost status list.\n");

            if (!$this->checkToken()) {
                return null;
            }

            $http = Http::withHeaders($this->headers)
                ->withToken($this->token)
                ->get($this->host . '/api/v1/sender-api/get-statuses');

            if ($http->successful()) {

                $data = $http->json()['data']['statuses'];

                foreach ($data as $status) {
                    ForwarderStatus::updateOrCreate([
                        'status_id' => $status['id'],
                        'forwarder_id' => Forwarder::FORWARDER_HYPERPOST
                    ], ['name' => $status['name_ku']]);
                }

                $this->writeLog("Successfully refreshed hyperpost status list.\n");

            } else {

                $this->writeLog("Failed refreshing hyperpost status list.\n");
            }
        }

    }

    public function refreshForwarderLocations($forwarder_id)
    {

        $forwarder = Forwarder::findOrFail($forwarder_id);

        if ($forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

            $this->writeLog("Preparing to refresh hyperpost location list.\n");

            if (!$this->checkToken()) {
                return null;
            }

            $http = Http::withHeaders($this->headers)
                ->withToken($this->token)
                ->get($this->host . '/api/v1/sender-api/get-locations');

            if ($http->successful()) {

                $data = $http->json()['data']['locations'];

                foreach ($data as $location) {
                    ForwarderLocation::updateOrCreate([
                        'location_id' => $location['id'],
                        'forwarder_id' => Forwarder::FORWARDER_HYPERPOST
                    ], [
                        'name' => $location['name_ku'],
                    ]);
                }

                $this->writeLog("Successfully refreshed hyperpost location list.\n");

            } else {

                $this->writeLog("Failed refreshing hyperpost location list.\n");
            }
        }

    }

    public function deleteOrders($forwarder_id)
    {


        $forwarder = Forwarder::findOrFail($forwarder_id);

        if ($forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

            $orders = Order::whereDate('created_at', '>=', '2023-01-15')->whereNotNull('forwarder_order_id');

            $perpage = 50;
            $totalpages = $orders->paginate($perpage)->lastPage();

            //Send new orders.
            for ($i = 1; $i <= $totalpages; $i++) {

                $send = $orders->paginate($perpage, ['*'], 'page', $i)->items();

                foreach ($send as $order) {


                    $http = Http::withHeaders($this->headers)
                        ->withToken($this->token)
                        ->delete('https://hp-iraq.co/api/v1/sender-api/delete-track/' . $order->forwarder_order_id, [
                            '_method' => 'delete'
                        ]);

                    $order->update([
                        'forwarder_order_id' => null,
                        'forwarder_refresh_timestamp' => null,
                    ]);
                    

                   echo json_encode($http->json());
                }

                echo 'page switched <br>';

            }



            echo 'done all';

        }

    }

}
