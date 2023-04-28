<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tgl')->nullable();
            $table->string('kode')->nullable();
            $table->char('nominal', 25)->default(0);
            $table->text('keterangan')->nullable();
            $table->uuid('akuns_id')->nullable()->index();
            $table->foreign('akuns_id')->references('id')->on('akuns')->onDelete('cascade');
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
        Schema::dropIfExists('pengeluarans');
    }
}
