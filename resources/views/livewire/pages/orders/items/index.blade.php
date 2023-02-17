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
                        <small>
                            <a href="{{ route('orders.show', $order->id) }}"> - Show Order Details</a>
                        </small>
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

                                <select name="size" id="size" class="form-control" wire:model.lazy="size">
                                    <option value="Free Size">Free Size</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                    <option value="XXXL">XXXL</option>
                                    <option value="4XL">4XL</option>
                                    <option value="5XL">5XL</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                    <option value="60">60</option>
                                </select>
                                @error('size')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="color">Color</label>
                                <select value="color" id="color" class="form-control" wire:model.lazy="color">
                                    <option value="Same as picture">Same as picture</option>
                                    <option value="Red">Red</option>
                                    <option value="Green">Green</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Black">Black</option>
                                    <option value="Brown">Brown</option>
                                    <option value="White">White</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Orange">Orange</option>
                                    <option value="Yellow">Yellow</option>
                                    <option value="Apricot">Apricot</option>
                                    <option value="Khaki">Khaki</option>
                                    <option value="Wind Red">Wind Red</option>
                                    <option value="Leopard">Leopard</option>
                                    <option value="Purple">Purple</option>
                                    <option value="Light Blue">Light Blue</option>
                                    <option value="Navy Blue">Navy Blue</option>
                                </select>
                                @error('color')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="price">Price (IQD)</label>
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

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="code">Code</label>
                                <input type="text" id="code" class="form-control" wire:model.lazy="code">
                                @error('code')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @if($foundInReturnedList > 0)
                                   <div class="text-success mt-1">Found in return list: {{ $foundInReturnedList }}</div>
                                @endif
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

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Color & Size</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
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
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->color . ' - ' . $item->size }}</td>
                                    <td>{{ number_format($item->price) }} IQD</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->total()) }} IQD</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm"
                                                wire:click="triggerDeleteOrderItem({{ $item->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">There are no order items at the moment.</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="8"></td>
                                <td><b>Total:</b> {{ number_format( $order->total()) }} IQD</td>
                            </tr>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>


        </div>
    </div>

</div>
