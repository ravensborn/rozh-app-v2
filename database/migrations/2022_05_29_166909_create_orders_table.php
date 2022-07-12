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
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->string('number');
            $table->integer('status')->default(\App\Models\Order::STATUS_DEFAULT);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->unsignedBigInteger('page_id');
            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
                ->onDelete('restrict');

            //Which forwarder?
            $table->unsignedBigInteger('forwarder_id')
                ->nullable();
            $table->foreign('forwarder_id')
                ->references('id')
                ->on('forwarders')
                ->onDelete('restrict');

            //Forwarder location.
            $table->unsignedBigInteger('forwarder_location_id')
                ->nullable();
            $table->foreign('forwarder_location_id')
                ->references('location_id')
                ->on('forwarder_locations')
                ->onDelete('restrict');

            //Forwarder status.
            $table->unsignedBigInteger('forwarder_status_id')
            ->nullable();
            $table->foreign('forwarder_status_id')
                ->references('status_id')
                ->on('forwarder_statuses')
                ->onDelete('restrict');

            //Forwarder order id.
            $table->string('forwarder_order_id')
                ->nullable();

            //Forwarder refresh timestamp.
            $table->timestamp('forwarder_refresh_timestamp')
                ->nullable();

            $table->string('delivery_address')->nullable();
            $table->bigInteger('delivery_price')->default(0);

            $table->string('customer_name');
            $table->string('customer_primary_phone');
            $table->string('customer_secondary_phone')->nullable();
            $table->string('customer_profile_link')->nullable();


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
        Schema::dropIfExists('orders');
    }
};
