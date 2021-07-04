<?php

Route::group(['middleware' => ['web']], function() {
	Route::prefix('docs/items')->group(function () {
		//Route::get('api', 'Rutatiina\Item\Http\Controllers\ApiController@index')->name('items.api.index');
    });
});

Route::group(['middleware' => ['web', 'auth', 'tenant']], function() {

	Route::prefix('items')->group(function () {

		Route::any('datatables', 'Rutatiina\Item\Http\Controllers\ItemController@datatables')->name('items.datatables');
		Route::post('search', 'Rutatiina\Item\Http\Controllers\ItemController@search')->name('items.search');
		Route::post('vue-search-select-sales', 'Rutatiina\Item\Http\Controllers\ItemController@VueSearchSelectSales');
		Route::post('import', 'Rutatiina\Item\Http\Controllers\ItemController@import')->name('items.import');
		Route::post('deactivate', 'Rutatiina\Item\Http\Controllers\ItemController@deactivate')->name('items.deactivate');
		Route::post('delete', 'Rutatiina\Item\Http\Controllers\ItemController@delete')->name('items.delete');
		Route::post('activate', 'Rutatiina\Item\Http\Controllers\ItemController@activate')->name('items.activate');
		Route::post('select2-data/sales', 'Rutatiina\Item\Http\Controllers\Select2DataController@sales')->name('items.select2-data.sales');
		Route::post('select2-data/purchases', 'Rutatiina\Item\Http\Controllers\Select2DataController@purchases')->name('items.select2-data.purchases');
		Route::post('select2-data/inventory', 'Rutatiina\Item\Http\Controllers\Select2DataController@inventory')->name('items.select2-data.inventory');
		Route::post('select2-data/accounts', 'Rutatiina\Item\Http\Controllers\Select2DataController@accounts')->name('items.select2-data.accounts');

		Route::get('{id}/deactivate', 'Rutatiina\Item\Http\Controllers\ItemController@deactivate')->name('items.deactivate');
		Route::get('{id}/delete', 'Rutatiina\Item\Http\Controllers\ItemController@delete')->name('items.delete');
		Route::get('{id}/activate', 'Rutatiina\Item\Http\Controllers\ItemController@activate')->name('items.activate');

    });

	Route::resource('items', 'Rutatiina\Item\Http\Controllers\ItemController');

});
