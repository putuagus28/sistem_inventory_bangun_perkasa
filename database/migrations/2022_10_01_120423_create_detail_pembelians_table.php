<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPembeliansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pembelians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pembelians_id')->nullable()->index();
            $table->foreign('pembelians_id')->references('id')->on('pembelians')->onDelete('cascade');
            $table->uuid('barangs_id')->nullable()->index();
            $table->foreign('barangs_id')->references('id')->on('barangs')->onDelete('cascade');
            $table->uuid('suppliers_id')->nullable()->index();
            $table->foreign('suppliers_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->integer('qty')->default(0);
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
        Schema::dropIfExists('detail_pembelians');
    }
}
