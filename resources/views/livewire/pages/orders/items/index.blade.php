<div>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 text-gray-800">Order {{ $order->number }} items</h4>
    </div>


    <div class="row">
        <div class="col-md-12">


            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        New Item
                    </h6>
                </div>
                <div class="card-body">

                    <form wire:submit.prevent="submitOrderItem">

                        <h5>Order Details:</h5>
                        <hr>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="name">Name</label>
                                <input type="text" id="name" class="form-control" wire:model.lazy="name">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="image">Image</label>
                                <input type="file" id="image" class="form-control-file" wire:model.lazy="image"
                                       accept="image/*">
                                @error('image')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror

                                @if ($image)
                                    <div class="mt-1">
                                        <img src="{{ $image->temporaryUrl() }}" style="width: 200px; height: auto;"
                                             class="img-fluid border">
                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col-md-6">
                                <label for="size">Size</label>
                                <input type="text" id="size" class="form-control" wire:model.lazy="size">
                                @error('size')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="color">Color</label>
                                <input type="text" id="color" class="form-control" wire:model.lazy="color">
                                @error('color')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="price">Price</label>
                                <input type="number" id="price" class="form-control" wire:model.lazy="price">
                                @error('price')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" class="form-control" wire:model.lazy="quantity">
                                @error('quantity')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>

                        <div class="mt-3">
                            <hr>
                            <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-check"></i>
                                        </span>
                                <span class="text">Save</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>


            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Order Items
                    </h6>
                </div>
                <div class="card-body">

                    <table class="table">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Color & Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
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
                                <td>
                                    <button class="btn btn-danger btn-sm" wire:click="triggerDeleteOrderItem({{ $item->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">There are no order items at the moment.</td>
                            </tr>
                        @endforelse
                            <td colspan="7"></td>
                            <td><b>Total:</b> {{ $order->total() }} IQD</td>
                        </tr>
                    </table>


                </div>
            </div>


        </div>
    </div>

</div>