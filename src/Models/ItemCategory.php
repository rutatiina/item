<?php

namespace Rutatiina\Item\Models;

use Rutatiina\Tenant\Scopes\TenantIdScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ItemCategory extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'rg_item_categories';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

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

    public function sub_categories()
    {
        return $this->hasMany('Rutatiina\Item\Models\ItemSubCategory', 'item_category_id', 'id');
    }

    public function getSearchableColumns()
    {
        return Schema::connection('tenant')->getColumnListing($this->table);
    }


}
