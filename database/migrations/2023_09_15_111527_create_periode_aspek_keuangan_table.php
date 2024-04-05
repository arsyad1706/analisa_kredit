<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodeAspekKeuanganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periode_aspek_keuangan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('perhitungan_kredit_id', false, true);
            $table->foreign('perhitungan_kredit_id')->references('id')->on('perhitungan_kredit');
            $table->bigInteger('bulan', false, true);
            $table->bigInteger('tahun', false, true);
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
        Schema::dropIfExists('periode_aspek_keuangan');
    }
}
