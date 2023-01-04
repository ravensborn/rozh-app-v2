<div>


    <div class="row">
        <div class="col-12 col-md-11">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                               Select Orders
                            </h6>
                        </div>
                        <div class="card-body">

                            <div class="row">

                                <div class="col-12 col-md-3">
                                    <label for="from_date">From</label>
                                    <input type="date" class="form-control" id="from_date"
                                           wire:model="from_date"
                                           wire:change="getOrders()">
                                </div>
                                <div class="col-12 col-md-3 mt-2 mt-md-0">
                                    <label for="to_date">To</label>
                                    <input type="date" class="form-control" id="to_date"
                                           wire:model="to_date"
                                           wire:change="getOrders()">
                                </div>
                                <div class="col-12 col-md-3 mt-2 mt-md-0">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="status">Status</label>
                                            <select wire:model="status" wire:change="getOrders()" class="form-control">
                                                @foreach(\App\Models\Order::getInternalStatusArray() as $status)
                                                    <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mt-2 mt-md-0">
                                    @if($this->status == \App\Models\Order::INTERNAL_STATUS_PENDING)
                                        <p style="margin-bottom: 0.5rem;">Return the last item.</p>

                                        <button class="btn btn-primary btn-icon-split mb-3"
                                                wire:click="returnLastUpdatedOrderToPending()">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-history"></i>
                                                    </span>
                                                    <span class="text">
                                                        Revert
                                                    </span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($orders as $order)

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div>
                                            <h4 class="font-weight-bold">{{ $order->number }}</h4>
                                        </div>
                                        <div><b>Date: </b>{{ $order->created_at->format('d-m-Y') }}</div>
                                        <div><b>Customer Name: </b>{{ $order->customer_name }}</div>
                                        <div><b>Primary Phone: </b>{{ $order->customer_primary_phone }}</div>
                                        @if($order->customer_secondary_phone)
                                            <span><b>Secondary Phone: </b>{{ $order->customer_secondary_phone }}</span>
                                        @endif
                                        <div><b>Total: </b>{{ number_format($order->total()) }} IQD</div>
                                        <div><b>Item count: </b>{{ $order->items->count() }}</div>
                                        @if($order->forwarder_id == \App\Models\Forwarder::FORWARDER_HYPERPOST && $order->forwarder_order_id)
                                            <div>
                                                <a href="{{ 'https://hp-iraq.com/customer/add/track/print/' . $order->forwarder_order_id}}">
                                                    Click to print hyperpost invoice
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>Color & Size</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($order->items as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            @if($item->hasMedia('images'))
                                                                <a href="{{ $item->getFirstMediaUrl('images') }}">
                                                                    <img src="{{ $item->getFirstMediaUrl('images') }}"
                                                                         alt="Item Image"
                                                                         width="64" class="img-thumbnail" height="auto">
                                                                </a>
                                                            @else
                                                                no-image
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->color . ' - ' . $item->size }}</td>
                                                        <td>{{ $item->price }} IQD</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ $item->total() }} IQD</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">There are no order items at the moment.</td>
                                                    </tr>
                                                @endforelse
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">

                                        <div class="d-flex justify-content-between">

                                            @if($order->internal_status == \App\Models\Order::INTERNAL_STATUS_PENDING)

                                                <button class="btn btn-warning btn-icon-split mb-3"
                                                        wire:click="setStatus({{ $order->id }}, {{ \App\Models\Order::INTERNAL_STATUS_PROCESS_LATER }})">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                    <span class="text">
                                                        Process Later
                                                    </span>
                                                </button>

                                                <button class="btn btn-success btn-icon-split mb-3"
                                                        wire:click="setStatus({{ $order->id }},{{ \App\Models\Order::INTERNAL_STATUS_FULFILLED }})">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <span class="text">
                                                        Mark as Done
                                                    </span>
                                                </button>

                                            @endif

                                            @if($order->internal_status == \App\Models\Order::INTERNAL_STATUS_PROCESS_LATER)

                                                <button class="btn btn-danger btn-icon-split mb-3"
                                                        wire:click="setStatus({{ $order->id }},{{ \App\Models\Order::INTERNAL_STATUS_CANCELLED }})">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                    <span class="text">
                                                        Cancel
                                                     </span>
                                                </button>


                                                <button class="btn btn-success btn-icon-split mb-3"
                                                        wire:click="setStatus({{ $order->id }},{{ \App\Models\Order::INTERNAL_STATUS_FULFILLED }})">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <span class="text">
                                                        Mark as Done
                                                    </span>
                                                </button>

                                            @endif

                                            @if($order->internal_status == \App\Models\Order::INTERNAL_STATUS_FULFILLED || $order->internal_status == \App\Models\Order::INTERNAL_STATUS_CANCELLED)

                                                <button class="btn btn-warning btn-icon-split mb-3"
                                                        wire:click="setStatus({{ $order->id }}, {{ \App\Models\Order::INTERNAL_STATUS_PROCESS_LATER }})">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                    <span class="text">
                                                        Process Later
                                                    </span>
                                                </button>

                                            @endif

                                        </div>

                                        <div style="background-color: {{ $order->getInternalStatusColor() }}; height: 20px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <p style="margin-bottom: 0;">
                                    No orders to show.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="col-12 col-md-1 mt-3 mt-md-0">
            @foreach($miniOrders as $order)
                <div style="background-color: {{ $order->getInternalStatusColor() }}; color: white; padding: 5px;">
                    {{ $order->number }}
                </div>
            @endforeach
        </div>
    </div>

</div>
