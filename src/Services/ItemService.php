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
use Rutatiina\Item\Models\ItemImage;
use Rutatiina\Item\Models\ItemPurchaseTax;
use Rutatiina\Item\Models\ItemSalesTax;
use Rutatiina\Tax\Models\Tax;

class ItemService
{
    public static $errors = [];

    public static function create()
    {
        $attributes = (new Item)->rgGetAttributes();

        $attributes['type'] = 'product';
        $attributes['selling_currency'] = Auth::user()->tenant->base_currency;
        $attributes['billing_currency'] = Auth::user()->tenant->base_currency;
        $attributes['image'] = '/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['image_presently'] = $attributes['image'];
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
        $attributes['images_presently'] = $attributes['images'];
        $attributes['images_deleted'] = [];

        $attributes['sales_taxes'] = [];
        $attributes['purchase_taxes'] = [];
        $attributes['categorizations'] = [];

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
            'type' => 'required',
            'name' => ['required', 'string', 'max:255', 'unique:Rutatiina\Item\Models\Item'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:Rutatiina\Item\Models\Item'],
            'units' => 'required|numeric',

            'selling_rate' => 'required|numeric',
            'selling_currency' => 'required',
            //'selling_financial_account_code' => 'required',
            //'selling_tax_code' => 'required',
            //'selling_tax_inclusive' => 'required',
            //'selling_description' => 'required', //not required

            //'billing_rate' => 'required',
            //'billing_currency' => 'required',
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

            'sales_taxes' => 'array',
            'sales_taxes.*.code' => 'required',
            'purchase_taxes' => 'array',
            'purchase_taxes.*.code' => 'required',
        ];

        if ($update)
        {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                //'unique:tenant.rg_items',
                Rule::unique('Rutatiina\Item\Models\Item')->ignore($request->id, 'id')
            ];
            $rules['sku'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('Rutatiina\Item\Models\Item')->ignore($request->id, 'id')
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

            if ($request->file('image'))
            {
                $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('image'));

                $image_path = 'storage/' . $file_storage_name;
                $image_url = url('storage/' . $file_storage_name);
            }

            $Item = new Item;

            $Item->tenant_id = $tenantId;
            $Item->type = $request->type;
            $Item->barcode = $request->barcode;
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

            if ($request->file('image'))
            {
                $Item->image_name = $request->file('image')->getClientOriginalName();
                $Item->image_path = (isset($image_path)) ? $image_path : null;
                $Item->image_url = (isset($image_url)) ? $image_url : null;
            }

            $Item->save();

            for ($i = 0; $i <= 7; $i++)
            {
                if ($request->file('images' . $i))
                {
                    $file_storage_name = $storage->putFile('/' . $storage_path, $request->file('images' . $i));

                    //save the item images
                    $ItemImage = new ItemImage;
                    $ItemImage->tenant_id = $tenantId;
                    $ItemImage->item_id = $Item->id;
                    $ItemImage->position = $i;
                    $ItemImage->image_name = $request->file('images' . $i)->getClientOriginalName();
                    $ItemImage->image_path = 'storage/' . $file_storage_name;
                    $ItemImage->image_url = url('storage/' . $file_storage_name);
                    $ItemImage->save();
                }
            }

            //store sales_taxes
            $salesTaxes = [];
            if ($request->sales_taxes)
            {
                foreach ($request->sales_taxes as $salesTax)
                {
                    $salesTaxes[] = [
                        'tenant_id' => $tenantId,
                        //'project_id' => null,
                        'tax_code' => $salesTax['code']
                    ];
                }

                $Item->intermediate_sales_taxes()->createMany($salesTaxes);
            }

            //store purchase_taxes
            $purchaseTaxes = [];
            if ($request->purchase_taxes)
            {
                foreach ($request->purchase_taxes as $purchaseTax)
                {
                    $purchaseTaxes[] = [
                        'tenant_id' => $tenantId,
                        //'project_id' => null,
                        'tax_code' => $purchaseTax['code']
                    ];
                }

                $Item->intermediate_purchase_taxes()->createMany($purchaseTaxes);
            }

            //store categorizations
            $categorizations = [];
            if ($request->categorizations)
            {
                foreach ($request->categorizations as $categorization)
                {
                    $categorizations[] = [
                        'tenant_id' => $tenantId,
                        'item_category_id' => $categorization['item_category_id'],
                        'item_sub_category_id' => $categorization['item_sub_category_id'],
                    ];
                }

                $Item->categorizations()->createMany($categorizations);
            }


            DB::connection('tenant')->commit();

            return $Item;

        }
        catch (\Throwable $e)
        {
            DB::connection('tenant')->rollBack();

            Log::critical('Fatal Internal Error: Failed to save item to database');
            Log::critical($e);

            //print_r($e); exit;
            if (App::environment('local'))
            {
                self::$errors[] = 'Error: Failed to save item to database.';
                self::$errors[] = 'File: ' . $e->getFile();
                self::$errors[] = 'Line: ' . $e->getLine();
                self::$errors[] = 'Message: ' . $e->getMessage();
            }
            else
            {
                self::$errors[] = 'Fatal Internal Error: Failed to save item to database.';
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
            $item->barcode = $request->barcode;
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

            //update sales_taxes
            if ($request->sales_taxes)
            {
                //delete records removed
                $selectedSalesTaxes = collect($request->sales_taxes)->pluck('code')->values()->toArray();

                ItemSalesTax::where('item_id', $item->id)
                    ->whereNotIn('tax_code', $selectedSalesTaxes)
                    ->delete();

                foreach ($request->sales_taxes as $salesTax)
                {
                    $item->intermediate_sales_taxes()->firstOrCreate([
                        'tenant_id' => $tenantId,
                        //'project_id' => null,
                        'tax_code' => $salesTax['code']
                    ]);
                }
            }

            //update purchase_taxes
            if ($request->purchase_taxes)
            {
                //delete records removed
                $selectedPurchaseTaxes = collect($request->purchase_taxes)->pluck('code')->values()->toArray();

                ItemPurchaseTax::where('item_id', $item->id)
                    ->whereNotIn('tax_code', $selectedPurchaseTaxes)
                    ->delete();

                foreach ($request->purchase_taxes as $purchaseTax)
                {
                    $item->intermediate_purchase_taxes()->firstOrCreate([
                        'tenant_id' => $tenantId,
                        //'project_id' => null,
                        'tax_code' => $purchaseTax['code']
                    ]);
                }
            }

            //update categorizations
            if ($request->categorizations)
            {
                //soft delete all previous records of Item Categorization
                ItemCategorization::where('item_id', $item->id)->delete();

                foreach ($request->categorizations as $categorization)
                {
                    //undelete the ones that are reselected
                    ItemCategorization::withTrashed()
                        ->where('item_id', $item->id)
                        ->where('item_category_id', $categorization['item_category_id'])
                        ->where('item_sub_category_id', $categorization['item_sub_category_id'])
                        ->restore();

                    $item->categorizations()->firstOrCreate([
                        'tenant_id' => $tenantId,
                        'item_category_id' => $categorization['item_category_id'],
                        'item_sub_category_id' => $categorization['item_sub_category_id'],
                    ]);
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
