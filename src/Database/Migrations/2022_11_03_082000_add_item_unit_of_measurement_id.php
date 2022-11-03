<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemUnitOfMeasurementId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->table('rg_items', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_of_measurement_id')->after('inventory_tracking');
            $table->string('unit_of_measurement_symbol', 50)->nullable()->after(('unit_of_measurement_id'));
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
            $table->dropColumn('unit_of_measurement_id');
            $table->dropColumn('unit_of_measurement_symbol');
        });
    }
}
