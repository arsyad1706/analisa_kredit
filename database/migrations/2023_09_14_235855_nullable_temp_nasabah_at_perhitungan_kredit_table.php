<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NullableTempNasabahAtPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perhitungan_kredit', function (Blueprint $table) {
            DB::statement("ALTER TABLE perhitungan_kredit MODIFY COLUMN `temp_calon_nasabah_id` bigint(20) unsigned default null ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
