<?php

namespace Rutatiina\Item\Seeders;

use Illuminate\Database\Seeder;
use Rutatiina\Item\Models\Item;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::insert(
            [
                [
                    'id' => 1,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Laptop',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 1200000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Sales description - basic laptop',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 1000000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Billing description - basic laptop',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Ovacado',
                    'sku' => 'OVA',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 500.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Ovacado - Sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 300.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Ovacado - Billing description',
                    'inventory_tracking' => 1,
                    'status' => 'active',
                ],
                [
                    'id' => 3,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Testing',
                    'sku' => 'Testing',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 150000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Testing sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 50000.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Testign billing description',
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 4,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'item to delete',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 32.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => NULL,
                    'billing_currency' => 'UGX',
                    'billing_rate' => 12.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 5,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'trdtdutfuyfg',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 321.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => NULL,
                    'billing_currency' => 'UGX',
                    'billing_rate' => 123.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 6,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'rtetwer',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 432.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => NULL,
                    'billing_currency' => 'UGX',
                    'billing_rate' => 23.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 7,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'cooking oil',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 20000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => 18,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'buuto cooking oil',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 12000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => 18,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => 1,
                    'status' => 'active',
                ],
                [
                    'id' => 8,
                    'tenant_id' => 46,
                    'type' => 'product',
                    'name' => 'Softloan',
                    'sku' => 'SL',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 120000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'soft laon Sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 100000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'soft laon Billing description',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 9,
                    'tenant_id' => 46,
                    'type' => 'product',
                    'name' => 'Quick trading load ##',
                    'sku' => 'QTL',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 75000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Quick trading laon Sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 50000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Quick trading laon Billing description',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 10,
                    'tenant_id' => 46,
                    'type' => 'product',
                    'name' => 'Quick trading load del',
                    'sku' => 'DEL',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 70000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Quick trading laon Sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 50000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Quick trading laon Billing description',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 11,
                    'tenant_id' => 46,
                    'type' => 'product',
                    'name' => 'Matooke ##',
                    'sku' => 'MTK',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 75000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'Quick trading laon Sales description',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 50000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'Quick trading laon Billing description',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 12,
                    'tenant_id' => 47,
                    'type' => 'product',
                    'name' => 'Position',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 100000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => NULL,
                    'billing_currency' => 'UGX',
                    'billing_rate' => 100000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 13,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'used shoes',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 25000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'durable used shoes',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 8000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => 'durable used shoes',
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 14,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Item 001 - Edit',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 15000.0,
                    'selling_financial_account_code' => null,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => 1,
                    'selling_description' => 'This is test item Entry',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 6000.0,
                    'billing_financial_account_code' => null,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => 1,
                    'billing_description' => NULL,
                    'inventory_tracking' => NULL,
                    'status' => 'active',
                ],
                [
                    'id' => 15,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Gonja',
                    'sku' => 'GNJ',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 1000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => 'UGX',
                    'billing_rate' => 200.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 16,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'Server #1',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 100000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 17,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'service #2',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 12000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 18,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'service #3',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 150000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 19,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'service #4',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 530000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 20,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'fdfsad',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 34132.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 21,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'wwwee',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 34132.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 22,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => '12',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 34132.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => NULL,
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 23,
                    'tenant_id' => 1,
                    'type' => 'service',
                    'name' => 'service 2000',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 2000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => 'service 2000',
                    'billing_currency' => '',
                    'billing_rate' => 0.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => NULL,
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 24,
                    'tenant_id' => 50,
                    'type' => 'product',
                    'name' => 'Brake pad Front left',
                    'sku' => 'bpfl',
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 30000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => 'Brake pad Front left',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 12000.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => 'Brake pad Front left',
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 25,
                    'tenant_id' => 49,
                    'type' => 'product',
                    'name' => 'Product #1',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 2000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => 'Product #1',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 1000.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => 'Product #1',
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 26,
                    'tenant_id' => 50,
                    'type' => 'product',
                    'name' => 'Wheel Bearings',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 35000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => 'Wheel Bearings',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 25000.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => 'Wheel Bearings',
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
                [
                    'id' => 27,
                    'tenant_id' => 1,
                    'type' => 'product',
                    'name' => 'Product #01-2020',
                    'sku' => NULL,
                    'units' => 1,
                    'selling_currency' => 'UGX',
                    'selling_rate' => 20000.0,
                    'selling_financial_account_code' => NULL,
                    'selling_tax_code' => NULL,
                    'selling_tax_inclusive' => NULL,
                    'selling_description' => 'Product #01-2020',
                    'billing_currency' => 'UGX',
                    'billing_rate' => 12000.0,
                    'billing_financial_account_code' => NULL,
                    'billing_tax_code' => NULL,
                    'billing_tax_inclusive' => NULL,
                    'billing_description' => 'Product #01-2020',
                    'inventory_tracking' => 0,
                    'status' => 'active',
                ],
            ]
        );
    }
}
