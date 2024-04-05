<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIsTwoColumnsToMstItemPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_item_perhitungan_kredit', function (Blueprint $table) {
            $table->boolean('is_two_columns')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_item_perhitungan_kredit', function (Blueprint $table) {
            //
        });
    }
}
