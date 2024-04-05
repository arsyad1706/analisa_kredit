<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueFromNameOnMstSkemaKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_skema_kredit', function (Blueprint $table) {
            $table->dropUnique('mst_skema_kredit_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_skema_kredit', function (Blueprint $table) {
            $table->unique('name');
        });
    }
}
