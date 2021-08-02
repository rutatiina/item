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
use Rutatiina\Item\Models\ItemCategory;
use Rutatiina\Item\Models\ItemImage;
use Rutatiina\Item\Services\ItemService;
use Rutatiina\Tax\Models\Tax;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Classes\Countries as ClassesCountries;
use Rutatiina\Classes\Currencies as ClassesCurrencies;
use Rutatiina\Item\Traits\ItemsVueSearchSelect;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ItemCartegoryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:items.view');
        //$this->middleware('permission:items.create', ['only' => ['create', 'store']]);
        //$this->middleware('permission:items.update', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:items.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $per_page = ($request->per_page) ? $request->per_page : 20;

        if (!FacadesRequest::wantsJson())
        {
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $itemCategories = ItemCategory::paginate($per_page);

        return [
            'tableData' => $itemCategories
        ];
    }

    public function create()
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $itemCategory = new ItemCategory;
        $attributes = $itemCategory->rgGetAttributes();

        $attributes['image'] = '/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['image_presently'] = $attributes['image'];

        $data = [
            'pageTitle' => 'Create Item category',
            'urlPost' => '/items/categories', #required
            'attributes' => $attributes,
        ];

        return $data;
    }

    public function store(Request $request)
    {
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
            return view('l-limitless-bs4.layout_2-ltr-default.appVue');
        }

        $itemCategory = ItemCategory::find($id);
        $itemCategory->load('sub_categories');

        $attributes = $itemCategory->toArray();
        $attributes['_method'] = 'PATCH';
        $attributes['image'] = url($itemCategory->image_path);
        $attributes['image_presently'] = $attributes['image'];

        return [
            'pageTitle' => 'Update Item category',
            'urlPost' => '/items/categories/' . $attributes['id'], #required
            'attributes' => $attributes
        ];
    }

    public function update($id, Request $request)
    {
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

    public function destroy(Request $request)
    {
        Item::whereIn('id', $request->ids)->delete();

        $response = [
            'status' => true,
            'message' => count($request->ids) . ' Item(s) deleted.',
            'ids' => $request->ids,
        ];

        return json_encode($response);
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
}
