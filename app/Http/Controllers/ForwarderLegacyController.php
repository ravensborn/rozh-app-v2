<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use App\Models\Log;
use App\Models\Order;

use App\Models\Forwarder;
use App\Models\ForwarderLocation;
use App\Models\ForwarderStatus;

class ForwarderLegacyController extends Controller
{
    private string $hyperpost_token;
    private string $hyperpost_encryptCode;
    private string $hyperpost_url;

    public function __construct()
    {
        $this->middleware('auth');
        $this->hyperpost_url = config('envAccess.HYPERPOST_API_URL');
        $this->hyperpost_token = config('envAccess.HYPERPOST_TOKEN');
        $this->hyperpost_encryptCode = config('envAccess.HYPERPOST_ENCRYPT_CODE');
    }

    //Implement different function for other forwarders. or make one to be compatible with all.
    public function refreshHyperpostOrders($orders): array
    {
        if (!count($orders)) {
            return [
                'refresh_count' => 0,
                'error_array' => [
                    'Error, Order count was 0.'
                ],
            ];
        }

        $numberOfRefreshedOrders = 0;

        $requestBody = json_encode([
            '_token' => $this->hyperpost_token,
            ['sender_track[]' => $orders]
        ]);

        $response = Http::withBody($requestBody, 'application/json')
            ->post($this->hyperpost_url . '/api/other/project/check/tracks/' . $this->hyperpost_encryptCode);

        $tracks = json_decode($response->body(), true);

        $errorMessages = [];

        if ($tracks) {

            foreach ($tracks as $track) {

                $halatId = $track['halat_id'];
                $orderId = $track['sender_track'];

                if(array_key_exists('halat_id', $track) && array_key_exists('sender_track', $track) ) {

                    $order = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
                        ->where('forwarder_order_id', '=', $orderId)
                        ->first();

                    if ($order) {

                        $status = Order::STATUS_FORWARDER_STATUS;

                        if($halatId == null) {

                            $log = Log::create([
                                'content' => [
                                    'message' => 'Forwarder order status id returned null, order is probably deleted from hyperpost.',
                                    'type' => 'error',
                                    'extra' => [
                                        'action' => 'refreshHyperpostOrders',
                                        'error_in_track' => $track,
                                    ]
                                ]
                            ]);

                            array_push($errorMessages, 'halat_id is null, log: '. $log->id . '.');

                            $status = Order::STATUS_FORWARDER_ERROR_REFRESHING;
                        }


                        if($halatId == 11) {
                            $status = Order::STATUS_FORWARDER_RETURNED;
                        }

                        if($halatId == 8) {
                            $status = Order::STATUS_FORWARDER_ORDER_FULFILLED;
                        }

                        $order->update([
                            'status' => $status,
                            'forwarder_status_id' => $halatId,
                            'forwarder_refresh_timestamp' => now(),
                        ]);

                        $numberOfRefreshedOrders++;

                    } else {

                        $log = Log::create([
                            'content' => [
                                'message' => 'Could not find order in database.',
                                'type' => 'error',
                                'extra' => [
                                    'action' => 'refreshHyperpostOrders',
                                    'sent' => $orders,
                                    'received' => \Str::limit($response, 800),
                                    'error_in_track' => $track,
                                ]
                            ]
                        ]);

                        array_push($errorMessages, 'Order not found in database, log: '. $log->id . '.');

                    }

                } else {
                    $log = Log::create([
                        'content' => [
                            'message' => 'Error while getting halat_id and sender_track.',
                            'type' => 'error',
                            'extra' => [
                                'action' => 'refreshHyperpostOrders',
                                'sent' => $orders,
                                'received' => \Str::limit($response, 800),
                                'error_in_track' => $track,
                            ]
                        ]
                    ]);

                    array_push($errorMessages, 'Did not find halat_id and sender_track, log: '. $log->id . '.');
                }
            }

        } else {

            $log = Log::create([
                'content' => [
                    'message' => 'Error decoding response from hyperpost',
                    'type' => 'error',
                    'extra' => [
                        'action' => 'refreshHyperpostOrders',
                        'sent' => $orders,
                        'received' => \Str::limit($response, 800),
                    ]
                ]
            ]);

            array_push($errorMessages, 'Could not decode hyperpost response, log: '. $log->id . '.');
        }

        return [
            'refresh_count' => $numberOfRefreshedOrders,
            'error_array' => $errorMessages,
        ];
    }

