@extends('accounting::layouts.layout_2.LTR.layout_navbar_sidebar_fixed')

@section('title', 'Items')

@section('head')
    <script src="{{ mix('/template/limitless/layout_2/LTR/default/assets/mix/item.js') }}"></script>
@endsection

@section('content')

    <div class="navbar navbar-default navbar-fixed-top rg_datatable_onselect_btns animate-class-change">
        <button type="button" class="btn btn-link rg_datatable_selected_deactivate" data-url="/items/deactivate/"><i
                    class="icon-alert position-left"></i> Deactivate
        </button>
        <button type="button" class="btn btn-link rg_datatable_selected_activate" data-url="/items/activate/"><i
                    class="icon-info22 position-left"></i> Activate
        </button>
        <button type="button" class="btn btn-link rg_datatable_selected_delete" data-url="/items/delete/"><i
                    class="icon-bin position-left"></i> Delete
        </button>
    </div>


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header" style="border-bottom: 1px solid #ddd;">
            <div class="page-header-content">
                <div class="page-title clearfix">
                    <h1 class="pull-left no-margin text-light">
                        <i class="icon-file-plus position-left"></i> Edit Item
                        <small>Edit Item</small>
                    </h1>

                    <div class="pull-right">

                        <button type="button" class="btn btn-danger btn-labeled pr-20" class="label bg-blue-400" data-href="{{url('items/create')}}"><b><i class="icon-plus22"></i></b> New Item </button>

                        <div class="btn-group btn-xs btn-group-animated no-padding mr-20">
                            <button type="button" class="btn btn-danger btn-labeled pr-20 import_btn"
                                    class="label bg-blue-400" data-import="items"><b><i class="icon-download4"></i></b>
                                Import items
                            </button>
                            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><span
                                        class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="/import_templates/import_items.xlsx"><i class="icon-file-download"></i>
                                        Download template</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">

            <!-- Pagination types -->
            <div class="panel panel-flat no-border no-shadow mt-20">

                <div class="panel-body">

                    <form id="item_modal_form"
                          action="{{route('items.update', $item->id)}}"
                          method="post"
                          class="rg-item-form rg-form-ajax-submit">
                        @csrf
                        @method('PATCH')

                        <div class="col-md-7">

                            <fieldset>
                                <div class="form-group clearfix">
                                    <label class="control-label col-sm-2 text-left">
                                        Type
                                    </label>
                                    <div class="col-sm-10">

                                        <label class="radio-inline">
                                            <input type="radio" name="type" value="product" id="item_type_product"
                                                   checked onchange="rutatiina.item_type_change(this);">
                                            Product
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="type" value="service" id="item_type_service"
                                                   onchange="rutatiina.item_type_change(this);">
                                            Service
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="type" value="cost_center"
                                                   id="item_type_cost_center"
                                                   onchange="rutatiina.item_type_change(this);">
                                            Cost center
                                        </label>

                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>

                                <div class="form-group clearfix">
                                    <label class="col-lg-2 col-form-label">
                                        Name:
                                    </label>
                                    <div class="col-lg-8">
                                        <input type="text" name="name" value="{{$item->name}}" class="form-control input-roundless"
                                               placeholder="Item name">
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <label class="col-lg-2 col-form-label">
                                        SKU :
                                    </label>
                                    <div class="col-lg-8">
                                        <input type="text" name="sku" value="{{$item->sku}}" class="form-control input-roundless"
                                               placeholder="Stock keeping unit code">
                                    </div>
                                </div>

                                <div id="track_inventory" class="form-group clearfix" style="line-height: 35px;">
                                    <label class="col-lg-2 col-form-label"> Units </label>
                                    <div class="col-lg-2 {{--border-right border-grey-300--}}">
                                        <input type="text" name="units" value="{{$item->units}}" class="form-control input-roundless"
                                               placeholder="Units">
                                    </div>
                                    <div class="col-lg-8">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="inventory_tracking" value="1"
                                                   style="margin-top: 10px;">
                                            Track item inventory
                                        </label>
                                    </div>
                                </div>

                            </fieldset>

                        </div>

                        <div class="clearfix"></div>

                        <!--<hr class="no-margin-top" />-->

                        @php

                            $info_class = '';

                            switch($item->type) {
                                case 'product':
                                    $info_class = '';
                                    break;
                                case 'service':
                                    $info_class = 'service_rates';
                                    break;
                                case 'cost_center':
                                    $info_class = 'cost_center_rates';
                                    break;
                                default:
                                    $info_class = '';
                                    break;
                            }

                        @endphp

                        <label class="col-md-1 col-form-label mr-20"> </label>

                        <div id="item_rates" class="form-group col-md-7 {{$info_class}}">

                            <!-- OPEN: Sales information -->
                            <div id="item_sales_info" class="col-md-6">

                                <div class="form-group clearfix">
                                    <span class="label label-default">Sales information</span>
                                </div>

                                <div class="form-group clearfix">

                                    <div class="input-group">
                                        <span class="input-group-addon label-roundless">Rate:</span>
                                        <input type="text" name="selling_rate" value="{{floatval($item->selling_rate)}}"
                                               class="form-control input-roundless" placeholder="0.00"
                                               aria-describedby="basic-addon1">

                                        <span class="input-group-addon label-roundless no-border-left">{{$tenant->base_currency}}</span>
                                        <input type="hidden" name="selling_currency" value="{{$tenant->base_currency}}">

                                        {{--
                                        <select name="selling_currency" class="bootstrap-select  input-roundless" data-width="100px">
                                            <option value="{{$tenant->base_currency}}">{{$tenant->base_currency}}</option>
                                        </select>
                                        --}}

                                    </div>

                                </div>

                                <div class="form-group clearfix">

                                    <div class="input-group">
                                        <span class="input-group-addon label-roundless" id="basic-addon1">
                                            Account:
                                        </span>

                                        <select name="selling_financial_account_code" class="select-search input-roundless">
                                            <option value="0">* Default</option>
                                            @foreach($accounts as $account)
                                                <option value="{{$account->code}}">{{$account->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                </div>

                                <div class="form-group clearfix">
                                    <div class="input-group">
                                        <span class="input-group-addon label-roundless" id="basic-addon1">
                                            Tax:
                                        </span>
                                        <select name="selling_tax_code" class="select-search input-roundless">
                                            <option value="0">* None</option>
                                            @foreach($taxes as $tax)
                                                <option value="{{$tax->value}}"
                                                        data-id="{{$tax->id}}">{{$tax->display_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon switchery-xs  no-border-right">
                                            <input type="checkbox" name="selling_tax_inclusive" value="1"
                                                   class="switchery" checked>
                                        </span>
                                        <span class="input-group-addon no-border-left no-padding-left">Inclusive</span>
                                    </div>
                                </div>

                                <div class="form-group clearfix">

                                    <small>Sales description</small>
                                    <textarea type="text" name="selling_description"
                                              class="form-control input-roundless" placeholder="Sales description"
                                              rows="3">{{$item->selling_description}}</textarea>

                                </div>

                            </div>
                            <!-- CLOSE: Sales information -->

                            <!-- OPEN: Cost / Purchase information -->
                            <div id="item_cost_info" class="col-md-6">

                                <div class="form-group clearfix">
                                    <span class="label label-default">Cost / Purchase information</span>
                                </div>

                                <div class="form-group">

                                    <div class="input-group">
                                        <span class="input-group-addon label-roundless">Rate:</span>
                                        <input type="text" name="billing_rate" value="{{floatval($item->billing_rate)}}"
                                               class="form-control input-roundless" placeholder="0.00">

                                        <span class="input-group-addon label-roundless no-border-left">{{$tenant->base_currency}}</span>
                                        <input type="hidden" name="billing_currency" value="{{$tenant->base_currency}}">

                                        {{--
                                        <select name="billing_currency" class="bootstrap-select  input-roundless" data-width="100px">
                                            <option value="{{$tenant->base_currency}}">{{$tenant->base_currency}}</option>
                                        </select>
                                        --}}
                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="input-group m-input-group">
                                        <span class="input-group-addon label-roundless">
                                            Account:
                                        </span>
                                        <select name="billing_financial_account_code" class="select-search input-roundless">
                                            <option value="0">* Default</option>
                                            @foreach($accounts as $account)
                                                <option value="{{$account->code}}">{{$account->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon label-roundless" id="basic-addon1">
                                            Tax:
                                        </span>
                                        <select name="billing_tax_code" class="select-search input-roundless">
                                            <option value="">* None</option>
                                            @foreach($taxes as $tax)
                                                <option value="{{$tax->value}}"
                                                        data-id="{{$tax->id}}">{{$tax->display_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon switchery-xs  no-border-right">
                                            <input type="checkbox" name="billing_tax_inclusive" value="1"
                                                   class="switchery" checked>
                                        </span>
                                        <span class="input-group-addon no-border-left no-padding-left">Inclusive</span>
                                    </div>
                                </div>

                                <div class="form-group clearfix">

                                    <small>Billing description</small>
                                    <textarea name="billing_description" class="form-control input-roundless"
                                              placeholder="Sales description" rows="3">{{$item->billing_description}}</textarea>

                                </div>

                            </div>
                            <!-- CLOSE: Cost / Purchase information -->

                        </div>


                    </form>

                    <div class="clearfix"></div>

                    <div class="form-group clearfix">
                        <div class="col-md-7">
                            <label class="col-md-2 col-form-label"> </label>
                            <div class="col-lg-10">
                                <button type="button" onclick="rutatiina.form_ajax_submit('#item_modal_form');"
                                        class="btn btn-danger"><i class="icon-check"></i> Save item
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- /pagination types -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->

@endsection