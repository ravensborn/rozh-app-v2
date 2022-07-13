<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->string('name')
                ->comment('The product name at the moment of buying.')
                ->nullable();

            $table->string('color')
                ->nullable();
            $table->string('size')
                ->nullable();

            $table->integer('quantity');
//            $table->decimal('price', 15, 4);
            $table->integer('price');

            $table->string('link')
                ->nullable();

            $table->longText('properties')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
