<div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Block new phone number
                    </h6>
                </div>
                <div class="card-body">
                    <form action="">

                        <div class="row">
                            <div class="col-6">
                                <label for="phone">Phone number</label>
                                <input type="text" class="form-control form-control-sm" wire:model="phone">
                                @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-danger btn-sm" type="button" wire:click.prevent="addNewItem()">Block</button>
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
                        List of blocked phone numbers
                    </h6>
                </div>
                <div class="card-body">

                    <livewire:pages.components.block-list-table/>

                </div>
            </div>
        </div>
    </div>


</div>
