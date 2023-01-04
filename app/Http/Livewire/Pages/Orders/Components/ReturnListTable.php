<?php

namespace App\Http\Livewire\Pages\Orders\Components;

use App\Models\ReturnedItem;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

use Jantinnerezo\LivewireAlert\LivewireAlert;


class ReturnListTable extends DataTableComponent
{
    use LivewireAlert;

    protected $listeners = [
        'refreshed-items' => '$refresh',
        'deleteItem',
        'refresh-items' => '$refresh',
    ];

    protected $model = ReturnedItem::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableAttributes([
            'class' => 'table-sm text-center table-bordered',
        ]);
        $this->setDefaultSort('created_at', 'desc');

    }

    public function columns(): array
    {
        return [

            Column::make('Code', 'code')->format(function ($value, $item) {
//                    return $value->format('Y-m-d / h:i A');
                if($item->hasMedia('images')) {
                    return '<img width="64" class="img-thumbnail" src="' . $item->getFirstMediaUrl('images')  . '">';
                }
                return 'no-image';

            })->searchable()
                ->html(),

            Column::make('Code', 'code')
                ->searchable(),

            Column::make('Price', 'price')
                ->format(function ($value, $row) {
                    return number_format($value) . ' IQD';
                })->html(),

            Column::make('Quantity', 'quantity')
                ->searchable(),

            Column::make('Total', 'id')
                ->format(function ($value, $row) {
                    return number_format($row->quantity * $row->price) . ' IQD';
                })->html(),

            Column::make('Size', 'size')
                ->searchable(),

            Column::make('Color', 'color')
                ->searchable(),

            Column::make('Page', 'page.name')
                ->searchable(),

            Column::make('Created Date', 'created_at')
                ->format(function ($value) {
                    return $value->format('Y-m-d / h:i A');
                })->searchable()
                ->html(),

            Column::make('Updated Date', 'updated_at')
                ->format(function ($value) {
                    return $value->format('Y-m-d / h:i A');
                })->searchable()
                ->html(),

            Column::make('Actions', 'id')->format(function ($value, $row, $column) {

                $div = "<div class='d-flex justify-content-center'>";
                $closeDiv = "</div>";
                $decreaseBtn = '<a wire:click="addQuantity('. $value .','."'". 'i' ."'".')" class="btn btn-info btn-sm"><span class="icon"> <i class="fas fa-plus"></i></span></a>';
                $increaseBtn = '<a wire:click="addQuantity('. $value .','."'". 'd' ."'".')" class="btn btn-info btn-sm"><span class="icon"> <i class="fas fa-minus"></i></span></a>';


                return $div . $decreaseBtn . '&nbsp;' . $increaseBtn  . $closeDiv;
            })->html(),

            Column::make('Options', 'id')->format(function ($value, $row, $column) {

                $div = "<div class='d-flex justify-content-center'>";
                $closeDiv = "</div>";
                $deleteBtn = '<a wire:click="triggerDeleteItem('. $value .')" class="btn btn-danger btn-sm"><span class="icon"> <i class="fas fa-trash"></i></span></a>';

                return $div . $deleteBtn  . $closeDiv;
            })->html(),
        ];
    }

    public function addQuantity($id, $operation) {

        $returnItem = ReturnedItem::find($id);
        $value = $returnItem->quantity;
        if($operation == 'i') {
            $value++;
        } else {
            $value--;
        }

        $returnItem->update([
            'quantity' => $value
        ]);

        $this->alert('success', 'Successfully updated item.');
    }

    public $itemToBeDeleted = '';

    public function triggerDeleteItem(ReturnedItem $item)
    {
        $this->confirm('Are you sure that you want to delete this item?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'No',
            'confirmButtonText' => 'Yes',
            'onConfirmed' => 'deleteItem'
        ]);

        $this->itemToBeDeleted = $item;
    }

    public function deleteItem()
    {

        $this->itemToBeDeleted->delete();

        $this->alert('success', 'Item successfully deleted.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

}
