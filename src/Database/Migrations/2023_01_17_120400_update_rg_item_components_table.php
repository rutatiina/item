<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRgItemComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'item_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('item_id', 'parent_item_id');
            });
        }

        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'component_item_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('component_item_id', 'item_id');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'component_unit_of_measurement_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('component_unit_of_measurement_id', 'unit_of_measurement_id');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'component_unit_of_measurement_symbol'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('component_unit_of_measurement_symbol', 'unit_of_measurement_symbol');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'component_quantity'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('component_quantity', 'quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'item_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('item_id', 'component_item_id');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'parent_item_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('parent_item_id', 'item_id');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'unit_of_measurement_id'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('unit_of_measurement_id', 'component_unit_of_measurement_id');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'unit_of_measurement_symbol'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('unit_of_measurement_symbol', 'component_unit_of_measurement_symbol');
            });
        }
        if (Schema::connection('tenant')->hasColumn('rg_item_components', 'quantity'))
        {
            Schema::connection('tenant')->table('rg_item_components', function (Blueprint $table) {
                $table->renameColumn('quantity', 'component_quantity');
            });
        }
    }
}
