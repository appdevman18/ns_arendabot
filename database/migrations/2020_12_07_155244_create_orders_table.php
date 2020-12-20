<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('price');
            $table->integer('distance');
            $table->string('game');
            $table->integer('duration');
            $table->integer('type')->default(0);
            $table->integer('status')->default(0);
            $table->bigInteger('costumer_id')->unsigned();
            $table->timestamps();

            $table->foreign('costumer_id')
                ->references('id')->on('costumers')
                ->onDelete('cascade');
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
}
