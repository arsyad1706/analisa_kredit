<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHaveDetailToMstItemPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_item_perhitungan_kredit', function (Blueprint $table) {
            $table->boolean('have_detail')->after('is_array')->default(false);
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
            $table->dropColumn('have_detail');
        });
    }
}
