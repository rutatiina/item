<?php

namespace Rutatiina\Item\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;

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
            'image_url'
        );
        $query->whereNotIn('type', ['cost_center']);
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
                    ];
                }
            }
        }

        //Delete the empty types

        return array_values($response);
    }

    public function VueSearchSelectDataItemsPurchases()
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
            'image_url'
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
            DB::raw('\'inclusive\' as tax_method')
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
            'image_url'

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
            DB::raw('name'),
            DB::raw("'item' as type"),
            DB::raw('selling_currency as currency'),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate'),
            DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method'),
            'image_url',
            'image_path'
        );
        $query->whereNotIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);

        $items = $query->get();

        $items->load('sales_taxes');
        $items->load('purchase_taxes');

        //print_r($items); exit;

        //Extract the categories
        $types = [];

        //print_r($items); exit;

        if ($items->isEmpty()) {
            return [];
        }

        foreach ($items as $item)
        {
            $types[] = (empty($item->account_type)) ? $item->type : $item->account_type;
        }

        $types = array_unique($types);

        $response = [];

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
                        'image_path' => $item->image_path,
                        'sales_taxes' => $item->sales_taxes,
                        'purchase_taxes' => $item->purchase_taxes,
                    ];
                }
            }
        }

        //Delete the empty types

        return array_values($response);
    }

}
