<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstItemPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_item_perhitungan_kredit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('skema_kredit_limit_id', false, true);
            $table->string('field', 50);
            $table->timestamps();
            
            $table->foreign('skema_kredit_limit_id')->references('id')->on('mst_skema_limit')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_item_perhitungan_kredit');
    }
}
