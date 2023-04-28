<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pembayarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('jasa_barang_id')->nullable()->index();
            $table->char('kategori', '10')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('harga')->nullable();
            $table->uuid('pembayarans_id')->nullable()->index();
            $table->foreign('pembayarans_id')->references('id')->on('pembayarans')->onDelete('cascade');
            $table->uuid('users_id')->nullable()->index();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('detail_pembayarans');
    }
}
