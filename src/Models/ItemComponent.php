<?php

namespace Rutatiina\Item\Models;

use Rutatiina\Tenant\Scopes\TenantIdScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemComponent extends Model
{
	use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'rg_item_components';

    protected $primaryKey = 'id';

    //protected $dates = ['deleted_at'];

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


}
