<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        User List
                    </h6>
                </div>
                <div class="card-body">

                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-icon-split mb-3">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-plus"></i>
                                        </span>
                        <span class="text">
                            New User
                        </span>
                    </a>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <td class="text-center">#</td>
                                <td>Name</td>
                                <td>E-mail Address</td>
                                <td>Roles</td>
                                <td>Joined Date</td>
                                <td>Actions</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ ucwords($user->name) }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="{{ route('users.edit', $user->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if(auth()->user()->id == $user->id)

                                            <button class="btn btn-secondary btn-sm disabled" disabled>
                                                <i class="fa fa-trash"></i>
                                            </button>

                                        @else

                                            <button class="btn btn-danger btn-sm" wire:click="triggerDeleteUser({{ $user->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
