<div>
    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Filter Options
                    </h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="from_date">From</label>
                                <input type="date" class="form-control" id="from_date" wire:model="from_date"
                                       wire:change="filterByDate()">
                            </div>

                            <div class="col-md-6">
                                <label for="to_date">To</label>
                                <input type="date" class="form-control" id="to_date" wire:model="to_date"
                                       wire:change="filterByDate()">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="phone_number">Search by phone number</label>
                                <input type="text" class="form-control" placeholder="0750-123-4567"
                                       wire:model="phone_number">
                                @error('phone_number')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="forwarder_id">Forwarder</label>
                                <select id="forwarder_id" class="form-control" wire:model.lazy="forwarder_id">
                                    <option value="0">All</option>
                                    @foreach($forwarders as $forwarder)
                                        <option value="{{ $forwarder->id }}">{{ $forwarder->name }}</option>
                                    @endforeach
                                </select>
                                @error('forwarder_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($this->ifForwarderSelected())
                                <div class="col-3">
                                    <label for="forwarder_status_id">Forwarder Status</label>
                                    <select id="forwarder_status_id" class="form-control"
                                            wire:model.lazy="forwarder_status_id">
                                        <option value="-1">All</option>
                                        @foreach($forwarderStatuses as $forwarderStatus)
                                            <option value="{{ $forwarderStatus->status_id }}">{{ $forwarderStatus->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('forwarder_status_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-3">
                                    <label for="status">Order Status</label>
                                    <select id="status" class="form-control"
                                            wire:model.lazy="status">
                                        <option value="-1">All</option>
                                        @foreach(App\Models\Order::getStatusArray() as $status)
                                            <option value="{{$status['id'] }}">{{ $status['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Details
                    </h6>
                </div>
                <div class="card-body">
                    Total:
                    {{ number_format($orders->sum(function ($order) { return $order->total(); })) }}
                    IQD
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Orders
                    </h6>
                </div>
                <div class="card-body">

                    <div wire:loading>
                        Loading...
                    </div>

                    <div wire:loading.remove>
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>User</th>
                                <th>Customer Name</th>
                                <th>Primary Phone</th>
                                <th>Forwarder</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->number }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $order->getStatus() }}</td>
                                    <td>{{ number_format($order->total()) }} IQD</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_primary_phone }}</td>
                                    @if($order->hasForwarder())

                                        <td>{{ $order->forwarder->name }}</td>
                                    @else
                                        <td>No Forwarder</td>
                                    @endif
                                    <td>
                                        <a class="btn btn-info btn-sm" href="{{ route('orders.show', $order->id) }}">
                                            <i class="fa fa-file"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        No orders found, please choose different filters.
                                    </td>
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
