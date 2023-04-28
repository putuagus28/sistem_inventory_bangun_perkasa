<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('uang_muka')->default(0);
            $table->string('jenis_antrean')->nullable();
            $table->string('no_antrean')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('lama_service')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('riwayat')->nullable(); // ini teknisi yg akan update
            $table->string('status')->nullable(); //open / close

            $table->uuid('pelanggans_id')->nullable()->index();
            $table->foreign('pelanggans_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->uuid('teknisi_id')->nullable()->index();
            $table->foreign('teknisi_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('services');
    }
}
