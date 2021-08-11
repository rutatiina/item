<?php

namespace Rutatiina\Item\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Rutatiina\Classes\Countries as ClassesCountries;
use Rutatiina\Classes\Currencies as ClassesCurrencies;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Item\Models\Item;
use Rutatiina\Item\Models\ItemCategory;
use Rutatiina\Item\Models\ItemImage;
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
                Rule::unique('Rutatiina\Item\Models\ItemSubCategory')->ignore($request->id, 'id')
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


            $storage_path = '/items/' . date('Y-m');

            $storage = Storage::disk('public_storage');
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
                        'name' => $sub_category['name'],
                        'description' => $sub_category['description'],
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

            //check and delete the profile image if scheduled for
            if (in_array('profile', $request->input('images_deleted', [])))
            {
                Item::where('id', $id)->update([
                    'image_name' => null,
                    'image_path' => null,
                    'image_url' => null
                ]);
            }

            $item = Item::find($id);

            $item->updated_by = Auth::id();
            $item->type = $request->type;
            $item->name = $request->name;
            $item->sku = $request->sku;
            $item->inventory_tracking = $request->inventory_tracking;
            $item->units = (is_numeric($request->units)) ? $request->units : 1;

            $item->selling_rate = floatval($request->selling_rate);
            $item->selling_currency = $request->selling_currency;
            $item->selling_financial_account_code = $request->selling_financial_account_code;
            $item->selling_tax_code = (empty($request->selling_tax_code)) ? null : $request->selling_tax_code;
            $item->selling_tax_inclusive = $request->selling_tax_inclusive;
            $item->selling_description = $request->selling_description;

            $item->billing_rate = floatval($request->billing_rate);
            $item->billing_currency = $request->billing_currency;
            $item->billing_financial_account_code = $request->billing_financial_account_code;
            $item->billing_tax_code = (empty($request->billing_tax_code)) ? null : $request->billing_tax_code;
            $item->billing_tax_inclusive = $request->billing_tax_inclusive;
            $item->billing_description = $request->billing_description;

            if ($request->file('image'))
            {
                $item->image_name = $request->file('image')->getClientOriginalName();
                $item->image_path = (isset($image_path)) ? $image_path : null;
                $item->image_url = (isset($image_url)) ? $image_url : null;
            }

            $item->save();

            //delete the images that are sheduled for delete
            foreach ($request->input('images_deleted', []) as $imagePosition)
            {
                if (is_numeric($imagePosition))
                {
                    ItemImage::where('position', $imagePosition)->delete();
                }
            }

            for ($i = 0; $i <= 7; $i++)
            {
                if ($request->file('images' . $i))
                {
                    $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('images' . $i));

                    //save the item images
                    $ItemImage = new ItemImage;
                    $ItemImage->tenant_id = $tenantId;
                    $ItemImage->item_id = $item->id;
                    $ItemImage->position = $i;
                    $ItemImage->image_name = $request->file('images' . $i)->getClientOriginalName();
                    $ItemImage->image_path = 'storage/' . $file_storage_name;
                    $ItemImage->image_url = url('storage/' . $file_storage_name);
                    $ItemImage->save();
                }
            }

            DB::connection('tenant')->commit();

            return $item;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to update item in database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to update item in database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to update item in database.';
            }

            return false;
        }

    }

    public static function destroy($id)
    {
        //start database transaction
        DB::connection('tenant')->beginTransaction();

        try
        {
            $item = Item::findOrFail($id);

            //Delete affected relations
            $item->images()->delete();

            $item->delete();

            DB::connection('tenant')->commit();

            return true;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to delete item from database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to delete item from database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to delete item from database.';
            }

            return false;
        }
    }

}
