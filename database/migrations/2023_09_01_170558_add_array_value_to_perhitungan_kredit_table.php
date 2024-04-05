<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArrayValueToPerhitunganKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perhitungan_kredit', function (Blueprint $table) {
            $table->text('array_value')->nullable()->after('nominal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perhitungan_kredit', function (Blueprint $table) {
            $table->dropColumn('array_value');
        });
    }
}
