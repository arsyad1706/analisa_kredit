<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProdukKreditIdOnMstSkemaKreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_skema_kredit', function(Blueprint $table) {
            $table->dropForeign('mst_skema_kredit_produk_kredit_id_foreign');
            $table->dropColumn('produk_kredit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_skema_kredit', function(Blueprint $table) {
            $table->bigInteger('produk_kredit_id', false, true)->after('id');
            $table->foreign('produk_kredit_id')->references('id')->on('mst_produk_kredit')->cascadeOnDelete();
        });
    }
}
