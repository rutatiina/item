<?php

namespace Rutatiina\Item\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Rutatiina\Tax\Models\Tax;
use Illuminate\Validation\Rule;
use Rutatiina\Item\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Rutatiina\Item\Models\ItemImage;
use Illuminate\Support\Facades\Storage;
use Rutatiina\Item\Models\ItemCategory;
use Rutatiina\Item\Services\ItemService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Rutatiina\Item\Traits\ItemsVueSearchSelect;
use Rutatiina\Item\Models\ItemUnitOfMeasurement;
use Rutatiina\FinancialAccounting\Models\Account;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Rutatiina\Globals\Services\Countries as ClassesCountries;
use Rutatiina\Globals\Services\Currencies as ClassesCurrencies;

class ItemController extends Controller
{
    use ItemsVueSearchSelect;

    //calls AccountingTrait

    private $loadViewsFrom = 'item::limitless.';

    public function __construct()
    {
        $this->middleware('permission:items.view');
        $this->middleware('permission:items.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:items.update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:items.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $per_page = ($request->per_page) ? $request->per_page : 20;

        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        // $Item = Item::paginate($per_page);

        $query = Item::query();

        if ($request->search)
        {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('name', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('sku', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('barcode', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('selling_description', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('billing_description', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
            });
        }

        $query->orderBy('name', 'asc');
        $Items = $query->paginate($per_page);

        return [
            'tableData' => $Items
        ];
    }

    public function create()
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        return ItemService::create();
    }

    public function store(Request $request)
    {
        //return $request;

        $store = ItemService::store($request);

        if ($store)
        {
            return [
                'status' => true,
                'messages' => ['Item successfully saved.']
            ];
        }
        else
        {
            return [
                'status' => false,
                'messages' => ItemService::$errors
            ];
        }
    }

    public function show()
    {
        //
    }

    public function edit($id)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $taxes = Tax::all();

        $accounts = Account::all();
        // $accountsKeyById = $accounts->keyBy('id');

        $item = Item::find($id);

        $item->load('sales_taxes');
        $item->load('purchase_taxes');
        $item->load('categorizations');
        $item->load('components.item.unit_of_measurement');

        $attributes = $item->toArray();
        $attributes['_method'] = 'PATCH';
        $attributes['image'] = ($item->image_path) ? url($item->image_path) : '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['image_presently'] = $attributes['image'];

        $itemImages = $item->images->keyBy('position')->toArray();
        $attributesImages = [];

        for ($i=0;$i<8;$i++)
        {
            if (isset($itemImages[$i]))
            {
                $attributesImages[$i] = url($itemImages[$i]['image_path']);
            }
            else
            {
                $attributesImages[$i] = '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg';
            }
        }

        $attributes['images_presently'] = (object) $attributesImages;
        $attributes['images'] = (object) $attributesImages;
        $attributes['images_deleted'] = [];

        return [
            'pageTitle' => 'Update Item',
            'urlPost' => '/items/' . $attributes['id'], #required
            'currencies' => ClassesCurrencies::en_INSelectOptions(),
            'countries' => ClassesCountries::ungroupedSelectOptions(),
            'units_of_measurement' => ItemUnitOfMeasurement::select(['id', 'name'])->get(),
            'taxes' => $taxes,
            'accounts' => $accounts,
            'attributes' => $attributes
        ];
    }

    public function update($id, Request $request)
    {
        // return $request;

        $store = ItemService::update($id, $request);

        if ($store)
        {
            return [
                'status' => true,
                'messages' => ['Item successfully Updated.']
            ];
        }
        else
        {
            return [
                'status' => true,
                'messages' => ItemService::$errors
            ];
        }
    }

    public function destroy($id, Request $request)
    {
        if (ItemService::destroy($request->ids))
        {
            return [
                'status' => true,
                'messages' => [count($request->ids) . ' Item deleted.'],
            ];
        }
        else
        {
            return [
                'status' => false,
                'messages' => ItemService::$errors
            ];
        }
    }

    public function VueSearchSelectSales(Request $request)
    {
        return static::VueSearchSelectDataItemsSales($request);
    }

    public function VueSearchSelectPurchases(Request $request)
    {
        return static::VueSearchSelectDataItemsPurchases($request);
    }

    public function vuePos(Request $request)
    {
        return static::vuePosData($request);
    }

    public function search(Request $request)
    {
        $query = Item::query();
        foreach ($request->search as $search)
        {
            $query->where($search['column'], 'like', '%' . $search['value'] . '%');
        }
        return $query->paginate(10);
    }

    public function datatables()
    {
        $items = Item::query();
        return Datatables::of($items->orderBy('name', 'asc'))->make(true);
    }

    public function import(Request $request)
    {
        $allowed_file_type = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', //xlsx
            'application/vnd.ms-excel',
            'text/plain',
            'text/csv',
            'text/tsv'
        ];

        if (in_array($request->file->getClientMimeType(), $allowed_file_type))
        {
            // do nothing i.e allow file processing
        }
        else
        {
            $response = [
                'status' => false,
                'message' => 'ERROR: File type not allowed / mime type not allowed.'
            ];
            return json_encode($response);
        }

        //print_r($this->input->post()); exit;

        //Save the uploaded file
        $importFile = Storage::disk('public')->putFile('/', $request->file('file'));


        //Copy imported file into array
        //$params = ['io_factory' => true];
        //$this->load->library('third_party_phpexcel', $params);

        //$data = Excel::toCollection($request->file('file'), 'storage/'.$importFile);
        $excelToArray = Excel::toArray($request->file('file'), 'storage/' . $importFile);
        //dd($excelToArray);
        //print_r($excelToArray[0]);

        $data = $excelToArray[0];

        //print_r($data); exit;
        unset($data[0]); //delete the 1st line of titles

        /*
            Check for error within the file
            [A] => Category
            [B] => Full name
            [C] => Display name
            [D] => Contact person
            [E] => Contact Email
            [F] => Contact phone
            [G] => Payment terms
            [H] => Remarks
        */

        $responseMessage = null;
        $items = [];
        foreach ($data as $key => $value)
        {

            $row = [
                'type' => $value[0],
                'name' => $value[1],
                'sku' => $value[2],
                'units' => $value[3],
                'selling_rate' => $value[4],
                'billing_rate' => $value[5],
                'selling_description' => $value[6],
                'billing_description' => $value[7],
                'tenant_id' => Auth::user()->tenant->id,
                'user_id' => Auth::id(),
                'selling_currency' => Auth::user()->tenant->base_currency,
                'billing_currency' => Auth::user()->tenant->base_currency,
                'selling_tax_inclusive' => 1,
                'billing_tax_inclusive' => 1,
            ];

            $validator = Validator::make($row, [
                'name' => ['required'],
                'units' => ['required', 'numeric'],
                'selling_rate' => ['required', 'numeric'],
                'billing_rate' => ['numeric'],
            ]);

            if ($validator->fails())
            {
                foreach ($validator->errors()->all() as $field => $messages)
                {
                    $responseMessage .= "\n" . $messages;
                }
                $response = [
                    'status' => false,
                    'message' => 'Error on row #' . ($key + 1) . $responseMessage,
                ];

                return json_encode($response);

            }
            else
            {
                $items[] = $row;
            }

        }

        //print_r($items); exit;

        Item::insert($items);

        $response = [
            'status' => true,
            'message' => count($items) . ' Item(s) imported.' . "\n" . $responseMessage,
        ];

        return json_encode($response);
    }

    public function deactivate(Request $request)
    {
        Item::whereIn('id', $request->ids)->update(['status' => 'deactivated']);

        $response = [
            'status' => true,
            'messages' => [count($request->ids) . ' Item(s) deactivated.']
        ];

        return json_encode($response);
    }

    public function activate(Request $request)
    {
        Item::whereIn('id', $request->ids)->update(['status' => 'active']);

        $response = [
            'status' => true,
            'messages' => [count($request->ids) . ' Item(s) activated.'],
        ];

        return json_encode($response);
    }

    public function delete(Request $request)
    {
        if (ItemService::destroy($request->ids))
        {
            return [
                'status' => true,
                'messages' => [count($request->ids) . ' Item(s) deleted.'],
            ];
        }
        else
        {
            return [
                'status' => false,
                'messages' => ItemService::$errors
            ];
        }
    }

    public function categorizations(Request $request)
    {
        $categorizations = [];

        $query = ItemCategory::query();

        if ($request->search)
        {
            foreach ($request->search as $search)
            {
                $query->where($search['column'], 'like', '%' . $search['value'] . '%');
            }
        }

        $data = $query->get();
        $data->load('sub_categories');

        foreach ($data as $category)
        {
            $categorizations[] = [
                'id' => $category->id,
                'item_category_id' => $category->id,
                'item_category_name' => $category->name,
                'item_sub_category_id' => null,
                'item_sub_category_name' => null,
            ];

            foreach ($category->sub_categories as $sub_category)
            {
                $categorizations[] = [
                    'id' => $category->id.'-'.$sub_category->id,
                    'item_category_id' => $category->id,
                    'item_category_name' => $category->name,
                    'item_sub_category_id' => $sub_category->id,
                    'item_sub_category_name' => $sub_category->name,
                ];
            }
        }

        return $categorizations;

    }

    public function routes()
    {
        return [
            'delete' => route('items.delete'),
            'activate' => route('items.activate'),
            'deactivate' => route('items.deactivate'),
            'update_many' => route('items.update_many'),
        ];
    }

    public function updateMany(Request $request)
    {
        $status = true;

        switch ($request->action)
        {
            case 'activate':
                Item::whereIn('id', $request->ids)->update(['status' => 'active']);
                $messages = [count($request->ids) . ' Item(s) activated.'];
                break;
            case 'deactivate':
                Item::whereIn('id', $request->ids)->update(['status' => 'deactivated']);
                $messages = [count($request->ids) . ' Item(s) deactivated.'];
                break;
            case 'track-inventory':
                Item::whereIn('id', $request->ids)->update(['inventory_tracking' => 1]);
                $messages = [count($request->ids) . ' Item(s) inventory tracking activated.'];
                break;
            default:
                $status = false;
                $messages = ['Oops: Unknown action : '.$request->action];
        }

        $response = [
            'status' => $status,
            'messages' => $messages
        ];

        return json_encode($response);
    }
    
    public function componentOptions(Request $request)
    {
        // $per_page = ($request->per_page) ? $request->per_page : 20;

        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $query = Item::query();
        $query->select(['id', 'name', 'unit_of_measurement_id']);
        $query->has('unit_of_measurement');
        $query->with('unit_of_measurement');
        
        // $query->where(function($q) {
        //     $q->whereNotNull('unit_of_measurement_symbol');
        //     $q->Where('unit_of_measurement_symbol', '<>', '');
        // });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('name', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('sku', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('barcode', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('selling_description', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
                $q->orWhere('billing_description', 'like', '%'.Str::replace(' ', '%', $request->search).'%');
            });
        }

        $query->orderBy('name', 'asc');
        $query->with(['unit_of_measurement' => function($query) {
            $query->select('id', 'name', 'symbol');
        }]);
        $Items = $query->get();

        $Items->each(function ($item, $key) {
            $item->unit_of_measurement_name = Str::plural($item->unit_of_measurement->name);
        });

        return $Items;
    }
}
