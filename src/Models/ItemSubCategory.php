<?php

namespace Rutatiina\Item\Models;

use Rutatiina\Tenant\Scopes\TenantIdScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemSubCategory extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'rg_item_sub_categories';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

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
        $attributes['sub_categories'] = [];

        return $attributes;
    }

    public function categories()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function categorizations()
    {
        return $this->hasMany(ItemCategorization::class, 'item_sub_category_id', 'id');
    }

    //returns the number of time the sub-category is in use
    public function getUsagesAttribute()
    {
        return ItemCategorization::where('item_sub_category_id', $this->id)->count();
    }

}
