<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemMadeAtSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->table('rg_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('made_at_sale')->after('inventory_tracking');
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
            $table->dropColumn('made_at_sale');
        });
    }
}
