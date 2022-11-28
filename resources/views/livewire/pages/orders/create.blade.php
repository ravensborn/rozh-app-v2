<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Create Order
                    </h6>
                </div>
                <div class="card-body">

                    <form wire:submit.prevent="submitOrder">

                        <h5>Forwarder Details:</h5>
                        <hr>

                        <div class="row mt-3">
                            <div class="col-12 col-md-6">
                                <label for="forwarder_id">Forwarder</label>
                                <select id="forwarder_id" class="form-control" wire:model.lazy="forwarder_id">
                                    @foreach($forwarders as $forwarder)
                                        <option value="{{ $forwarder->id }}">{{ $forwarder->name }}</option>
                                    @endforeach
                                </select>
                                @error('forwarder_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label for="forwarder_location_id">Forwarder Location</label>
                                <select id="forwarder_location_id" class="form-control"
                                        wire:model.lazy="forwarder_location_id">
                                    @foreach($forwarderLocations as $forwarderLocation)
                                        <option value="{{ $forwarderLocation->location_id }}">{{ $forwarderLocation->name }}</option>
                                    @endforeach
                                </select>
                                @error('forwarder_location_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 col-md-6">
                                <label for="delivery_address">Delivery Address</label>
                                <input type="text" id="delivery_address" class="form-control"
                                       wire:model.lazy="delivery_address"
                                       @if($forwarder_id == \App\Models\Forwarder::NO_FORWARDER) readonly @endif>
                                @error('delivery_address')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label for="delivery_price">Delivery Price (IQD)</label>
                                <input type="text" id="delivery_price" class="form-control"
                                       wire:model.lazy="delivery_price"
                                       @if($forwarder_id == \App\Models\Forwarder::NO_FORWARDER) readonly @endif>
                                @error('delivery_price')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4"></div>

                        <h5>Customer Details:</h5>
                        <hr>

                        <div class="row mt-3">
                            <div class="col-12 col-md-6">
                                <label for="customer_name">Name</label>
                                <input type="text" id="customer_name" class="form-control"
                                       wire:model.lazy="customer_name">
                                @error('customer_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <label for="customer_profile_link">Profile Link</label>
                                <input type="text" id="customer_profile_link" class="form-control"
                                       wire:model.lazy="customer_profile_link">
                                @error('customer_profile_link')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 col-md-6">
                                <label for="customer_primary_phone">Primary Phone</label>
                                <input type="text" id="customer_primary_phone" class="form-control"
                                       wire:model="customer_primary_phone">
                                @error('customer_primary_phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @if($blockedPhoneError)
                                    <div class="text-danger mt-1">This phone number is blocked.</div>
                                @endif
                            </div>
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
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

                            <div class="col-12 col-md-6">
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
