<?php
use Illuminate\Support\Facades\Route;
use Rutatiina\Item\Http\Controllers\ItemCartegoryController;
use Rutatiina\Item\Http\Controllers\ItemController;
use Rutatiina\Item\Http\Controllers\ItemSubCategoryController;

Route::group(['middleware' => ['web']], function() {
	Route::prefix('docs/items')->group(function () {
		//Route::get('api', 'Rutatiina\Item\Http\Controllers\ApiController@index')->name('items.api.index');
    });
});

Route::group(['middleware' => ['web', 'auth', 'tenant']], function() {

	Route::prefix('items')->group(function () {

		Route::post('routes', 'Rutatiina\Item\Http\Controllers\ItemController@routes')->name('items.routes');
		Route::any('datatables', 'Rutatiina\Item\Http\Controllers\ItemController@datatables')->name('items.datatables');
		Route::post('search', 'Rutatiina\Item\Http\Controllers\ItemController@search')->name('items.search');
		Route::post('vue-search-select-sales', 'Rutatiina\Item\Http\Controllers\ItemController@VueSearchSelectSales');
		Route::post('vue-search-select-purchases', 'Rutatiina\Item\Http\Controllers\ItemController@VueSearchSelectPurchases');
		Route::post('vue-pos', [ItemController::class, 'vuePos']);
		Route::get('categorizations', [ItemController::class, 'categorizations']);
		Route::post('import', 'Rutatiina\Item\Http\Controllers\ItemController@import')->name('items.import');
		Route::patch('update-many', 'Rutatiina\Item\Http\Controllers\ItemController@updateMany')->name('items.update_many');
		Route::patch('deactivate', 'Rutatiina\Item\Http\Controllers\ItemController@deactivate')->name('items.deactivate');
        Route::patch('activate', 'Rutatiina\Item\Http\Controllers\ItemController@activate')->name('items.activate');
		Route::delete('delete', 'Rutatiina\Item\Http\Controllers\ItemController@delete')->name('items.delete');
		Route::post('select2-data/sales', 'Rutatiina\Item\Http\Controllers\Select2DataController@sales')->name('items.select2-data.sales');
		Route::post('select2-data/purchases', 'Rutatiina\Item\Http\Controllers\Select2DataController@purchases')->name('items.select2-data.purchases');
		Route::post('select2-data/inventory', 'Rutatiina\Item\Http\Controllers\Select2DataController@inventory')->name('items.select2-data.inventory');
		Route::post('select2-data/accounts', 'Rutatiina\Item\Http\Controllers\Select2DataController@accounts')->name('items.select2-data.accounts');
		Route::get('component-options', 'Rutatiina\Item\Http\Controllers\ItemController@componentOptions')->name('items.component.options');

		//item category routes
        Route::post('categories/search', [ItemCartegoryController::class, 'search'])->name('items.categories.search');
        Route::patch('categories/deactivate', [ItemCartegoryController::class, 'deactivate'])->name('items.categories.deactivate');
        Route::patch('categories/activate', [ItemCartegoryController::class, 'activate'])->name('items.categories.activate');
        Route::delete('categories/delete', [ItemCartegoryController::class, 'delete'])->name('items.categories.delete');
        Route::post('categories/routes', [ItemCartegoryController::class, 'routes'])->name('items.categories.routes');

        //item sub-category routes
        Route::post('sub-categories/search', [ItemSubCategoryController::class, 'search'])->name('items.sub-categories.search');

    });

    Route::resources([
        'items/categories/{id}/sub-categories' => ItemSubCategoryController::class,
        'items/sub-categories' => ItemSubCategoryController::class,
        'items/categories' => ItemCartegoryController::class,
        'items' => ItemController::class,
    ]);

});
