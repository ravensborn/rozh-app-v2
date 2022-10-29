<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Order Details
                        <small>
                            <a href="{{ route('orders.items.index', $order->id) }}"> - Add Items</a>
                            <a href="{{ route('orders.edit', $order->id) }}"> - Edit</a>
                        </small>
                    </h6>
                </div>
                <div class="card-body">

                    <h6>
                        Order details
                    </h6>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h6><b>Number:</b> {{ $order->number }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6>
                                <b>Print:</b>
                                <a href="{{ route('orders.invoice', $order->id)  }}">
                                    Invoice
                                </a>
                            </h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>User:</b> {{ $order->user->name }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Status:</b> {{ $order->getStatus() }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Page:</b> {{ $order->page->name }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Delivery Address:</b> {{ $order->delivery_address }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Delivery Price:</b> {{ $order->delivery_price }} IQD</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Items:</b> {{ $order->items->count() }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Total:</b> {{ $order->total() }} IQD</h6>
                        </li>
                    </ul>

                    <div class="mt-3"></div>

                    <h6>Customer details</h6>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h6><b>Name:</b> {{ $order->customer_name }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Primary Phone:</b> {{ $order->customer_primary_phone }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6><b>Secondary Phone:</b> {{ $order->customer_secondary_phone }}</h6>
                        </li>
                        <li class="list-group-item">
                            <h6>
                                <b>Profile Link:</b>
                                <a href="{{ $order->customer_profile_link }}">
                                    {{ $order->customer_profile_link }}
                                </a>
                            </h6>
                        </li>
                    </ul>

                    <div class="mt-3"></div>
                    @if($order->hasForwarder())

                        <h6>Forwarder details</h6>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <h6><b>Forwarder:</b> {{ $order->forwarder->name }}</h6>
                            </li>

                            <li class="list-group-item">
                                <h6><b>Forwarder Location:</b> {{ $order->forwarderLocation->name  }}</h6>
                            </li>
                            <li class="list-group-item">
                                <h6><b>Forwarder Status:</b> {{ $order->forwarderStatus->name }}</h6>
                            </li>
                            <li class="list-group-item">
                                <h6><b>Forwarder Id:</b> {{ $order->forwarder_order_id }}</h6>
                            </li>

                            <li class="list-group-item">
                                <h6>
                                    <b>Last
                                        Refresh:</b> {{ $order->forwarder_refresh_timestamp ? $order->forwarder_refresh_timestamp->format('d-m-Y / h:i A') : null }}
                                </h6>
                            </li>
                            @if(in_array($order->status, [\App\Models\Order::STATUS_FORWARDER_ERROR_SENDING, \App\Models\Order::STATUS_FORWARDER_NO_STATUS]))
                                <li class="list-group-item">
                                    <button class="btn btn-sm btn-primary" wire:click="sendToForwarder()">
                                        Send to forwarder
                                    </button>
                                </li>
                            @else
                                <li class="list-group-item">
                                    <button class="btn btn-sm btn-primary" wire:click="refreshWithForwarder()">
                                        Refresh with forwarder
                                    </button>
                                </li>
                            @endif
                        </ul>

                    @endif

                    <div class="mt-3"></div>
                    @if($order->items->count())

                        <h6>Item Details</h6>

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
                                                    <img src="{{ $item->getFirstMediaUrl('images') }}" alt="Item Image"
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
                                <tr>
                                    <td colspan="6"></td>
                                    <td><b>Total:</b> {{ number_format($order->total()) }} IQD</td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
