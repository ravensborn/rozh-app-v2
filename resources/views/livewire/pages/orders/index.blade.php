<div>

    <div class="row">

        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Order Details
                    </h6>
                </div>
                <div class="card-body">

                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-icon-split mb-3">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-plus"></i>
                                        </span>
                        <span class="text">
                            New Order
                        </span>
                    </a>

                    <livewire:pages.orders.components.orders-table/>
                </div>
            </div>
        </div>
    </div>
</div>
