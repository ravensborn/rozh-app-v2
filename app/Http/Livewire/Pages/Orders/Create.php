<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\BlockList;
use App\Models\Forwarder;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\ForwarderLocation;
use App\Models\Page;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Redirector;

class Create extends Component
{
    use LivewireAlert;

    public array $websiteTypes = [
        'facebook', 'instagram', 'other',
    ];
    public Collection $pages;
    public Collection $forwarderLocations;
    public Collection $forwarders;

    public string $customer_name = "";
    public string $customer_profile_link = "";
    public string $customer_profile_type = "other";
    public string $customer_primary_phone = "";
    public string $customer_secondary_phone = "";
    public int $page_id = 1;
    public int $forwarder_id = 1;
    public int $forwarder_location_id = 0;
    public string $searchForwarderLocation = '';
    public string $delivery_address = "";
    public int $delivery_price = 0;

    public bool $blockedPhoneError = false;

    public function updatingSearchForwarderLocation($value)
    {

        if ($this->forwarderLocations) {

            $this->getForwarderLocations($value);

        }
    }

    public function updatingCustomerPrimaryPhone($phone)
    {
        $blockList = BlockList::where('phone', $phone)->first();
        if ($blockList) {
            $this->blockedPhoneError = true;
        } else {
            $this->blockedPhoneError = false;
        }
    }

    public function updatingCustomerProfileLink($profileLink): void
    {

        $this->customer_profile_type = 'other';

        if (str_contains($profileLink, 'instagram')) {
            $this->customer_profile_type = 'instagram';
        }

        if (str_contains($profileLink, 'facebook') || str_contains($profileLink, 'fb')) {
            $this->customer_profile_type = 'facebook';
        }
    }

    public function submitOrder()
    {

        $rules = [
            'customer_name' => 'required|max:255',
            'customer_profile_link' => 'required|max:255',
            'customer_profile_type' => 'required|in:facebook,instagram,other',
            'customer_primary_phone' => 'required|max:255',
            'customer_secondary_phone' => 'nullable|max:255',
            'page_id' => 'required|exists:pages,id',
            'forwarder_id' => 'required|exists:forwarders,id',
            'forwarder_location_id' => 'required|exists:forwarder_locations,location_id',
            'delivery_address' => 'required|max:255',
            'delivery_price' => 'required|numeric',
        ];

        $blockList = BlockList::where('phone', $this->customer_primary_phone)->first();
        if ($blockList) {
            $this->alert('error', 'The customer primary phone number is in block list.');
            return false;
        }

        if ($this->forwarder_id == Forwarder::NO_FORWARDER) {
            $rules['delivery_address'] = 'nullable|max:256';
            $rules['delivery_price'] = 'nullable|max:256';

            $this->delivery_address = "";
            $this->delivery_price = 0;
        }

        $validated = $this->validate($rules);
        $validated['user_id'] = auth()->user()->id;
        $validated['number'] = $this->generateNumber();

        $validated['status'] = Order::STATUS_DEFAULT;

        //TODO Forwarder Settings
        if ($validated['forwarder_id'] == Forwarder::FORWARDER_HYPERPOST) {
            $validated['status'] = Order::STATUS_FORWARDER_NO_STATUS;
        }

        if ($validated['forwarder_id'] == Forwarder::NO_FORWARDER) {
            $validated['forwarder_location_id'] = null;
        }

        if ($validated['customer_secondary_phone'] == 0 || $validated['customer_secondary_phone'] == "") {
            $validated['customer_secondary_phone'] = null;
        } else {
            $validated['customer_secondary_phone'] = str_replace(" ", "", $validated['customer_secondary_phone']);
        }

        $validated['customer_primary_phone'] = str_replace(" ", "", $validated['customer_primary_phone']);

        $order = new Order();
        $order = $order->create($validated);

//        return redirect()->route('orders.show', [
//            'order' => $order->id,
//        ]);
        return redirect()->route('orders.index');
    }

    public function updatedForwarderId()
    {

        $this->forwarderLocations = ForwarderLocation::where('forwarder_id', $this->forwarder_id)->get();
    }

    public function getForwarderLocations($search = null)
    {

        $locations = ForwarderLocation::where('forwarder_id', $this->forwarder_id);

        if ($search) {
            $locations->where('name', 'LIKE', '%' . $search . '%');
        }

        $this->forwarderLocations = $locations->get();

        if ($this->forwarderLocations->count() > 0) {

            $this->forwarder_location_id = $this->forwarderLocations->first()->location_id;
        } else {
            $this->forwarder_location_id = -1;
        }
    }

    public function mount()
    {

        $this->pages = Page::all();
        $this->forwarders = Forwarder::all();

        $this->forwarder_id = Forwarder::FORWARDER_HYPERPOST;

        $this->getForwarderLocations();

    }

    public function render()
    {
        return view('livewire.pages.orders.create')
            ->extends('layouts.app')
            ->section('content');
    }

    private function getLatestOrder(): Model|Builder|null
    {
        return Order::orderBy('created_at', 'DESC')->first();
    }

    private function getLatestOrderId(): int
    {
        return $this->getLatestOrder() ? $this->getLatestOrder()->id : 0;
    }

    public function generateNumber(): string
    {

        $prefix = strtoupper(substr(config('envAccess.APP_NAME'), 0, 1)) . '_';
        $last = $this->getLatestOrderId();
        $next = 1 + $last;

        return sprintf(
            '%s%s',
            $prefix,
            str_pad((string)$next, 6, "0", STR_PAD_LEFT)
        );
    }
}
