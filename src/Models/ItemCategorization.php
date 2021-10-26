<?php

namespace Rutatiina\Item\Models;

use Rutatiina\Tenant\Scopes\TenantIdScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategorization extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'rg_item_categorizations';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

    protected $appends = [
        'item_category_name',
        'item_sub_category_name'
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

    public function getItemCategoryNameAttribute()
    {
        return ItemCategory::find($this->item_category_id)->name;
    }

    public function getItemSubCategoryNameAttribute()
    {
        return optional(ItemSubCategory::find($this->item_sub_category_id))->name;
    }

}
