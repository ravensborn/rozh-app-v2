<div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Overview
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                           Return List Items: {{ $returnListItems }}
                        </li>
                        <li class="list-group-item">
                            Return List Amount: {{ number_format($returnListItemsAmount) }} IQD
                        </li>
                        <li class="list-group-item" >
                            <a wire:click="calculateStatistics" href="#">Click to refresh</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Add new
                    </h6>
                </div>
                <div class="card-body">
                    <form action="">

                        <div class="row">
                            <div class="col-md-7 col-12">
                                <div class="row">
                                    <div class="col-4">
                                        <label for="code">Code</label>
                                        <input type="text" class="form-control" wire:model="code">
                                        @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-4">
                                        <label for="price">Price</label>
                                        <input type="number" class="form-control" wire:model="price">
                                        @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-4">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" wire:model="quantity">
                                        @error('quantity')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="row mt-2">

                                    <div class="col-6 col-md-4">
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

                                    <div class="col-6 col-md-4">
                                        <label for="color">Color</label>
                                        <select id="color" class="form-control" wire:model.lazy="color">
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
                                    <div class="col-12 col-md-4 mt-md-0 mt-2">
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
                            </div>
                            <div class="col-md-5 col-12">
                              <div class="row">
                                  <div class="col">
                                      <div class="row mt-md-0 mt-3">
                                          <div class="col">
                                              <label for="image">Image</label>
                                              <input type="file" id="image" class="form-control-file" wire:model.lazy="image"
                                                     accept="image/*">
                                              @error('image')
                                              <div class="text-danger">{{ $message }}</div>
                                              @enderror


                                          </div>
                                          <div class="col">
                                              @if ($image)
                                                  <div>
                                                      <img src="{{ $image->temporaryUrl() }}" style="width: 200px; height: auto;"
                                                           class="img-fluid border">
                                                  </div>
                                              @endif
                                          </div>
                                      </div>

                                  </div>
                              </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm" type="button" wire:click.prevent="addNewCode()">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Returned list of items
                    </h6>
                </div>
                <div class="card-body">

                    <livewire:pages.orders.components.return-list-table/>

                </div>
            </div>
        </div>
    </div>


</div>
