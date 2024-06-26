<?php

namespace App\Http\Livewire\Pages\Orders\Components;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Order;

use Jantinnerezo\LivewireAlert\LivewireAlert;


class OrdersTable extends DataTableComponent
{
    use LivewireAlert;

    public $orderToBeDeleted = null;

    protected $model = Order::class;

    protected $listeners = [
        'deleteOrder',
        'refresh-orders' => '$refresh',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableAttributes([
            'class' => 'table-sm text-center table-bordered',
        ]);
        $this->setDefaultSort('id', 'desc');

    }

    public function builder(): Builder
    {

        $query = Order::query();
        $user = auth()->user();

        if ($user->hasRole('data-entry')) {
            if ($user->hasRole('limited_to_page')) {

                $page_id = $user->getLimitedByPageId();

                $query->where('page_id', $page_id);

            }
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'number')
                ->searchable(),
            Column::make('Date', 'created_at')
                ->format(function ($value) {
//                    return $value->format('Y-m-d / h:i A');
                    return '<span title="' . $value->diffForHumans() . '">' . $value->format('Y-m-d') . '<span>';
                })->searchable()
                ->html(),
            Column::make('Status', 'status')
                ->format(function ($value, $row, $column) {
                    return $row->getStatus();
                })->searchable(),
            Column::make('Primary Phone', 'customer_primary_phone')
                ->searchable(),
//            Column::make('Page', 'page.name')
//                ->searchable(),
            Column::make('User', 'user.name')
                ->searchable(),
            Column::make('Total', 'id')
                ->format(function ($value, $row, $column) {
                    return number_format($row->total()) . ' IQD';
                })->searchable(),
//            Column::make('Address', 'delivery_address')
//                ->searchable(),
            Column::make('Forwarder', 'forwarder.name')->searchable(),
            Column::make('Items', 'id')->format(function ($value, $row) {

                //Todo: Prevent while order is sent.
                if (in_array($row->status, [
                    Order::STATUS_DEFAULT,
                    Order::STATUS_FORWARDER_NO_STATUS,
                    Order::STATUS_FORWARDER_ERROR_SENDING
                ])) {
                    //Not Sent
                    return '<a class="btn btn-primary btn-sm" href="' . route('orders.items.index', ['order' => $value]) . '"><span class="icon"><i class="fas fa-cart-plus"></i></span></a>';
                } else {
                    //Sent
                    return '<a class="btn btn-secondary btn-sm"><span class="icon"><i class="fas fa-cart-plus"></i></span></a>';
                }

            })->html(),
            Column::make('Options', 'id')->format(function ($value, $row, $column) {

                $div = "<div class='d-flex justify-content-center'>";
                $closeDiv = "</div>";
                $showBtn = '<a href="' . route('orders.show', $value) . '" class="btn btn-info btn-sm"><span class="icon"> <i class="fas fa-file"></i></span></a>';

                //Todo: Prevent while order is sent.
                if (in_array($row->status, [
                    Order::STATUS_DEFAULT,
                    Order::STATUS_FORWARDER_NO_STATUS,
                    Order::STATUS_FORWARDER_ERROR_SENDING
                ])) {
                    //Not Sent

                    $deleteBtn = '<a class="btn btn-danger btn-sm" wire:click="triggerDeleteOrder(' . $value . ')"><span class="icon"> <i class="fas fa-trash"></i></span></a>';
                    $editBtn = '<a href="' . route('orders.edit', $value) . '" class="btn btn-warning btn-sm"><span class="icon"> <i class="fas fa-edit"></i></span></a>';

                } else {
                    //Sent

                    $deleteBtn = '<a class="btn btn-secondary btn-sm disabled"><span class="icon"> <i class="fas fa-trash"></i></span></a>';
                    $editBtn = '<a class="btn btn-secondary btn-sm disabled"><span class="icon"> <i class="fas fa-edit"></i></span></a>';
                }

                return $div . $showBtn . '&nbsp;' . $editBtn . '&nbsp;' . $deleteBtn . $closeDiv;
            })->html(),
        ];
    }

    public function triggerDeleteOrder(Order $order)
    {
        $this->confirm('Are you sure that you want to delete this order?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'No',
            'onConfirmed' => 'deleteOrder'
        ]);

        $this->orderToBeDeleted = $order;
    }

    public function deleteOrder()
    {
        //Todo: Prevent while order is sent.
        if (in_array($this->orderToBeDeleted->status, [
            Order::STATUS_DEFAULT,
            Order::STATUS_FORWARDER_NO_STATUS,
            Order::STATUS_FORWARDER_ERROR_SENDING
        ])) {
            //Not Sent
        } else {
            //Sent
            abort(401, "You cannot do this action.");
        }

        $this->orderToBeDeleted->delete();
        $this->alert('success', 'Order successfully deleted.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

}
