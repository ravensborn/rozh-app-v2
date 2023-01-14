<div>

    <div class="row">
        <div class="col-6 col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Expenses
                    </h6>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('expense-items.index') }}">Go to expenses page</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Order Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('orders.statistic') }}">Go to statistics page</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Most profitable items (MPI)
                    </h6>
                </div>
                <div class="card-body">

                    <form>

                        <div class="row mb-1">
                            <div class="col-md-12">

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           wire:model="mpi_most_sold_stats_enabled" value=""
                                           id="mbi_filter_enabled">
                                    <label class="form-check-label" for="mbi_filter_enabled">
                                        Enable MPI
                                    </label>
                                </div>

                            </div>
                        </div>

                        @if($mpi_most_sold_stats_enabled)

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="mpi_filter_page_id">Page</label>
                                    <select wire:model="mpi_filter_page_id" class="form-control"
                                            wire:change="MpiGetMostSoldItemList()"
                                            id="mpi_filter_page_id">
                                        <option value="0">All</option>
                                        @foreach($pages as $page)
                                            <option value="{{ $page->id }}">{{ $page->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">

                                <div class="col-md-6">
                                    <label for="mpi_filter_from_date">From</label>
                                    <input type="date" class="form-control" id="mpi_filter_from_date"
                                           wire:model="mpi_filter_from_date"
                                           wire:change="MpiGetMostSoldItemList()">
                                </div>

                                <div class="col-md-6 mt-md-0 mt-2">
                                    <label for="mpi_filter_to_date">To</label>
                                    <input type="date" class="form-control" id="mpi_filter_to_date"
                                           wire:model="mpi_filter_to_date"
                                           wire:change="MpiGetMostSoldItemList()">
                                </div>
                            </div>
                        @endif
                    </form>
                    @if($mpi_most_sold_stats_enabled)
                        <div class="mt-3 mb-1">Most sold items grouped by code:</div>
                        <ul class="list-group">
                            @forelse($mpi_topSoldItemsByCodeArray as $item)
                                <li class="list-group-item">
                                    <div>
                                        Item code: {{ $item['code'] }}
                                    </div>
                                    <div>
                                        Sold quantity: {{ $item['count'] }}
                                    </div>
                                </li>
                            @empty
                                Nothing to show currently.
                            @endforelse
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Profit Calculator (PC)
                    </h6>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="pc_enabled" value=""
                                       id="pc_filter_enabled">
                                <label class="form-check-label" for="pc_filter_enabled">
                                    Enable Profit Calculator
                                </label>
                            </div>

                        </div>
                    </div>

                    @if($pc_enabled)
                        <div class="row mt-3">
                            <div class="col-12 col-md-6 order-1 mt-3 mt-md-0">

                                <div wire:loading wire:target="processProfitCalculator">
                                    Loading Data...
                                </div>

                                <div wire:loading.remove>
                                    <ul class="list-group">

                                        <li class="list-group-item">
                                            <span>Orders: {{ $pc_orders }} - {{ number_format($pc_orders_worth) }} IQD</span>
                                        </li>

                                        <li class="list-group-item">
                                            <span>Order Items: {{ $pc_order_items }}</span>
                                        </li>

                                        <li class="list-group-item">
                                            <span>Expenses: {{ $pc_expenses }} - {{ number_format($pc_expenses_worth) }} IQD</span>
                                        </li>

                                        <li class="list-group-item">

                                            <span class="d-inline">Profit Per Order Item:</span>
                                            <input type="number" class="form-control form-control-sm w-auto d-inline"
                                                   wire:model.debounce.2000ms="pc_profitPerOrderItem">
                                            <span class="d-inline"> IQD</span>
                                        </li>

                                        <li class="list-group-item">
                                            <span>Profit: {{ number_format($pc_profit) }} IQD</span>
                                        </li>

                                        <li class="list-group-item">
                                    <span>
                                        Profit Minus
                                        Expenses: {{ number_format(($pc_profit) - $pc_expenses_worth) }}
                                        IQD
                                    </span>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                            <div class="col-12 col-md-6 order-0">

                                <div class="row mt-3 mt-md-0">
                                    <div class="col-md-6">
                                        <label for="pc_page_id">Page</label>
                                        <select wire:model="pc_page_id" class="form-control" id="page_id"
                                                wire:change="processProfitCalculator()">
                                            <option value="0">All</option>
                                            @foreach($pages as $page)
                                                <option value="{{ $page->id }}">{{ $page->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 col-12">
                                        <label for="pc_from_date">From</label>
                                        <input type="date" class="form-control" id="pc_from_date"
                                               wire:model="pc_from_date"
                                               wire:change="processProfitCalculator()">
                                    </div>

                                    <div class="col-md-6 col-12 mt-3 mt-md-0">
                                        <label for="pc_to_date">To</label>
                                        <input type="date" class="form-control" id="pc_to_date" wire:model="pc_to_date"
                                               wire:change="processProfitCalculator()">
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
