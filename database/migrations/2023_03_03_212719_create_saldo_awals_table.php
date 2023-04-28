<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoAwalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_awals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('akuns_id')->nullable()->index();
            $table->foreign('akuns_id')->references('id')->on('akuns')->onDelete('cascade');
            $table->integer("debet")->default(0);
            $table->integer("kredit")->default(0);
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
        Schema::dropIfExists('saldo_awals');
    }
}
