<?php

namespace App\Console\Commands;

use App\Http\Controllers\ForwarderController;
use App\Models\Forwarder;
use App\Models\Order;
use Illuminate\Console\Command;

class HyperpostGetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hyperpost:get-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes hyperpost locations and statuses.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $forwarderClient = new ForwarderController();

        $forwarderClient->refreshForwarderStatuses(Forwarder::FORWARDER_HYPERPOST);
        $forwarderClient->refreshForwarderLocations(Forwarder::FORWARDER_HYPERPOST);

        $forwarderClient->sendLogToTelegram();

        return 0;
    }
}
