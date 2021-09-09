<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BizamiCreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bizami_warehouse', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_id');
        });

        Schema::create('bizami_warehouse_state', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_id');
            $table->string('product_id');
            $table->integer('qty', false, true);
            $table->integer('qty_reserved', false, true);
            $table->integer('qty_ordered', false, true);
            $table->string('status');
            $table->integer('norm', false, true); // normatyw
        });

        Schema::create('bizami_product', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->collation("utf8_general_ci")->unique();
            $table->string('name');
            $table->string('catalog_number');
            $table->string('provider');
            $table->string('producent');
            $table->string('catalog_group');
            $table->decimal('gross_weight')->nullable();
            $table->decimal('gross_volume')->nullable();
            $table->string('category');
            $table->smallInteger('is_active')->default(1);
            $table->decimal('value');
        });

        Schema::create('bizami_sales_document', function (Blueprint $table) {
            $table->id();
            $table->string('document_id');
            $table->date('date');
            $table->string('type');
            $table->string('product_id');
            $table->integer('qty');
            $table->decimal('value');
            $table->string('warehouse_id');
            $table->smallInteger('is_investment')->default(0);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bizami_sales_document');
        Schema::dropIfExists('bizami_product');
        Schema::dropIfExists('bizami_warehouse_state');
        Schema::dropIfExists('bizami_warehouse');
    }
}
