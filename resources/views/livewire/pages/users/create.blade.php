<div>

    <div class="row">

        <div class="col-md-5 col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        New User
                    </h6>
                </div>
                <div class="card-body">

                    <form wire:submit.prevent="submitUser">

                        <h5>User Details:</h5>
                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <label for="name">Name</label>
                                <input type="text" id="name" class="form-control"
                                       wire:model.lazy="name">
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <label for="email">E-Mail</label>
                                <input type="email" id="email" class="form-control"
                                       wire:model.lazy="email">
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <label for="role_name">Role</label>
                                <select id="role_name" class="form-control" wire:model.lazy="role_name">
                                    <option value="">-- Select a role --</option>
                                    <option value="admin">admin</option>
                                    <option value="data-entry">data-entry</option>
                                </select>
                                @error('role_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($role_name == 'data-entry')
                            <div class="col-12 mt-3">
                                <label for="limited_to_page_id">Limited to page</label>
                                <select id="limited_to_page_id" class="form-control" wire:model.lazy="limited_to_page_id">
                                    <option value="">-- Select a page --</option>
                                    @foreach(\App\Models\Page::all() as $page)
                                        <option value="{{ $page->id }}">{{ $page->name }}</option>
                                    @endforeach
                                </select>
                                @error('limited_to_page_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            <div class="col-12 mt-3">
                                <label for="password">Password</label>
                                <input type="password" id="password" class="form-control"
                                       wire:model.lazy="password">
                                @error('password')
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
