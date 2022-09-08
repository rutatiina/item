<?php

namespace Rutatiina\Item\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Item\Models\ItemCategory;
use Rutatiina\Item\Models\ItemSubCategory;

trait ItemsVueSearchSelect
{

    protected static $only_instance;
    public static $contact;

    public function VueSearchSelectDataItemsSales($request)
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name'),
            DB::raw("'item' as type"),
            DB::raw('selling_currency as currency'),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate'),
            DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'inventory_tracking'
        );
        $query->whereNotIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);
        $query->orderBy('name', 'asc');
        $items = $query->get();

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item) {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

        $search_text = ($request->search_text == '-initiate-') ? '' : $request->search_text;

        $response[] = [
            'id' => 0,
            'name' => $request->search_text,
            'type' => '',
            'currency' => '',
            'description' => '',
            //'financial_account_code' => $item->financial_account_code,
            'rate' => 0,
            'tax_method' => '',
            'account_type' => '',
            'image_url' => '',
            'inventory_tracking' => 0
        ];

        foreach ($types as $type) {

            //$response = [];

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->selling_rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                        'image_url' => $item->image_url,
                        'inventory_tracking' => $item->inventory_tracking
                    ];
                }
            }
        }

        //Delete the empty types

        return array_values($response);
    }

    public function VueSearchSelectDataItemsPurchases($request)
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name'),
            DB::raw("'account' as type"),
            DB::raw('billing_currency as currency'),
            DB::raw('billing_description as description'),
            DB::raw('billing_financial_account_code as financial_account_code'),
            DB::raw('billing_rate'),
            DB::raw('if (billing_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'inventory_tracking',
            DB::raw('billing_financial_account_code as debit_financial_account_code')
        );
        $query->whereNotIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);
        $query->orderBy('name', 'asc');
        $items = $query->get();

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item) {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

        $search_text = ($request->search_text == '-initiate-') ? '' : $request->search_text;

        $response[] = [
            'id' => 0,
            'name' => $request->search_text,
            'type' => '',
            'currency' => '',
            'description' => '',
            //'financial_account_code' => $item->financial_account_code,
            'rate' => 0,
            'tax_method' => '',
            'account_type' => '',
            'image_url' => '',
            'inventory_tracking' => 0,
            'debit_financial_account_code' => 0
        ];

        foreach ($types as $type) {

            //$response = [];

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->selling_rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                        'image_url' => $item->image_url,
                        'inventory_tracking' => $item->inventory_tracking,
                        'debit_financial_account_code' => intval($item->debit_financial_account_code)
                    ];
                }
            }
        }

        //Delete the empty types

        return array_values($response);
    }

    public function ___old___VueSearchSelectDataItemsPurchases()
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name'),
            DB::raw("'account' as type"),
            DB::raw('billing_currency as currency'),
            DB::raw('billing_description as description'),
            DB::raw('billing_financial_account_code as financial_account_code'),
            DB::raw('billing_rate'),
            DB::raw('if (billing_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'inventory_tracking',
            DB::raw('billing_financial_account_code as debit_financial_account_code')
        );
        $query->whereIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);
        $items = $query->get();

        $aQuery = Account::query();
        $aQuery->select(
            'id',
            'code',
            DB::raw('name'),
            DB::raw("'account' as type"),
            DB::raw('type as account_type'),
            DB::raw("'".Auth::user()->tenant->base_currency."' as currency"),
            DB::raw("'' as description"),
            DB::raw('0 as billing_rate'),
            DB::raw('\'inclusive\' as tax_method'),
            DB::raw('0 as inventory_tracking'),
            DB::raw('code as debit_financial_account_code')
        );
        $aQuery->whereIn('type', ['asset', 'equity', 'expense']);
        $accounts = $aQuery->get();

        //print_r($accounts); exit;

        /*
            'asset','equity','expense','income','liability','inventory','cost_of_sales','none'

            asset > bill for fixed asset
            equity > buying shares
            expense > as known
        */

        $items = $items->merge($accounts);

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item) {
            if ($item->type == 'asset' || $item->account_type == 'asset') continue; //So that it appears last on the list
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $types[] = 'asset'; //So that it appears last on the list //This line was comment out on 27 Feb 2018

        $response = [];

        foreach ($types as $type) {

            $response[$type]['text'] = ucfirst($type);

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->billing_rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                        'image_url' => $item->image_url,
                        'inventory_tracking' => $item->inventory_tracking,
                        'debit_financial_account_code' => $item->debit_financial_account_code
                    );
                }
            }
        }

        //Delete the empty types
        foreach ($types as $type) {
            if ( empty($response[$type]['children']) ) {
                unset($response[$type]);
            }
        }

        return array_values($response);
    }

    public function VueSearchSelectDataItemsInventory()
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name'),
            DB::raw("'item' as type"),
            DB::raw('selling_currency as currency'),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate as rate'),
            DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'inventory_tracking'

        );
        $query->whereNotIn('type', ['cost_center']);
        $query->where('inventory_tracking', '1');
        $query->whereNotIn('status', ['deactivated']);

        $items = $query->get();

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item) {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

        foreach ($types as $type) {

            $response[$type]['text'] = ucfirst($type);

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                        'image_url' => $item->image_url,
                        'inventory_tracking' => $item->inventory_tracking
                    );
                }
            }
        }

        //Delete the empty types
        foreach ($types as $type) {
            if ( empty($response[$type]['children']) ) {
                unset($response[$type]);
            }
        }

        return array_values($response);
    }

    public function VueSearchSelectDataItemsAccounts()
    {
        $aQuery = Account::query();
        $aQuery->select(
            'id',
            'code',
            DB::raw('name'),
            DB::raw("'account' as type"),
            DB::raw('type as account_type'),
            DB::raw("'".Auth::user()->tenant->base_currency."' as currency"),
            DB::raw("'' as description"),
            DB::raw('0 as rate'),
            DB::raw('\'inclusive\' as tax_method')

        );
        $aQuery->whereIn('type', ['asset', 'equity', 'expense']);
        $items = $aQuery->get();

        //print_r($items); exit;

        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item) {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

        foreach ($types as $type) {

            $response[$type]['text'] = ucfirst($type);

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                    );
                }
            }
        }

        //Delete the empty types
        foreach ($types as $type) {
            if ( empty($response[$type]['children']) ) {
                unset($response[$type]);
            }
        }

        return array_values($response);
    }

    public function vuePosData($request)
    {
        $query = Item::query();
        $query->select(
            'id',
            'barcode',
            DB::raw('name'),
            DB::raw("'item' as type"),
            DB::raw('selling_currency as currency'),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate'),
            DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'image_path',
            'inventory_tracking'
        );
        $query->whereNotIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);

        if ($request->barcode)
        {
            $query->where('barcode', $request->barcode);
        }

        if ($request->search)
        {
            $query->where(function($q) use ($request)
            {
                $q->where('barcode', 'like', '%'.$request->search.'%');
                $q->orWhere('name', 'like', '%'.$request->search.'%');
                $q->orWhere('sku', 'like', '%'.$request->search.'%');
                $q->orWhere('selling_description', 'like', '%'.$request->search.'%');
                $q->orWhere('billing_description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->item_category)
        {
            $query->whereHas('categorizations', function (Builder $query) use ($request)
            {
                if ($request->item_category) $query->where('item_category_id', $request->item_category);
                if ($request->item_sub_category) $query->where('item_sub_category_id', $request->item_sub_category);
            });
        }

        $query->orderBy('name', 'asc');

        $items = $query->get();

        $items->load('sales_taxes');
        $items->load('purchase_taxes');

        $itemCategory = ItemCategory::find($request->item_category);
        $itemSubCategory = ItemSubCategory::find($request->item_sub_category);

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty())
        {
            return [
                'items' => [],
                'item_category' => [
                    'name' => null,
                    'sub_category' => [
                        'name' => null
                    ],
                ],
            ];
        }

        foreach ($items as $item)
        {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

        foreach ($types as $type)
        {
            //$response = [];

            foreach ($items as $item)
            {
                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker))
                {
                    $response[] = [
                        'id' => $item->id,
                        'barcode' => $item->barcode,
                        'name' => $item->name,
                        'type' => $item->type,
                        'currency' => $item->currency,
                        'description' => $item->description,
                        //'financial_account_code' => $item->financial_account_code,
                        'rate' => $item->selling_rate,
                        'tax_method' => $item->tax_method,
                        'account_type' => @$item->account_type,
                        'image_url' => $item->image_url,
                        'image_path' => $item->image_path,
                        'sales_taxes' => $item->sales_taxes,
                        'purchase_taxes' => $item->purchase_taxes,
                        'inventory_tracking' => $item->inventory_tracking
                    ];
                }
            }
        }

        //Delete the empty types

        //return array_values($response);

        return [
            'items' => array_values($response),
            'item_category' => [
                'name' => optional($itemCategory)->name,
                'sub_category' => [
                    'name' => optional($itemSubCategory)->name
                ],
            ],
        ];
    }

}
