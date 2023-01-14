<div>


    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Total Expenses
                    </h6>
                </div>
                <div class="card-body">
                    {{ number_format($total) }} IQD
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
                                    <td>{{ $item->quantity}}</td>
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
