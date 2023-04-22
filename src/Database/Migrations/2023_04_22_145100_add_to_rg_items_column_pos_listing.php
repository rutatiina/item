<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToRgItemsColumnPosListing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->table('rg_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('pos_listing')->after('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('rg_items', function (Blueprint $table) {
            $table->dropColumn('pos_listing');
        });
    }
}
