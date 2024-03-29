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
use Rutatiina\Item\Models\ItemSubCategory;
use Rutatiina\Item\Services\ItemCategoryService;
use Rutatiina\Item\Services\ItemService;
use Rutatiina\Tax\Models\Tax;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Globals\Services\Countries as ClassesCountries;
use Rutatiina\Globals\Services\Currencies as ClassesCurrencies;
use Rutatiina\Item\Traits\ItemsVueSearchSelect;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use function PHPUnit\Framework\isEmpty;

class ItemSubCategoryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:items.view');
        //$this->middleware('permission:items.create', ['only' => ['create', 'store']]);
        //$this->middleware('permission:items.update', ['only' => ['edit', 'update']]);
        //$this->middleware('permission:items.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, $categoryId = null)
    {
        $per_page = ($request->per_page) ? $request->per_page : 20;

        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        if ($categoryId)
        {
            $itemCategories = ItemSubCategory::where('item_category_id', $categoryId)->paginate($per_page);
        }
        else
        {
            $itemCategories = ItemSubCategory::paginate($per_page);
        }

        return [
            'tableData' => $itemCategories
        ];
    }

    public function create()
    {
        //load the vue version of the app
        if (!FacadesRequest::wantsJson())
        {
            return view('ui.limitless::layout_2-ltr-default.appVue');
        }

        $itemCategory = new ItemSubCategory;
        $attributes = $itemCategory->rgGetAttributes();

        $attributes['image'] = '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg';
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
        //return $request;

        $store = ItemCategoryService::store($request);

        if ($store)
        {
            return [
                'status' => true,
                'messages' => ['Item category successfully saved.']
            ];
        }
        else
        {
            return [
                'status' => false,
                'messages' => ItemCategoryService::$errors
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

        $itemCategory = ItemSubCategory::find($id);
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
        //return $request;
        $itemCategory = ItemCategory::find($request->item_category);

        $query = ItemSubCategory::query();

        foreach ($request->search as $search)
        {
            if (empty($search['value'])) continue;

            $query->where($search['column'], 'like', '%' . $search['value'] . '%');
        }

        if ($request->data_format == 'select2')
        {
            return [
                'item_category' => [
                    'name' => $itemCategory->name,
                ],
                'item_sub_category' => $query->limit(100)->get()
            ];
        }
        else
        {
            return [
                'item_category' => [
                    'name' => $itemCategory->name,
                ],
                'item_sub_category' => $query->paginate(10)
            ];
        }
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
