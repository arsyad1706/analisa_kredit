<?php

use App\Models\MstProdukKredit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterPengajuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Call seeder
        Artisan::call('db:seed', [
            '--class' => 'MstProdukKreditSeeder',
        ]);

        Schema::table('pengajuan', function(Blueprint $table) {
            $table->bigInteger('produk_kredit_id', false, true)->nullable()->after('skema_kredit')->comment('if null = data lama');
            $table->bigInteger('skema_kredit_id', false, true)->nullable()->after('produk_kredit_id')->comment('if null = data lama');
            $table->bigInteger('skema_limit_id', false, true)->nullable()->after('skema_kredit_id')->comment('if null = data lama');

            $table->foreign('produk_kredit_id')->references('id')->on('mst_produk_kredit')->cascadeOnDelete();
            $table->foreign('skema_kredit_id')->references('id')->on('mst_skema_kredit')->cascadeOnDelete();
            $table->foreign('skema_limit_id')->references('id')->on('mst_skema_limit')->cascadeOnDelete();
        });

        $pkpj_id = MstProdukKredit::select('id', 'name')->where('name', 'PKPJ')->first()->id;
        $kkb_id = MstProdukKredit::select('id', 'name')->where('name', 'KKB')->first()->id;
        $umroh_id = MstProdukKredit::select('id', 'name')->where('name', 'Talangan Umroh')->first()->id;
        $prokesra_id = MstProdukKredit::select('id', 'name')->where('name', 'Prokesra')->first()->id;
        $kusuma_id = MstProdukKredit::select('id', 'name')->where('name', 'Kusuma')->first()->id;

        $pkpj_query = "UPDATE pengajuan SET produk_kredit_id = IF(skema_kredit = 'PKPJ', $pkpj_id, produk_kredit_id)";
        DB::statement($pkpj_query);
        $kkb_query = "UPDATE pengajuan SET produk_kredit_id = IF(skema_kredit = 'KKB', $kkb_id, produk_kredit_id)";
        DB::statement($kkb_query);
        $umroh_query = "UPDATE pengajuan SET produk_kredit_id = IF(skema_kredit = 'Talangan Umroh', $umroh_id, produk_kredit_id)";
        DB::statement($umroh_query);
        $prokesra_query = "UPDATE pengajuan SET produk_kredit_id = IF(skema_kredit = 'Prokesra', $prokesra_id, produk_kredit_id)";
        DB::statement($prokesra_query);
        $kusuma_query = "UPDATE pengajuan SET produk_kredit_id = IF(skema_kredit = 'Kusuma', $kusuma_id, produk_kredit_id)";
        DB::statement($kusuma_query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE pengajuan SET produk_kredit_ID = NULL");
        
        Schema::table('pengajuan', function(Blueprint $table) {
            $table->dropForeign('pengajuan_produk_kredit_id_foreign');
            $table->dropForeign('pengajuan_skema_kredit_id_foreign');
            $table->dropForeign('pengajuan_skema_limit_id_foreign');

            $table->dropColumn('produk_kredit_id');
            $table->dropColumn('skema_kredit_id');
            $table->dropColumn('skema_limit_id');
        });
    }
}
