<?php

namespace Rutatiina\Item\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Rutatiina\Globals\Services\Countries as ClassesCountries;
use Rutatiina\Globals\Services\Currencies as ClassesCurrencies;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Item\Models\ItemCategorization;
use Rutatiina\Item\Models\ItemCategory;
use Rutatiina\Item\Models\ItemImage;
use Rutatiina\Item\Models\ItemSubCategory;
use Rutatiina\Tax\Models\Tax;

class ItemCategoryService
{
    public static $errors = [];

    public static function create()
    {
        $attributes = (new Item)->rgGetAttributes();

        $attributes['type'] = 'product';
        $attributes['selling_currency'] = Auth::user()->tenant->base_currency;
        $attributes['billing_currency'] = Auth::user()->tenant->base_currency;
        $attributes['image'] = '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['imagePresently'] = $attributes['image'];
        $attributes['images'] = (object)[
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
            '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg',
        ];
        $attributes['imagesPresently'] = $attributes['images'];
        $attributes['imagesDeleted'] = [];

        return [
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
    }

    public static function edit($id)
    {
        //
    }

    private static function validate($request, $update = false)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:Rutatiina\Item\Models\ItemCategory'],
            'description' => 'nullable',
            'image' => 'mimes:jpg,png,jpeg|max:2048|nullable',

            'sub_categories' => 'array',
            'sub_categories.*.name' => ['required', 'string', 'max:255', 'unique:Rutatiina\Item\Models\ItemSubCategory'],
            'sub_categories.*.description' => 'nullable',
        ];

        if ($update)
        {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                //'unique:tenant.rg_items',
                Rule::unique('Rutatiina\Item\Models\ItemCategory')->ignore($request->id, 'id')
            ];

            $rules['sub_categories.*.name'] = [
                'nullable',
                'string',
                'max:255',
                /*Rule::unique('Rutatiina\Item\Models\ItemSubCategory')->where(function ($query) use ($request) {

                    //get the id's of the sub-categories retained
                    $retainedSubCategoriesIds = collect($request->sub_categories)->pluck('id')->values()->toArray();

                    return $query->whereNotIn('id', $retainedSubCategoriesIds);
                })*/
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            self::$errors = $validator->errors()->all();
            return false;
        }
    }

    public static function store($request)
    {
        $validate = self::validate($request);

        if ($validate === false)
        {
            return false;
        }

        //start database transaction
        DB::connection('tenant')->beginTransaction();

        try
        {
            $tenantId = Auth::user()->tenant->id;


            $storage_path = '/items/categiory/' . date('Y-m');

            $storage = Storage::disk('public');
            if (!$storage->has($storage_path))
            {
                $storage->makeDirectory($storage_path);
            }

            $itemCategory = new ItemCategory;
            $itemCategory->tenant_id = $tenantId;
            $itemCategory->name = $request->name;
            $itemCategory->description = $request->description;
            $itemCategory->status = 'active';

            if ($request->file('image'))
            {
                $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('image'));
                $image_path = 'storage/' . $file_storage_name;
                $image_url = url('storage/' . $file_storage_name);

                $itemCategory->image_name = $request->file('image')->getClientOriginalName();
                $itemCategory->image_path = (isset($image_path)) ? $image_path : null;
                $itemCategory->image_url = (isset($image_url)) ? $image_url : null;
            }

            $itemCategory->save();

            $sub_categories = [];

            if ($request->sub_categories && count($request->sub_categories) > 0)
            {
                foreach ($request->sub_categories as $sub_category)
                {
                    $sub_categories[] = [
                        'tenant_id' => $tenantId,
                        'name' => $sub_category['name'],
                        //'description' => $sub_category['description'], //$sub_category['description'] is not set by the form
                    ];
                }

                $itemCategory->sub_categories()->createMany($sub_categories);
            }

            DB::connection('tenant')->commit();

            return $itemCategory;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to save item category to database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to save item category to database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to save item category to database.';
            }

            return false;
        }
        //*/

    }

    public static function update($id, $request)
    {
        $validate = self::validate($request, true);

        if ($validate === false)
        {
            return false;
        }

        //start database transaction
        DB::connection('tenant')->beginTransaction();

        try
        {
            $tenantId = Auth::user()->tenant->id;


            $storage_path = '/items/categiory/' . date('Y-m');

            $storage = Storage::disk('public');
            if (!$storage->has($storage_path))
            {
                $storage->makeDirectory($storage_path);
            }

            $itemCategory = ItemCategory::find($id);
            //$itemCategory->tenant_id = $tenantId;
            $itemCategory->name = $request->name;
            $itemCategory->description = $request->description;
            $itemCategory->status = 'active';

            if ($request->file('image'))
            {
                $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('image'));
                $image_path = 'storage/' . $file_storage_name;
                $image_url = url('storage/' . $file_storage_name);

                $itemCategory->image_name = $request->file('image')->getClientOriginalName();
                $itemCategory->image_path = (isset($image_path)) ? $image_path : null;
                $itemCategory->image_url = (isset($image_url)) ? $image_url : null;
            }

            $itemCategory->save();

            $sub_categories = [];

            if ($request->sub_categories && count($request->sub_categories) > 0)
            {
                //get the id's of the sub-categories retained
                //the empty value caused by a new sub category was resulting in deleting failing... thuse the ->filter()->all() to fix that
                $retainedSubCategoriesIds = collect($request->sub_categories)->pluck('id')->values()->filter()->all();

                //delete the subcategories that have been deleted
                ItemSubCategory::where('item_category_id', $itemCategory->id)
                    ->whereNotIn('id', $retainedSubCategoriesIds)
                    ->delete();

                //note the *upsert* method of a collection turned out to be the wrong method to use
                foreach ($request->sub_categories as $sub_category)
                {
                    ItemSubCategory::updateOrCreate(
                        [
                            'id' => @$sub_category['id'],
                            'tenant_id' => $tenantId, //since there will be creating involved, this column is a must have
                            'item_category_id' => $itemCategory->id,
                        ],
                        [
                            'name' => $sub_category['name'],
                            //'description' => $sub_category['description']
                        ]
                    );
                }


                $itemCategory->sub_categories()->createMany($sub_categories);
            }
            else
            {
                //delete the subcategories that have been deleted
                ItemSubCategory::where('item_category_id', $itemCategory->id)
                    ->doesntHave('categorizations')
                    ->delete();
            }

            DB::connection('tenant')->commit();

            return $itemCategory;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to update item category to database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to update item category to database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to update item category to database.';
            }

            return false;
        }
        //*/

    }

    public static function destroy($ids)
    {
        //start database transaction
        DB::connection('tenant')->beginTransaction();

        try
        {
            foreach ($ids as $id)
            {
                //deactivate the category
                $itemCategory = ItemCategory::findOrFail($id);
                $itemCategory->status = 'deactivated';
                $itemCategory->save();

                DB::connection('tenant')->commit();

                //check if the category is has any items tagged to it
                if (class_exists(ItemCategorization::class) && ItemCategorization::where('item_category_id', $id)->first())
                {
                    self::$errors = ['Category has items attached to it and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //Delete affected relations
                $itemCategory->sub_categories()->delete();

                $itemCategory->delete();
            }

            DB::connection('tenant')->commit();

            return true;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to delete item category from database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to delete item category from database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to delete item category from database.';
            }

            return false;
        }
    }

}
