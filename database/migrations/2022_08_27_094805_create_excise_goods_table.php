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
        Schema::create('excise_goods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('wb_id');
            $table->string('inn');
            $table->float('finishedPrice');
            $table->integer('operationTypeId');
            $table->dateTime('fiscalDt');
            $table->integer('docNumber');
            $table->string('fnNumber');
            $table->string('regNumber');
            $table->string('excise');
            $table->dateTime('date', 3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('excise_goods');
    }
};
