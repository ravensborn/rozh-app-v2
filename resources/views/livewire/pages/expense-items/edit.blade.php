<div>

    <div class="row">

        <div class="col-md-5 col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Editing User {{ $expenseItem->name }}
                    </h6>
                </div>
                <div class="card-body">

                    <form wire:submit.prevent="updateExpenseItem">

                        <h5>Expense Item Details:</h5>
                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <label for="title">Title</label>
                                <input type="text" id="title" class="form-control"
                                       wire:model.lazy="title">
                                @error('title')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="amount">Amount</label>
                                <input type="number" id="amount" class="form-control"
                                       wire:model.lazy="amount">
                                @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" class="form-control"
                                       wire:model.lazy="quantity">
                                @error('quantity')
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

                        <div class="mt-3">
                            <a href="{{ route('expense-items.index') }}">Click here to go back to all expenses list.</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
