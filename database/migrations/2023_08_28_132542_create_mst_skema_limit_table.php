<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSkemaLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_skema_limit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('skema_kredit_id', false, true);
            $table->bigInteger('from', false, true);
            $table->bigInteger('to', false, true)->nullable();
            $table->timestamps();

            $table->foreign('skema_kredit_id')->references('id')->on('mst_skema_kredit')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_skema_limit');
    }
}
