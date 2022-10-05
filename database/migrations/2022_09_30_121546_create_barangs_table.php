<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('jenis_id')->nullable()->index();
            $table->foreign('jenis_id')->references('id')->on('jenis')->onDelete('cascade');
            $table->string('nama_barang')->nullable();
            $table->char('ukuran', 10)->nullable();
            $table->char('satuan', 15)->nullable();
            $table->integer('harga')->default(0);
            $table->integer('jumlah')->nullable();
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
        Schema::dropIfExists('barangs');
    }
}
