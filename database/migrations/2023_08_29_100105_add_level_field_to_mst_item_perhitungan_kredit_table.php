<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelFieldToMstItemPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_item_perhitungan_kredit', function (Blueprint $table) {
            $table->smallInteger('level', false, true)->after('skema_kredit_limit_id');
            $table->bigInteger('parent_id', false, true)->after('level');
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
            $table->dropColumn('level');
            $table->dropColumn('parent_id');
        });
    }
}
