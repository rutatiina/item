<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRgItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->create('rg_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            //>> default columns
            $table->softDeletes();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            //<< default columns

            //>> table columns
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('external_key')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type', 100);
            $table->string('name');
            $table->string('sku')->nullable();
            $table->char('barcode', 128)->nullable()->index();
            $table->unsignedInteger('units')->default(1);
            $table->string('selling_currency', 3);
            $table->unsignedDecimal('selling_rate', 20, 5);
            $table->unsignedBigInteger('selling_financial_account_code')->nullable()->default(2); //Sales revenue
            $table->unsignedBigInteger('selling_tax_code')->nullable();
            $table->unsignedTinyInteger('selling_tax_inclusive')->nullable();
            $table->string('selling_description')->nullable();;
            $table->string('billing_currency', 3);
            $table->unsignedDecimal('billing_rate', 20, 5);
            $table->unsignedBigInteger('billing_financial_account_code')->nullable()->default(54); //Cost of Sales
            $table->unsignedBigInteger('billing_tax_code')->nullable();
            $table->unsignedTinyInteger('billing_tax_inclusive')->nullable();
            $table->string('billing_description')->nullable();
            $table->unsignedTinyInteger('inventory_tracking')->nullable();
            $table->string('status', 50)->nullable();

            $table->string('image_name', 2048)->nullable();
            $table->string('image_path', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('rg_items');
    }
}
