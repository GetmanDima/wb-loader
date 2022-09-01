<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('lastChangeDate');
            $table->string('supplierArticle', 75);
            $table->string('techSize', 30);
            $table->string('barcode', 30);
            $table->integer('quantity');
            $table->boolean('isSupply');
            $table->boolean('isRealization');
            $table->integer('quantityFull');
            $table->integer('quantityNotInOrders');
            $table->string('warehouseName', 50);
            $table->integer('inWayToClient');
            $table->integer('inWayFromClient');
            $table->unsignedBigInteger('nmId');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->integer('daysOnSite');
            $table->string('brand', 50);
            $table->string('SCCode', 50);
            $table->unsignedBigInteger('warehouse');
            $table->float('Price');
            $table->integer('Discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
