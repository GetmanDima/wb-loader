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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedBigInteger('realizationreport_id');
            $table->string('suppliercontract_code')->nullable();
            $table->unsignedBigInteger('rrd_id');
            $table->unsignedBigInteger('gi_id');
            $table->string('subject_name')->nullable();
            $table->unsignedBigInteger('nm_id')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('sa_name')->nullable();
            $table->string('ts_name')->nullable();
            $table->string('barcode');
            $table->string('doc_type_name');
            $table->integer('quantity');
            $table->float('retail_price');
            $table->float('retail_amount');
            $table->float('sale_percent');
            $table->float('commission_percent');
            $table->string('office_name')->nullable();
            $table->string('supplier_oper_name');
            $table->dateTime('order_dt');
            $table->dateTime('sale_dt');
            $table->dateTime('rr_dt');
            $table->unsignedBigInteger('shk_id');
            $table->float('retail_price_withdisc_rub');
            $table->float('delivery_amount');
            $table->float('return_amount');
            $table->float('delivery_rub');
            $table->string('gi_box_type_name');
            $table->integer('product_discount_for_report');
            $table->integer('supplier_promo');
            $table->unsignedBigInteger('rid');
            $table->float('ppvz_spp_prc');
            $table->float('ppvz_kvw_prc_base');
            $table->float('ppvz_kvw_prc');
            $table->float('ppvz_sales_commission');
            $table->float('ppvz_for_pay');
            $table->float('ppvz_reward');
            $table->float('ppvz_vw');
            $table->float('ppvz_vw_nds');
            $table->unsignedBigInteger('ppvz_office_id');
            $table->unsignedBigInteger('ppvz_supplier_id');
            $table->string('ppvz_supplier_name');
            $table->string('ppvz_inn');
            $table->string('declaration_number');
            $table->string('sticker_id');
            $table->string('site_country');
            $table->integer('penalty');
            $table->integer('additional_payment');
            $table->string('srid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