    //This function can be modified to work with other forwarders, currently supported, No-forwarder & Hyperpost.
    public function sendOrders($orders): array
    {

        if (!count($orders)) {
            return [
                'sent_count' => 0,
                'error_array' => [
                    'Error, Order count was 0.'
                ],
            ];
        }

        $numberOfSentOrders = 0;
        $errorMessages = [];

        foreach ($orders as $order) {
            //Hyperpost Forwarder
            if ($order->forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

                $total = $order->total();

                if ($total <= 0) {

                    $order->setStatus(Order::STATUS_FORWARDER_ERROR_SENDING);

                    $log = Log::create([
                        'content' => [
                            'message' => 'Order total is less than or equal to 0 iqd, will not be send to forwarder.',
                            'type' => 'info',
                            'extra' => [
                                'action' => 'sendOrders',
                                'forwarder' => $order->forwarder->name,
                                'order_number' => $order->number,
                            ]
                        ]
                    ]);

                   array_push($errorMessages, 'Order total was 0, log: ' . $log->id);
                    continue;
                }

                $data = [
                    '_token' => $this->hyperpost_token,
                    'location_id' => $order->forwarder_location_id,
                    'cod_amount' => $total + $order->delivery_price,
                    'address' => $order->delivery_address,
                    'r_phone1' => $order->customer_primary_phone,
                    'reference_number' => env('APP_NAME') . ' Reference: ' . $order->number,
                ];

                if ($order->customer_secondary_phone) {
                    $data['r_phone2'] = $order->customer_secondary_phone;
                }

                $response = Http::post($this->hyperpost_url . '/api/other/project/add/track/location/' . $this->hyperpost_encryptCode, $data);

                $response = json_decode($response, true);
                $sendErrors = false;

                //Check 1
                if (is_array($response)) {

                    $response = $response[0];

                    //Check 2
                    if (is_array($response)) {

                        //check 3
                        if (array_key_exists('halat_id', $response) && array_key_exists('sender_track', $response)) {

                            $order->status = Order::STATUS_FORWARDER_STATUS;
                            $order->forwarder_status_id = $response['halat_id'];
//                            $order->delivery_price = (int)$response['total'] - (int)$response['cod_amount'];
                            $order->forwarder_order_id = $response['sender_track'];
                            $order->forwarder_refresh_timestamp = now();
                            $order->save();

                            $order->setProperty('delivery_price_calculated_from_hyperpost', (int)$response['total'] - (int)$response['cod_amount']);

                            $numberOfSentOrders++;

                        } else {
                            $sendErrors = true;
                        }

                    } else {
                        $sendErrors = true;
                    }

                } else {
                    $sendErrors = true;
                }

                if ($sendErrors) {

                    $order->setStatus(Order::STATUS_FORWARDER_ERROR_SENDING);

                    $log = Log::create([
                        'content' => [
                            'message' => 'Error in hyperpost response.',
                            'type' => 'error',
                            'extra' => [
                                'action' => 'sendOrders',
                                'forwarder' => $order->forwarder->name,
                                'order_number' => $order->number,
                                'sent' => $data,
                                'received' => \Str::limit($response, 800),
                            ]
                        ]
                    ]);

                    array_push($errorMessages, 'Error in hyperpost response, log: ' . $log->id);
                    continue;
                }
            }
        }

        return [
            'sent_count' => $numberOfSentOrders,
            'error_array' => $errorMessages,
        ];

    }

    //Outdated.
    public function refreshForwarderStatus($forwarder_id): bool
    {
        $forwarder = Forwarder::findOrFail($forwarder_id);

        if ($forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

            $data = Http::post($this->hyperpost_url . '/api/other/project/get/status/' . $this->hyperpost_encryptCode, [
                '_token' => $this->hyperpost_token,
            ]);

            $data = json_decode($data);
            foreach ($data as $status) {

                ForwarderStatus::updateOrCreate([
                    'status_id' => $status->halat_id,
                    'name' => $status->halat_name_en,
                    'forwarder_id' => Forwarder::FORWARDER_HYPERPOST
                ]);
            }

            Log::create([
                'content' => [
                    'message' => 'Successfully refreshed hyperpost statuses from API.',
                    'type' => 'info',
                    'extra' => [
                        'action' => 'refreshForwarderStatus',
                        'forwarder' => $forwarder->name,
                        'imported_count' => ForwarderLocation::count(),
                    ]
                ]
            ]);

            return true;
        }

        Log::create([
            'content' => [
                'message' => 'Forwarder refresh status was called without passing forwarder id.',
                'type' => 'danger',
                'extra' => [
                    'action' => 'refreshForwarderStatus',
                ],
            ]
        ]);

        return true;

    }

    //Outdated.
    public function refreshForwarderLocations($forwarder_id): bool
    {

        $forwarder = Forwarder::findOrFail($forwarder_id);

        if ($forwarder->id == Forwarder::FORWARDER_HYPERPOST) {

            $data = Http::post($this->hyperpost_url . '/api/other/project/get/location/' . $this->hyperpost_encryptCode, [
                '_token' => $this->hyperpost_token,
            ]);

            $data = json_decode($data);

            foreach ($data as $location) {
                ForwarderLocation::updateOrCreate([
                    'location_id' => $location->location_id,
                    'name' => $location->l_name_ku,
                    'forwarder_id' => Forwarder::FORWARDER_HYPERPOST,
                ]);
            }

            Log::create([
                'content' => [
                    'message' => 'Successfully refreshed hyperpost locations from API.',
                    'type' => 'info',
                    'extra' => [
                        'action' => 'refreshForwarderLocations',
                        'forwarder' => $forwarder->name,
                        'imported_count' => ForwarderLocation::count(),
                    ]
                ]
            ]);

            return true;
        }

    }


}
