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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->dateTime('lastChangeDate');
            $table->string('supplierArticle', 75);
            $table->string('techSize', 30);
            $table->string('barcode', 30);
            $table->float('totalPrice');
            $table->float('discountPercent');
            $table->boolean('isSupply');
            $table->boolean('isRealization');
            $table->float('promoCodeDiscount');
            $table->string('warehouseName', 50);
            $table->string('countryName', 200);
            $table->string('oblastOkrugName', 200);
            $table->string('regionName', 200);
            $table->unsignedBigInteger('incomeID');
            $table->string('saleID', 15);
            $table->unsignedBigInteger('odid');
            $table->float('spp');
            $table->float('forPay');
            $table->float('finishedPrice');
            $table->float('priceWithDisc');
            $table->unsignedBigInteger('nmId');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->integer('IsStorno');
            $table->string('gNumber', 50);
            $table->string('sticker');
            $table->string('srid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
