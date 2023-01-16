<div>
    <div class="row">

        <div class="col-12 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Filter Options
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <label for="phone_number">Search by phone number</label>
                                <input type="text" class="form-control" placeholder="0750-123-4567"
                                       wire:model="phone_number" autofocus>
                                @error('phone_number')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>

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
                                <div><b>Order Number: </b>{{ $order->number }}</div>
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
                                        <a href="{{ 'https://hyperpostbackup.com/customer/add/track/print/' . $order->forwarder_order_id}}">
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
                                        </tbody>

                                    </table>
                                </div>
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
                        <p>
                            No orders to show.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforelse


</div>
