<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Expenses and Profit
                    </h6>
                </div>
                <div class="card-body">

                    <form>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <input type="checkbox" value="" id="filter_on" wire:model="filter_on">
                                <label for="filter_on">
                                    Toggle Filter By Date
                                </label>
                            </div>

                        </div>
                        @if($filter_on)
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <label for="from_date">From</label>
                                    <input type="date" class="form-control" id="from_date" wire:model="from_date"
                                           wire:change="filterExpensesByDate()">
                                </div>

                                <div class="col-md-6 col-12 mt-3 mt-md-0">
                                    <label for="to_date">To</label>
                                    <input type="date" class="form-control" id="to_date" wire:model="to_date"
                                           wire:change="filterExpensesByDate()">
                                </div>
                            </div>
                        @endif
                    </form>

                    <hr>

                    <div wire:loading wire:target="filterExpensesByDate">
                        Loading Data...
                    </div>

                    <div wire:loading.remove>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span>Total Expense Amount: {{ number_format($totalExpensesAmount) }} IQD</span>
                            </li>

                            <li class="list-group-item">
                                <span>Number of Orders: {{ number_format($numberOfOrders) }}</span>
                            </li>

                            <li class="list-group-item">

                                <span class="d-inline">Profit Per Order:</span>
                                <input type="number" class="form-control form-control-sm w-auto d-inline"
                                       wire:model="profitPerOrder">
                                <span class="d-inline"> IQD</span>
                            </li>

                            <li class="list-group-item">
                                <span>Total Profit: {{ number_format($numberOfOrders * $profitPerOrder) }} IQD</span>

                            </li>
                            <li class="list-group-item">
                                <span>Total Profit Minus
                                    Expenses: {{ number_format(($numberOfOrders * $profitPerOrder) - $totalExpensesAmount) }}
                                    IQD</span>

                            </li>
                        </ul>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Expense Item List
                    </h6>
                </div>
                <div class="card-body">

                    <a href="{{ route('expense-items.create') }}" class="btn btn-primary btn-icon-split mb-3">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-plus"></i>
                                        </span>
                        <span class="text">
                            New Expense Item
                        </span>
                    </a>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <td class="text-center">#</td>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($expenseItems as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ number_format($item->amount) }} IQD</td>
                                    <td>{{ number_format($item->quantity) }} IQD</td>
                                    <td>{{ number_format($item->quantity * $item->amount) }} IQD</td>
                                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="text-truncate" title="{{ $item->note }}">
                                            {{ $item->note }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="btn btn-warning btn-sm"
                                           href="{{ route('expense-items.edit', $item->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <button class="btn btn-danger btn-sm"
                                                wire:click="triggerDeleteExpenseItem({{ $item->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No expense items.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div>
                            {{ $expenseItems->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
