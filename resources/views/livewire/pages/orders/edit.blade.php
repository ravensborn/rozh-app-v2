<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Edit Order {{ $order->number }}
                    </h6>
                </div>
                <div class="card-body">

                    <form wire:submit.prevent="updateOrder">

                        <h5>Customer Details:</h5>
                        <hr>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="customer_name">Name</label>
                                <input type="text" id="customer_name" class="form-control" wire:model.lazy="customer_name">
                                @error('customer_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="customer_profile_link">Profile Link</label>
                                <input type="text" id="customer_profile_link" class="form-control" wire:model.lazy="customer_profile_link">
                                @error('customer_profile_link')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="customer_primary_phone">Primary Phone</label>
                                <input type="text" id="customer_primary_phone" class="form-control" wire:model="customer_primary_phone">
                                @error('customer_primary_phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_secondary_phone">Secondary Phone</label>
                                <input type="text" id="customer_secondary_phone" class="form-control"
                                       wire:model.lazy="customer_secondary_phone">
                                @error('customer_secondary_phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="mt-4"></div>

                        <h5>Order Details:</h5>
                        <hr>
                        <div class="row mt-3">

                            <div class="col-6">
                                <label for="page_id">Page</label>
                                <select id="page_id" class="form-control" wire:model.lazy="page_id">
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->name }}</option>
                                    @endforeach
                                </select>
                                @error('page_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="delivery_address">Delivery Address</label>
                                <input type="text" id="delivery_address" class="form-control" wire:model.lazy="delivery_address" @if($order->status != \App\Models\Order::STATUS_FORWARDER_NO_STATUS) disabled @endif>
                                @error('delivery_address')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="delivery_price">Delivery Price (IQD)</label>
                                <input type="text" id="delivery_price" class="form-control" wire:model.lazy="delivery_price">
                                @error('delivery_price')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>




                        <div class="mt-3">
                            <hr>
                            <button class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-check"></i>
                                        </span>
                                <span class="text">Save</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
