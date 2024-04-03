<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameLogCetakKkbToLogCetakKkbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE log_cetak RENAME log_cetak");
        Schema::table('log_cetak', function (Blueprint $table) {
            $table->string('no_pk', 25)->nullable()->after('tgl_cetak_po');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE log_cetak RENAME log_cetak");
        Schema::table('log_cetak', function (Blueprint $table) {
            $table->dropColumn('no_pk');
        });
    }
}
