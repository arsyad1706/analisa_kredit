<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSkemaKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_skema_kredit', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('produk_kredit_id', false, true);
            $table->string('name', 30)->unique();
            $table->timestamps();

            $table->foreign('produk_kredit_id')->references('id')->on('mst_produk_kredit')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_skema_kredit');
    }
}
