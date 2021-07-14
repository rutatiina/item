<template>

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-md-inline">
                <div class="page-title d-flex">
                    <h4>
                        <i class="icon-price-tags mr-2"></i>
                        <span class="font-weight-semibold">Items</span>
                        <!--<span class="pl-3 small">Manage, products, services, and cost-centers</span>-->
                    </h4>
                    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>

                <!--
                <div class="header-elements d-none">
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
                        <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Bills</span></a>
                    </div>
                </div>
                -->
            </div>

            <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
                <div class="d-flex">
                    <div class="breadcrumb">
                        <a href="/" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                        <span class="breadcrumb-item active">Items</span>
                    </div>

                    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>

                <div class="header-elements">
                    <div class="breadcrumb justify-content-center">
                        <router-link to="/items/create" class=" btn btn-danger btn-sm rounded-round font-weight-bold">
                            <i class="icon-price-tags2 mr-1"></i>
                            New Item
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content border-0 p-0">

            <loading-animation></loading-animation>

            <!-- Basic table -->
            <div class="card shadow-none rounded-0 border-0">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-active">
                                <th scope="col" style="width: 20px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               v-model="rgTableSelectAll"
                                               class="custom-control-input"
                                               id="row-checkbox-all">
                                        <label class="custom-control-label" for="row-checkbox-all"> </label>
                                    </div>
                                </th>
                                <th scope="col" class="font-weight-bold" style="width: 32px;"> </th>
                                <th scope="col" class="font-weight-bold">Name</th>
                                <th scope="col" class="font-weight-bold" nowrap="">SKU</th>
                                <th scope="col" class="font-weight-bold text-right" nowrap="">Rate</th>
                                <th scope="col" class="font-weight-bold text-right" nowrap="">Cost</th>
                                <!--<th scope="col" class="font-weight-bold" nowrap="">Description</th>-->
                                <th scope="col" class="font-weight-bold" nowrap="">Type</th>
                                <th scope="col" class="font-weight-bold" nowrap="">Status</th>
                            </tr>
                        </thead>

                        <rg-tables-state></rg-tables-state>

                        <tbody>
                            <tr v-for="row in tableData.payload.data"
                                @click="onRowClick(row)">
                                <td v-on:click.stop="" class="pr-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               v-model="tableData.selected"
                                               :value="row.id"
                                               number
                                               class="custom-control-input"
                                               :id="'row-checkbox-'+row.id"
                                               isabled>
                                        <label class="custom-control-label" :for="'row-checkbox-'+row.id"> </label>
                                    </div>
                                </td>
                                <td class="cursor-pointer pl-0 pr-0" nowrap>
                                    <img v-if="row.image_url" :src="row.image_url" class="rounded-circle" width="32" height="32" alt="">
                                    <img v-else src="/template/l/global_assets/images/placeholders/placeholder.jpg" class="rounded-circle" width="32" height="32" alt="">
                                </td>
                                <td class="cursor-pointer" nowrap>
                                    <div class="font-weight-semibold">{{row.salutaion}} {{row.name}}</div>
                                    <div class="text-muted">{{row.selling_description}}</div>
                                </td>
                                <td class="cursor-pointer">{{row.sku}}</td>
                                <td class="cursor-pointer text-right">{{$root.numberFormat(row.selling_rate)}}</td>
                                <td class="cursor-pointer text-right">{{$root.numberFormat(row.billing_rate)}}</td>
                                <!--<td class="cursor-pointer">{{row.selling_description}}</td>-->
                                <td class="cursor-pointer">
                                    <span class="badge badge-primary text-capitalize font-weight-semibold">{{row.type}}</span>
                                </td>
                                <td class="cursor-pointer">
                                    <span class="badge badge-success text-capitalize font-weight-semibold">{{row.status}}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <rg-tables-pagination v-bind:table-data-prop="tableData"></rg-tables-pagination>

                </div>

            </div>
            <!-- /basic table -->

        </div>
        <!-- /content area -->


        <!-- Footer -->

        <!-- /footer -->

    </div>
    <!-- /main content -->

</template>

<script>

    export default {
        name: 'ContactsIndex',
        components: {},
        data() {
            return {}
        },
        watch: {
            '$route.query.page': function (page) {
                this.tableData.url = this.$router.currentRoute.path + '?page='+page;
            }
        },
        mounted() {
            this.$root.appMenu('accounting')

            this.tableData.initiate = true

            let currentObj = this;

            if (currentObj.$route.query.page === undefined) {
                currentObj.tableData.url = this.$router.currentRoute.path; //'/crbt/transactions';
            } else {
                currentObj.tableData.url = this.$router.currentRoute.path + '?page='+currentObj.$route.query.page;
            }


        },
        methods: {
            onRowClick(item) {
                this.$router.push({ path: '/items/'+item.id + '/edit' })
            }
        },
        ready:function(){},
        beforeUpdate: function () {},
        updated: function () {
            InputsCheckboxesRadios.initComponents();
        }
    }
</script>
