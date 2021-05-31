<?php

namespace Rutatiina\Item\Http\Controllers\Api\V1;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rutatiina\FinancialAccounting\Traits\FinancialAccountingTrait;
use Rutatiina\Item\Models\Item;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    use FinancialAccountingTrait;

    public function __construct()
    {}

    /*
     * 1st priority is to check id
     * 2nd priority is to check external_key
     * so 1st id then external_key
     */
    private function item($id)
	{
		if(is_numeric($id)) {
			$Item = Item::find($id);
			if ($Item) {
				return $Item;
			}
		}

		$query = Item::where('external_key', $id);
        $count = $query->count();

		if (!$count) {
			response()->json([
				'status' => 'error',
				'data' => [],
				'messages' => ['Record not found'],
			])->send();
			exit;
		}

		if ($count > 1) {
			response()->json([
				'status' => 'error',
				'data' => [],
				'messages' => ['Multiple records found'],
			])->send();
			exit;
		}

		return $query->first();
	}

    public function index()
    {
        return [
			'status' => 'success',
			'data' => Item::all(),
			'messages' => []
		];
    }

    public function create()
    {
        return [
			'status' => 'error',
			'data' => [],
			'messages' => ['Unknown request (create)'],
		];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        	'external_key' => ['max:100', 'unique:rg_items'],
            'type' => 'required',
            'name' => ['required', 'string', 'max:255', 'unique:rg_items'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:rg_items'],
            'units' => 'required|numeric',

            'selling_rate' => 'required|numeric',
            'selling_currency' => 'required',
            //'selling_financial_account_code' => 'required',
            //'selling_tax_code' => 'required',
            //'selling_tax_inclusive' => 'required',
            //'selling_description' => 'required', //not required

            'billing_rate' => 'required',
            'billing_currency' => 'required',
            //'billing_financial_account_code' => 'required',
            //'billing_tax_code' => 'required',
            //'billing_tax_inclusive' => 'required',
            //'billing_description' => 'required', //not required
        ]);

        if ($validator->fails()) {
        	$response = ['status' => 'error', 'data' => [], 'messages' => []];
            foreach ($validator->errors()->all() as $field => $message) {
                $response['messages'][] = $message;
            }
            return $response;
        }

        $Item = new Item;

        $Item->tenant_id = Auth::user()->tenant->id;
        $Item->user_id = Auth::id();
        $Item->external_key = $request->external_key;
        $Item->type = $request->type;
        $Item->name = $request->name;
        $Item->sku = $request->sku;
        $Item->inventory_tracking = $request->inventory_tracking;
        $Item->units = (is_numeric($request->units)) ? $request->units : 1;

        $Item->selling_rate = floatval($request->selling_rate);
        $Item->selling_currency = $request->selling_currency;
        $Item->selling_financial_account_code = $request->selling_financial_account_code;
        $Item->selling_tax_code = (empty($request->selling_tax_code)) ? null : $request->selling_tax_code;
        $Item->selling_tax_inclusive = $request->selling_tax_inclusive;
        $Item->selling_description = $request->selling_description;

        $Item->billing_rate = floatval($request->billing_rate);
        $Item->billing_currency = $request->billing_currency;
        $Item->billing_financial_account_code = $request->billing_financial_account_code;
        $Item->billing_tax_code = (empty($request->billing_tax_code)) ? null : $request->billing_tax_code;
        $Item->billing_tax_inclusive = $request->billing_tax_inclusive;
        $Item->billing_description = $request->billing_description;

        $Item->save();

        return [
			'status' => 'success',
			'data' => [
				'id' => $Item->id,
			],
			'messages' => ['Item successfully saved.']
		];

    }

    public function show($id)
    {
    	$item = $this->item($id);
    	return [
			'status' => 'success',
			'data' => $item,
			'messages' => []
		];
	}

    public function edit($id)
    {
        return [
			'status' => 'error',
			'data' => [],
			'messages' => ['Unknown request (edit)'],
		];
    }

    public function update($id, Request $request)
    {
        //check if the new name already exists
        $Item = Item::where('id', '!=', $id)->where('name', $request->name)->where('tenant_id', Auth::user()->tenant->id)->first();
        if ($Item) {
            return [
				'status' => 'error',
				'data' => [],
				'messages' => ['Name already in use.'],
			];
        }
        //check if the new sku already exists
        $Item = Item::where('id', '!=', $id)->where('sku', $request->sku)->where('tenant_id', Auth::user()->tenant->id)->first();
        if ($Item) {
            return [
				'status' => 'error',
				'data' => [],
				'messages' => ['SKU already in use.'],
			];
        }

        $validator = Validator::make($request->all(), [
        	'external_key' => ['max:100', Rule::unique('rg_items')->ignore($id)],
            'type' => 'required',
            'units' => 'required|numeric',

            'selling_rate' => 'required|numeric',
            'selling_currency' => 'required',
            //'selling_financial_account_code' => 'required',
            //'selling_tax_code' => 'required',
            //'selling_tax_inclusive' => 'required',
            //'selling_description' => 'required', //not required

            'billing_rate' => 'required',
            'billing_currency' => 'required',
            //'billing_financial_account_code' => 'required',
            //'billing_tax_code' => 'required',
            //'billing_tax_inclusive' => 'required',
            //'billing_description' => 'required', //not required
        ]);

        if ($validator->fails()) {
            $response = ['status' => 'error', 'data' => [], 'messages' => []];
            foreach ($validator->errors()->all() as $field => $message) {
                $response['messages'][] = $message;
            }
            return $response;
        }

        $Item = $this->item($id);

        if ( $request->external_key) {
        	$Item->external_key  = $request->external_key;
		}

        $Item->user_id = Auth::id();
        $Item->type = $request->type;
        $Item->name = $request->name;
        $Item->sku = $request->sku;
        $Item->inventory_tracking = $request->inventory_tracking;
        $Item->units = (is_numeric($request->units)) ? $request->units : 1;

        $Item->selling_rate = floatval($request->selling_rate);
        $Item->selling_currency = $request->selling_currency;
        $Item->selling_financial_account_code = $request->selling_financial_account_code;
        $Item->selling_tax_code = (empty($request->selling_tax_code)) ? null : $request->selling_tax_code;
        $Item->selling_tax_inclusive = $request->selling_tax_inclusive;
        $Item->selling_description = $request->selling_description;

        $Item->billing_rate = floatval($request->billing_rate);
        $Item->billing_currency = $request->billing_currency;
        $Item->billing_financial_account_code = $request->billing_financial_account_code;
        $Item->billing_tax_code = (empty($request->billing_tax_code)) ? null : $request->billing_tax_code;
        $Item->billing_tax_inclusive = $request->billing_tax_inclusive;
        $Item->billing_description = $request->billing_description;

        $Item->save();

        return [
			'status' => 'success',
			'data' => [
				'id' => $Item->id,
			],
			'messages' => ['Item successfully Updated.']
		];
    }

    public function destroy($id)
    {
		$delete = Item::destroy($id);

		if (empty($delete)) {
			return [
				'status' => 'error',
				'data' => [],
				'messages' => ['Item ('.$id.') not found'],
			];
		}

		return [
			'status' => 'success',
			'data' => [],
			'messages' => ['Item successfully deleted'],
		];
    }

    public function deactivate($id, Request $request)
    {
    	Item::whereIn('id', $request->ids)->update(['status' => 'inactive']);

		 return [
			'status' => 'success',
			'data' => [],
			'messages' => [count($request->ids) . ' Item(s) deactivated.']
		];
    }

    public function activate(Request $request)
    {
    	Item::whereIn('id', $request->ids)->update(['status' => 'active']);

		return [
			'status' => 'success',
			'data' => [],
			'messages' => [count($request->ids) . ' Item(s) activated.']
		];
    }
}
