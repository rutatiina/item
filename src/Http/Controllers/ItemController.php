<?php

namespace Rutatiina\Item\Http\Controllers;

use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rutatiina\Item\Models\ItemImage;
use Rutatiina\Tax\Models\Tax;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Classes\Countries as ClassesCountries;
use Rutatiina\Classes\Currencies as ClassesCurrencies;
use Rutatiina\Item\Traits\ItemsVueSearchSelect;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

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
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $Item = Item::paginate($per_page);

        return [
            'tableData' => $Item
        ];
    }

    public function create()
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $Item = new Item;
        $attributes = $Item->rgGetAttributes();

        $attributes['type'] = 'product';
        $attributes['selling_currency'] = Auth::user()->tenant->base_currency;
        $attributes['billing_currency'] = Auth::user()->tenant->base_currency;
        $attributes['image'] = '/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['imagePresently'] = $attributes['image'];
        $attributes['images'] = (object)[
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/template/l/global_assets/images/placeholders/placeholder.jpg',
        ];
        $attributes['imagesPresently'] = $attributes['images'];
        $attributes['imagesDeleted'] = [];

        $data = [
            'pageTitle' => 'Create Item',
            'urlPost' => '/items', #required
            'currencies' => ClassesCurrencies::en_IN(),
            'countries' => ClassesCountries::ungrouped(),
            'taxes' => Tax::all(),
            'accounts' => Account::all(),
            'attributes' => $attributes,
            'selectedSellingTax' => json_decode('{}'),
            'selectedBillingTax' => json_decode('{}'),
            'selectedSellingAccount' => json_decode('{}'),
            'selectedBillingAccount' => json_decode('{}'),
        ];

        if (FacadesRequest::wantsJson())
        {
            return $data;
        }

        return view($this->loadViewsFrom . 'create')->with([
            'accounts' => Account::all(),
            'taxes' => Tax::all()
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'name' => ['required', 'string', 'max:255', 'unique:tenant.rg_items'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:tenant.rg_items'],
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
            //'billing_description' => 'required', //not required,

            'image' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images0' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images1' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images2' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images3' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images4' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images5' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images6' => 'mimes:jpg,png,jpeg|max:2048|nullable',
            'images7' => 'mimes:jpg,png,jpeg|max:2048|nullable',
        ]);

        if ($validator->fails())
        {
            $msg = '';
            foreach ($validator->errors()->all() as $field => $messages)
            {
                $msg .= "\n" . $messages;
            }
            return json_encode(['status' => false, 'message' => $msg]);
        }

        $storage_path = '/items/' . date('Y-m');

        $storage = Storage::disk('public_storage');
        if (!$storage->has($storage_path))
        {
            $storage->makeDirectory($storage_path);
        }

        if ($request->file('image'))
        {
            $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('image'));

            $image_path = 'storage/' . $file_storage_name;
            $image_url = url('storage/' . $file_storage_name);
        }

        $Item = new Item;

        $Item->tenant_id = Auth::user()->tenant->id;
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
        $Item->status = 'active';

        $Item->image_name = $request->file('image')->getClientOriginalName();
        $Item->image_path = (isset($image_path)) ? $image_path : null;
        $Item->image_url = (isset($image_url)) ? $image_url : null;

        $Item->save();

        for ($i = 0; $i <= 7; $i++)
        {
            if ($request->file('images' . $i))
            {
                $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('images' . $i));

                //save the item images
                $ItemImage = new ItemImage;
                $ItemImage->item_id = $Item->id;
                $ItemImage->image_name = $request->file('images' . $i)->getClientOriginalName();
                $ItemImage->image_path = 'storage/' . $file_storage_name;
                $ItemImage->image_url = url('storage/' . $file_storage_name);
                $ItemImage->save();
            }
        }

        $response = [
            'status' => true,
            'message' => 'Item successfully saved.'
        ];

        return json_encode($response);

    }

    public function show()
    {
    }

    public function edit($id)
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $taxes = Tax::all();

        $accounts = Account::all();
        $accountsKeyById = $accounts->keyBy('id');

        $item = Item::find($id);

        $attributes = $item->toArray();
        $attributes['_method'] = 'PATCH';
        $attributes['image'] = url($item->image_path);
        $attributes['imagePresently'] = $attributes['image'];

        $itemImages = $item->images->toArray();
        $attributesImages = [];

        for ($i=0;$i<8;$i++)
        {
            if (isset($itemImages[$i]))
            {
                $attributesImages[$i] = url($itemImages[$i]['image_path']);
            }
            else
            {
                $attributesImages[$i] = '/template/l/global_assets/images/placeholders/placeholder.jpg';
            }
        }

        $attributes['imagesPresently'] = (object) $attributesImages;
        $attributes['images'] = (object) $attributesImages;
        $attributes['imagesDeleted'] = [];

        $data = [
            'pageTitle' => 'Update Item',
            'urlPost' => '/items/' . $attributes['id'], #required
            'currencies' => ClassesCurrencies::en_INSelectOptions(),
            'countries' => ClassesCountries::ungroupedSelectOptions(),
            'taxes' => $taxes,
            'accounts' => $accounts,
            'attributes' => $attributes
        ];

        if (FacadesRequest::wantsJson())
        {
            return $data;
        }

        $Item = Item::find($id);
        return view($this->loadViewsFrom . 'edit')->with([
            'accounts' => self::accounts(),
            'taxes' => self::taxes(),
            'item' => $Item,
        ]);
    }

    public function update($id, Request $request)
    {
        //check if the new name already exists
        $Item = Item::where('id', '!=', $id)->where('name', $request->name)->first();
        if ($Item)
        {
            return json_encode(['status' => false, 'message' => 'Name already in use.']);
        }

        if ($request->sku)
        {

            //check if the new sku already exists
            $Item = Item::where('id', '!=', $id)->where('sku', $request->sku)->first();
            if ($Item)
            {
                return json_encode(['status' => false, 'message' => 'SKU already in use.']);
            }
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails())
        {

            //$request->flash();
            //return redirect()->back()->withErrors($validator);

            $errors = $validator->errors();
            //print_r($errors); exit;

            $msg = '';
            foreach ($errors->all() as $field => $messages)
            {
                $msg .= "\n" . $messages;
                //print_r($messages); exit;
            }

            $response = [
                'status' => false,
                'message' => $msg
            ];

            return json_encode($response);
        }

        $Item = Item::find($id);

        $Item->updated_by = Auth::id();
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

        $response = [
            'status' => true,
            'message' => 'Item successfully Updated.'
        ];

        return json_encode($response);
    }

    public function destroy()
    {
    }

    public function VueSearchSelectSales(Request $request)
    {
        return static::VueSearchSelectDataItemsSales($request);
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

    /* to be deleted - not needed
    public function select2ForSales()
    {
        $items = Item::select(
                DB::raw('DATE(created_at)'),
                'id',
                DB::raw('name as text'),
                'type',
                DB::raw('selling_description as description'),
                DB::raw('selling_financial_account_code as financial_account_code'),
                DB::raw('selling_rate as rate'),
                DB::raw('selling_tax_inclusive as tax_inclusive')
            )
            ->whereNotIn('type', ['cost_center'])
            ->whereNotIn('status', ['deactivated'])
            ->get();

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
                        'tax_inclusive' => ($item->tax_inclusive == 1)? true : false,
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
    */

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
        $importFile = Storage::disk('public_storage')->putFile('/', $request->file('file'));


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

    public function deactivate($id, Request $request)
    {
        Item::whereIn('id', $request->ids)->update(['status' => 'deactivated']);

        $response = [
            'status' => true,
            'message' => count($request->ids) . ' Item(s) deactivated.'
        ];

        return json_encode($response);
    }

    public function activate(Request $request)
    {
        Item::whereIn('id', $request->ids)->update(['status' => 'active']);

        $response = [
            'status' => true,
            'message' => count($request->ids) . ' Item(s) activated.',
        ];

        return json_encode($response);
    }

    public function delete(Request $request)
    {
        Item::whereIn('id', $request->ids)->delete();

        $response = [
            'status' => true,
            'message' => count($request->ids) . ' Item(s) deleted.',
            'ids' => $request->ids,
        ];

        return json_encode($response);
    }
}
