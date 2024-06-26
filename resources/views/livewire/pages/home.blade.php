<div>


    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Welcome, {{ auth()->user()->name }}</h1>
    </div>

    <div class="row">

        <div class="col-12">
            <img src="{{ asset('img/' . config('envAccess.LOGO_SMALL')) }}" alt="Website Logo" width="32px">
            <span>&nbsp; This dashboard allows you to to easily manage {{ strtolower( env('APP_NAME'))  }} orders.</span>
        </div>

    </div>

    @role('admin')
    <div class="mt-3"></div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Hyperpost statistics
                    </h6>
                </div>
                <div class="card-body">

                    <form>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="hyperpost_page_id">Page</label>
                                <select wire:model="hyperpost_page_id" class="form-control"
                                        wire:change="filterHyperpostStats()" id="hyperpost_page_id">
                                    <option value="0">All</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">

                            <div class="col-md-6">
                                <label for="from_date">From</label>
                                <input type="date" class="form-control" id="from_date"
                                       wire:model="hyperpost_from_date"
                                       wire:change="filterHyperpostStats()">
                            </div>

                            <div class="col-md-6 mt-md-0 mt-2">
                                <label for="to_date">To</label>
                                <input type="date" class="form-control" id="to_date" wire:model="hyperpost_to_date"
                                       wire:change="filterHyperpostStats()">
                            </div>
                        </div>
                    </form>

                    <div class="mt-3"></div>
                    <h6>Order total by hyperpost statistics.</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <td class="text-center">#</td>
                                <td>Status</td>
                                <td>Count</td>
                                <td>Total</td>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($hyperpostOrderData as $data)

                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $data['status_name']  }}</td>
                                    <td>{{ $data['orders_count']  }}</td>
                                    <td>{{ number_format($data['orders_total'])  }} IQD</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6">There is no data.</td>
                                </tr>

                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endrole


</div>
