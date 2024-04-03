<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perhitungan_kredit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('temp_calon_nasabah_id', false, true);
            $table->bigInteger('pengajuan_id', false, true)->nullable();
            $table->bigInteger('item_perhitungan_kredit_id', false, true);
            $table->bigInteger('nominal', false, true);
            $table->timestamps();

            $table->foreign('temp_calon_nasabah_id')->references('id')->on('temporary_calon_nasabah');
            $table->foreign('pengajuan_id')->references('id')->on('pengajuan')->cascadeOnDelete();
            $table->foreign('item_perhitungan_kredit_id')->references('id')->on('mst_item_perhitungan_kredit')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perhitungan_kredit');
    }
}
