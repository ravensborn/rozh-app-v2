<?php

namespace App\Http\Livewire\Pages\Components;


use App\Models\BlockList;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

use Jantinnerezo\LivewireAlert\LivewireAlert;


class BlockListTable extends DataTableComponent
{
    use LivewireAlert;

    protected $listeners = [
        'refreshed-items' => '$refresh',
        'deleteItem',
        'refresh-items' => '$refresh',
    ];

    protected $model = BlockList::class;

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

            Column::make('Phone Number', 'phone')
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

            Column::make('Options', 'id')->format(function ($value, $row, $column) {

                $div = "<div class='d-flex justify-content-center'>";
                $closeDiv = "</div>";
                $deleteBtn = '<a wire:click="triggerDeleteItem('. $value .')" class="btn btn-warning btn-sm"><span class="icon"> <i class="fas fa-unlock"></i></span></a>';

                return $div . $deleteBtn  . $closeDiv;
            })->html(),
        ];
    }


    public $itemToBeDeleted = '';

    public function triggerDeleteItem(BlockList $item)
    {
        $this->confirm('Are you sure that you want to unblock this phone number?', [
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

        $this->alert('success', 'Phone number successfully unblocked.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

}
