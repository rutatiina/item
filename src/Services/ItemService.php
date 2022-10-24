<?php

namespace Rutatiina\Item\Services;

use Rutatiina\Tax\Models\Tax;
use Illuminate\Validation\Rule;
use Rutatiina\Item\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Rutatiina\Item\Models\ItemImage;
use Illuminate\Support\Facades\Storage;
use Rutatiina\Item\Models\ItemSalesTax;
use Illuminate\Support\Facades\Validator;
use Rutatiina\Item\Models\ItemPurchaseTax;
use Rutatiina\Item\Models\ItemCategorization;
use Rutatiina\Item\Models\ItemUnitOfMeasurement;
use Rutatiina\FinancialAccounting\Models\Account;
use Rutatiina\Globals\Services\Countries as ClassesCountries;
use Rutatiina\Globals\Services\Currencies as ClassesCurrencies;

class ItemService
{
    public static $errors = [];

    public static function create()
    {
        $attributes = (new Item)->rgGetAttributes();

        $attributes['type'] = 'product';
        $attributes['selling_currency'] = Auth::user()->tenant->base_currency;
        $attributes['billing_currency'] = Auth::user()->tenant->base_currency;
        $attributes['image'] = '/web/assets/template/l/global_assets/images/placeholders/placeholder.jpg';
        $attributes['image_presently'] = $attributes['image'];
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
        $attributes['images_presently'] = $attributes['images'];
        $attributes['images_deleted'] = [];

        $attributes['sales_taxes'] = [];
        $attributes['purchase_taxes'] = [];
        $attributes['categorizations'] = [];
        $attributes['components'] = [];

        return [
            'pageTitle' => 'Create Item',
            'urlPost' => '/items', #required
            'currencies' => ClassesCurrencies::en_IN(),
            'countries' => ClassesCountries::ungrouped(),
            'units_of_measurement' => ItemUnitOfMeasurement::select(['id', 'name'])->get(),
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
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('Rutatiina\Item\Models\Item')
                ->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'))->whereNull('deleted_at');
                })
            ],
            'sku' => [
                'nullable', 
                'string', 
                'max:255',
                Rule::unique('Rutatiina\Item\Models\Item')
                ->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'))->whereNull('deleted_at');
                })
            ],
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
                Rule::unique('Rutatiina\Item\Models\Item')
                ->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'))->whereNull('deleted_at');
                })
                ->ignore($request->id, 'id')
            ];
            $rules['sku'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('Rutatiina\Item\Models\Item')
                ->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'))->whereNull('deleted_at');
                })
                ->ignore($request->id, 'id')
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

            $storage_path = 'items/' . date('Y-m');

            $storage = Storage::disk('public');
            if (!$storage->has($storage_path))
            {
                $storage->makeDirectory($storage_path);
            }

            if ($request->file('image'))
            {
                $file_storage_name = $storage->putFile($storage_path, $request->file('image'));

                $image_path = 'storage/' . $file_storage_name;
                $image_url = url('storage/' . $file_storage_name);
            }

            $Item = new Item;

            $Item->tenant_id = $tenantId;
            $Item->type = $request->type;
            $Item->barcode = $request->barcode;
            $Item->name = $request->name;
            $Item->sku = $request->sku;
            $Item->inventory_tracking = ($request->inventory_tracking == 'true') ? 1 : 0;
            $Item->units = 1; //(is_numeric($request->units)) ? $request->units : 1; //since components were introduced, there is no need for units

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

            $storage = Storage::disk('public');
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
            $item->inventory_tracking = ($request->inventory_tracking == 'true') ? 1 : 0;
            $item->units = 1; //(is_numeric($request->units)) ? $request->units : 1; //since components were introduced, there is no need for units

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
                $selectedSalesTaxes = collect($request->sales_taxes)->pluck('code')->values()->filter()->all();

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
            else
            {
                //delete all sales taxes info since items is being edited and NO $request->sales_taxes info posted
                ItemSalesTax::where('item_id', $item->id)->delete();
            }

            //update purchase_taxes
            if ($request->purchase_taxes)
            {
                //delete records removed
                $selectedPurchaseTaxes = collect($request->purchase_taxes)->pluck('code')->values()->filter()->all();

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
            else
            {
                //delete all purchas taxes info since items is being edited and NO $request->purchase_taxes info posted
                ItemPurchaseTax::where('item_id', $item->id)->delete();
            }

            //update categorizations
            if ($request->categorizations)
            {
                $selectedCategorizationsIds = collect($request->categorizations)->pluck('id')->values()->filter()->all();

                //soft delete removed records of Item Categorization
                ItemCategorization::where('item_id', $item->id)
                    ->whereNotIn('id', $selectedCategorizationsIds)
                    ->delete();

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
            else
            {
                ItemCategorization::where('item_id', $item->id)->delete();
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

    public static function destroy($ids)
    {
        //start database transaction
        DB::connection('tenant')->beginTransaction();

        try
        {
            foreach ($ids as $id)
            {
                //deactivate the contact
                $item = Item::find($id);
                $item->status = 'deactivated';
                $item->save();

                DB::connection('tenant')->commit();

                #ckeck if contact is attached to any sales transactions

                //estimates
                if (class_exists(\Rutatiina\Estimate\Models\EstimateItem::class) && \Rutatiina\Estimate\Models\EstimateItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Estimate and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //retainer invoices
                if (class_exists(\Rutatiina\RetainerInvoice\Models\RetainerInvoiceItem::class) && \Rutatiina\RetainerInvoice\Models\RetainerInvoiceItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Retainer Invoice and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //sales orders
                if (class_exists(\Rutatiina\SalesOrder\Models\SalesOrderItem::class) && \Rutatiina\SalesOrder\Models\SalesOrderItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Sales Order and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //invoices
                if (class_exists(\Rutatiina\Invoice\Models\InvoiceItem::class) && \Rutatiina\Invoice\Models\InvoiceItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Invoice and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //payment received - transactions dont have item_id

                //recurring invoices
                if (class_exists(\Rutatiina\Invoice\Models\RecurringInvoiceItem::class) && \Rutatiina\Invoice\Models\RecurringInvoiceItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Recurring Invoice and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //credit notes
                if (class_exists(\Rutatiina\CreditNote\Models\CreditNoteItem::class) && \Rutatiina\CreditNote\Models\CreditNoteItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Credit Note and thus cannot be deleted but only deactivated.'];
                    return false;
                }


                #ckeck if contact is attached to any purchases transactions

                //expenses - transactions dont have item_id

                //recurring expenses - transactions dont have item_id

                //purchase orders
                if (class_exists(\Rutatiina\PurchaseOrder\Models\PurchaseOrderItem::class) && \Rutatiina\PurchaseOrder\Models\PurchaseOrderItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Purchase Order and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //bills
                if (class_exists(\Rutatiina\Bill\Models\BillItem::class) && \Rutatiina\Bill\Models\BillItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Bill and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //payment made - transactions dont have item_id

                //recurring bill
                if (class_exists(\Rutatiina\Bill\Models\RecurringBillItem::class) && \Rutatiina\Bill\Models\RecurringBillItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Recurring Bill and thus cannot be deleted but only deactivated.'];
                    return false;
                }

                //debit notes
                if (class_exists(\Rutatiina\DebitNote\Models\DebitNoteItem::class) && \Rutatiina\DebitNote\Models\DebitNoteItem::where('item_id', $id)->first())
                {
                    self::$errors = ['Item is attached to an Debit Note and thus cannot be deleted but only deactivated.'];
                    return  false;
                }

                //if all the bove conditions are passed: Delete the contact
                $item->images()->delete(); //todo also delete the image files from storage
                $item->sales_taxes()->delete();
                $item->intermediate_sales_taxes()->delete();
                $item->purchase_taxes()->delete();
                $item->intermediate_purchase_taxes()->delete();
                $item->categorizations()->delete();
                $item->delete();
            }

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
