<?php

namespace Rutatiina\Item\Models;

use Rutatiina\Tax\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Rutatiina\Inventory\Models\Inventory;
use Rutatiina\Tenant\Scopes\TenantIdScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'rg_items';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    protected $casts = [
        'inventory_tracking' => 'integer',
        'made_at_sale' => 'integer',
        'selling_financial_account_code' => 'integer',
        'billing_financial_account_code' => 'integer',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TenantIdScope);
    }

    public function rgGetAttributes()
    {
        $attributes = [];
        $describeTable = \DB::connection('tenant')->select('describe ' . $this->getTable());

        foreach ($describeTable as $row)
        {
            if (in_array($row->Field, ['id', 'created_at', 'updated_at', 'deleted_at', 'tenant_id', 'user_id'])) continue;

            if (in_array($row->Field, ['currencies', 'taxes']))
            {
                $attributes[$row->Field] = [];
                continue;
            }

            if ($row->Default == '[]')
            {
                $attributes[$row->Field] = [];
            }
            else
            {
                $attributes[$row->Field] = $row->Default;
            }
        }

        //add the relationships
        //->Default is always a string and thus does not auto fill the vue select
        $attributes['selling_rate'] = 0; //the default of 0 has to be set to avoid vue / js throwing errors
        $attributes['billing_rate'] = 0; //the default of 0 has to be set to avoid vue / js throwing errors
        $attributes['units'] = 1;
        $attributes['selling_financial_account_code'] = config('financial-accounting.sales_revenue_code'); //Sales revenue
        $attributes['billing_financial_account_code'] = 130500; //Inventory //config('financial-accounting.cost_of_sales_code'); //Cost of Sales

        return $attributes;
    }

    public function getSellingRateAttribute($value)
    {
        return floatval($value);
    }

    public function getBillingRateAttribute($value)
    {
        return floatval($value);
    }

    public function images()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemImage', 'item_id', 'id');
    }

    public function sales_taxes()
    {
        return $this->hasManyThrough(
            Tax::class,
            ItemSalesTax::class,
            'item_id', // Foreign key on the ItemSalesTax table...
            'code', // Foreign key on the Tax table...
            'id', // Local key on the items table...
            'tax_code' // Local key on the ItemSalesTax table...
        );
    }

    public function intermediate_sales_taxes()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemSalesTax', 'item_id', 'id');
    }

    public function purchase_taxes()
    {
        return $this->hasManyThrough(
            Tax::class,
            ItemPurchaseTax::class,
            'item_id', // Foreign key on the ItemSalesTax table...
            'code', // Foreign key on the Tax table...
            'id', // Local key on the items table...
            'tax_code' // Local key on the ItemSalesTax table...
        );
    }

    public function intermediate_purchase_taxes()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemPurchaseTax', 'item_id', 'id');
    }

    public function categorizations()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemCategorization', 'item_id', 'id');
    }

    public function getInventoryAttribute()
    {
        return Inventory::where('item_id', $this->id)
        ->whereIn( 'date', function($query) {
            $query->selectRaw('MAX(date)')
                ->where('item_id', $this->id)
                ->whereNull('deleted_at') //ignore deleted columns
                ->from((new Inventory)->getTable())
                ->groupBy('batch');
        })
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->get();
    }

    public function inventory_records()
    {
        return $this->hasMany(Inventory::class, 'item_id', 'id');
    }

    public function unit_of_measurement()
    {
        return $this->hasOne('Rutatiina\Item\Models\ItemUnitOfMeasurement', 'id', 'unit_of_measurement_id');
    }

    public function components()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemComponent', 'parent_item_id');
    }


}
