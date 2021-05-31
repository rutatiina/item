<?php

namespace Rutatiina\Item\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\FinancialAccounting\Traits\FinancialAccountingTrait;
use Rutatiina\Item\Models\Item;

class Select2DataController extends Controller
{
    use FinancialAccountingTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sales()
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name as text'),
            DB::raw("'item' as type"),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate as rate'),
            DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method')
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

        foreach ($types as $type) {

            $response[$type]['text'] = $type;

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'text' => $item->text,
                        'type' => $item->type,
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

        return json_encode(array_values($response));
    }

    public function purchases()
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name as text'),
            DB::raw("'account' as type"),
            DB::raw('billing_description as description'),
            DB::raw('billing_financial_account_code as financial_account_code'),
            DB::raw('billing_rate as rate'),
			DB::raw('if (billing_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method')
        );
        $query->whereIn('type', ['cost_center']);
        $query->whereNotIn('status', ['deactivated']);
        $items = $query->get();

        $aQuery = Account::query();
        $aQuery->select(
            'id',
            'code',
            DB::raw('name as text'),
            DB::raw("'account' as type"),
            DB::raw('type as account_type'),
            DB::raw("'' as description"),
            DB::raw('0 as rate'),
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

            $response[$type]['text'] = $type;

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'text' => $item->text,
                        'type' => $item->type,
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

        return json_encode(array_values($response));
    }

    public function inventory()
    {
        $query = Item::query();
        $query->select(
            'id',
            DB::raw('name as text'),
            DB::raw("'item' as type"),
            DB::raw('selling_description as description'),
            DB::raw('selling_financial_account_code as financial_account_code'),
            DB::raw('selling_rate as rate'),
			DB::raw('if (selling_tax_inclusive, \'inclusive\', \'exclusive\') as tax_method')

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

            $response[$type]['text'] = $type;

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'text' => $item->text,
                        'type' => $item->type,
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

        return json_encode(array_values($response));
    }

    public function accounts()
    {
        $aQuery = Account::query();
        $aQuery->select(
            'id',
            'code',
            DB::raw('name as text'),
            DB::raw("'account' as type"),
            DB::raw('type as account_type'),
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

            $response[$type]['text'] = $type;

            foreach ($items as $item) {

                $checker = (empty($item->account_type)) ? $item->type : $item->account_type;

                if ( preg_match('/'.$type.'/i', $checker)) {

                    $response[$type]['children'][] = array(
                        'id' => $item->id,
                        'text' => $item->text,
                        'type' => $item->type,
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

        return json_encode(array_values($response));
    }
}
