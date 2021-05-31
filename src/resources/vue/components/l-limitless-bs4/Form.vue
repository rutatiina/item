<template>

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-md-inline">
                <div class="page-title d-flex">
                    <h4>
                        <i class="icon-file-plus"></i>
                        {{pageTitle}}
                    </h4>
                </div>

            </div>

            <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
                <div class="d-flex">
                    <div class="breadcrumb">
                        <a href="/" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Items</a>
                        <span class="breadcrumb-item active">Create</span>
                    </div>

                    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>

                <div class="header-elements">
                    <div class="breadcrumb justify-content-center">
                        <router-link to="/items" class=" btn btn-danger btn-sm rounded-round font-weight-bold">
                            <i class="icon-price-tags mr-1"></i>
                            Items
                        </router-link>
                    </div>
                </div>

            </div>

        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content border-0 padding-0">

            <!-- Form horizontal -->
            <div class="card shadow-none rounded-0 border-0">

                <div class="card-body p-0">

                    <loading-animation></loading-animation>

                    <form v-if="!this.$root.loading"
                          @submit="formSubmit"
                          action=""
                          method="post"
                          class="max-width-1040"
                          style="margin-bottom: 100px;"
                          autocomplete="off">


                        <div >

                            <fieldset>
                                <div class="form-group row">
                                    <label class="control-label col-sm-2 text-left">
                                        Type
                                    </label>
                                    <div class="col-sm-10">

                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" v-model="attributes.type" value="product" id="item-type-product" checked @change="itemTypeChange">
                                            <label class="custom-control-label" for="item-type-product">Product</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" v-model="attributes.type" value="service" id="item-type-service" @change="itemTypeChange">
                                            <label class="custom-control-label" for="item-type-service">Service</label>
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" v-model="attributes.type" value="cost_center" id="item-type-cost-center" @change="itemTypeChange">
                                            <label class="custom-control-label" for="item-type-cost-center">Cost center</label>
                                        </div>

                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>

                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">
                                        Name:
                                    </label>
                                    <div class="col-lg-10">
                                        <input type="text" v-model="attributes.name" class="form-control input-roundless"
                                               placeholder="Item name">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Units & SKU</label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Units:</span>
                                                </span>
                                            <input type="text" class="form-control" v-model="attributes.units" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <span class="input-group-text">SKU:</span>
                                            </span>
                                            <input type="text" class="form-control" v-model="attributes.sku" placeholder="Stock keeping unit code">
                                            <span class="input-group-append">
                                                <div class="input-group-text">
													<div class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" v-model="attributes.inventory_tracking" value="1" class="custom-control-input" id="inventory_tracking" >
                                                        <label class="custom-control-label" for="inventory_tracking">Track item inventory</label>
                                                    </div>
												</div>
											</span>
                                        </div>

                                    </div>
                                </div>

                            </fieldset>

                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-2 text-left">
                                Pricing /Costing
                            </label>
                            <div class="col-sm-10">

                                <div class="row m-0">

                                    <!-- OPEN: Sales information -->
                                    <div v-if="attributes.type == 'product' || attributes.type == 'service'" class="col-md-6">

                                        <div class="form-group row mr-0">
                                            <span class="badge bg-purple badge-pill">Sales information</span>
                                        </div>

                                        <div class="form-group row mr-0">

                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Rate:</span>
                                                </span>
                                                <input type="text" class="form-control font-weight-bold" v-model="attributes.selling_rate" placeholder="0.00">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">{{attributes.selling_currency}}</span>
                                                </span>
                                            </div>

                                        </div>

                                        <!--
                                        <div class="form-group row mr-0">
                                            <label class="col-auto col-form-label text-right bg-light border rounded-left border-right-0"
                                                   style="white-space: nowrap;">
                                                Selling Account:
                                            </label>
                                            <div class="col pl-0 pr-0">
                                                <model-list-select :list="accounts"
                                                                   v-model="attributes.selling_financial_account_code"
                                                                   option-value="id"
                                                                   option-text="name"
                                                                   class="rounded-left-0"
                                                                   placeholder="select account">
                                                </model-list-select>
                                            </div>
                                        </div>
                                        -->

                                        <!--
                                        <div class="form-group row mr-0">
                                            <label class="col-auto col-form-label text-right bg-light border rounded-left border-right-0"
                                                   style="white-space: nowrap;">
                                                Tax:
                                            </label>
                                            <div class="col-md-6 pl-0 pr-0">
                                                <model-list-select :list="taxes"
                                                                   v-model="attributes.selling_tax_code"
                                                                   option-value="id"
                                                                   option-text="name"
                                                                   class="rounded-0"
                                                                   placeholder="select account">
                                                </model-list-select>
                                            </div>

                                            <div class="col p-0">
                                                <span class="input-group-text h-100 rg-rounded-right-only border-left-0">
                                                    <div class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" v-model="attributes.selling_tax_inclusive" value="1" id="selling_tax_inclusive" checked>
                                                        <label class="custom-control-label" for="selling_tax_inclusive">Inclusive</label>
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                        -->

                                        <div class="form-group row mr-0">

                                            <label>Sales description</label>
                                            <textarea type="text" v-model="attributes.selling_description"
                                                      class="form-control input-roundless" placeholder="Sales description"
                                                      rows="3"></textarea>

                                        </div>

                                    </div>
                                    <!-- CLOSE: Sales information -->

                                    <!-- OPEN: Cost / Purchase information -->
                                    <div v-if="attributes.type == 'product' || attributes.type == 'cost_center'" class="col-md-6">

                                        <div class="form-group row ml-0">
                                            <span class="badge bg-purple badge-pill">Cost / Purchase information</span>
                                        </div>

                                        <div class="form-group row ml-0">

                                            <div class="input-group">
                                                <span class="input-group-prepend">
                                                    <span class="input-group-text">Rate:</span>
                                                </span>
                                                <input type="text" class="form-control font-weight-bold" v-model="attributes.billing_rate" placeholder="0.00">
                                                <span class="input-group-append">
                                                    <span class="input-group-text">{{attributes.billing_currency}}</span>
                                                </span>
                                            </div>

                                        </div>

                                        <!--
                                        <div class="form-group row pl-2">
                                            <label class="col-auto col-form-label text-right bg-light border rounded-left border-right-0"
                                                   style="white-space: nowrap;">
                                                Billing Account:
                                            </label>
                                            <div class="col pl-0 pr-0">
                                                <model-list-select :list="accounts"
                                                                   v-model="attributes.billing_financial_account_code"
                                                                   option-value="id"
                                                                   option-text="name"
                                                                   class="rounded-left-0"
                                                                   placeholder="select account">
                                                </model-list-select>
                                            </div>
                                        </div>
                                        -->

                                        <!--
                                        <div class="form-group row ml-0">
                                            <label class="col-auto col-form-label text-right bg-light border rounded-left border-right-0"
                                                   style="white-space: nowrap;">
                                                Tax:
                                            </label>
                                            <div class="col-md-6 pl-0 pr-0">
                                                <model-list-select :list="taxes"
                                                                   v-model="attributes.billing_tax_code"
                                                                   option-value="id"
                                                                   option-text="name"
                                                                   class="rounded-0"
                                                                   placeholder="select account">
                                                </model-list-select>
                                            </div>

                                            <div class="col p-0">
                                                <span class="input-group-text h-100 rg-rounded-right-only border-left-0">
                                                    <div class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" v-model="attributes.billing_tax_inclusive" value="1" id="billing_tax_inclusive" checked>
                                                        <label class="custom-control-label" for="billing_tax_inclusive">Inclusive</label>
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                        -->

                                        <div class="form-group row ml-0">
                                            <label class="">Billing description</label>
                                            <textarea v-model="attributes.billing_description" class="form-control input-roundless"
                                                      placeholder="Billing description" rows="3"></textarea>

                                        </div>

                                    </div>
                                    <!-- CLOSE: Cost / Purchase information -->

                                </div>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-2 text-left">
                                Images
                            </label>
                            <div class="col-sm-10">

                                <div class="row">

                                    <!-- OPEN: profile picture -->
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-0 text-center" ref="imageHolderDimensions" @click="$refs['image'].click()">
                                            <div class="card-img-actions h-100">
                                                <img class="img-fluid" :src="attributes.image" alt="" :style="'max-height: '+imageMaxHeight+'px; max-width: '+imageMaxWidth+'px; width: auto;'">
                                                <div class="card-img-actions-overlay card-img-top">
                                                    <span class="btn btn-outline border-white border-2 text-white" >
                                                        Select image
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- CLOSE: profile picture -->

                                    <!-- OPEN: other pictures -->
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" ref="imagesHolderDimensions" @click="$refs['images0'].click()">
                                                    <div class="card-img-actions align-middle" :style="'height: '+imagesMaxHeight+'px'">
                                                        <img class="mx-auto" :src="attributes.images[0]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images1'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[1]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images2'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[2]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images3'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[3]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images4'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[4]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images5'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[5]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images6'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[6]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-0 text-center" @click="$refs['images7'].click()">
                                                    <div class="card-img-actions">
                                                        <img class="" :src="attributes.images[7]" alt="" :style="'max-height: '+imagesMaxHeight+'px; max-width: '+imagesMaxWidth+'px; width: auto;'">
                                                        <div class="card-img-actions-overlay card-img-top">
                                                            <span class="btn btn-outline border-white border-2 text-white" >
                                                                Select image
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- CLOSE: other pictures -->

                                </div>

                            </div>
                        </div>

                        <input type="file" @change="onFileChange($event, null)" ref="image" name="image" class="d-none" />
                        <input type="file" @change="onFileChange($event, 0)" ref="images0" name="images0" class="d-none" />
                        <input type="file" @change="onFileChange($event, 1)" ref="images1" name="images1" class="d-none" />
                        <input type="file" @change="onFileChange($event, 2)" ref="images2" name="images2" class="d-none" />
                        <input type="file" @change="onFileChange($event, 3)" ref="images3" name="images3" class="d-none" />
                        <input type="file" @change="onFileChange($event, 4)" ref="images4" name="images4" class="d-none" />
                        <input type="file" @change="onFileChange($event, 5)" ref="images5" name="images5" class="d-none" />
                        <input type="file" @change="onFileChange($event, 6)" ref="images6" name="images6" class="d-none" />
                        <input type="file" @change="onFileChange($event, 7)" ref="images7" name="images7" class="d-none" />


                        <div class="text-left col-md-10 offset-md-2 p-0">
                            <button type="submit" class="btn btn-danger font-weight-bold">
                                <i class="icon-price-tags2 mr-1"></i> {{pageTitle}} - {{attributes.type}}
                            </button>
                        </div>


                    </form>

                </div>
            </div>
            <!-- /form horizontal -->


        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->

</template>

<script>

    export default {
        name: 'ItemsCreate',
        components: {},
        data() {
            return {
                payload:{},
                pageTitle: 'Create Item',
                urlPost: '/items',
                imageMaxHeight: 272,
                imageMaxWidth: 272,
                imagesMaxHeight: 125,
                imagesMaxWidth: 125,
                attributes: {
                    src: null,
                    images: {
                        0: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        1: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        2: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        3: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        4: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        5: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        6: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                        7: '/template/l/global_assets/images/placeholders/placeholder.jpg',
                    }
                },
                countries: [],
                currencies: [],
                accounts: [],
                taxes: []
            }
        },
        mounted() {
            this.$root.appMenu('accounting')
            this.fetchAttributes();

            // this.onResize(); //<< this should never be

        },
        watch: {
            $route: function () {
                this.fetchAttributes()
            }
        },
        methods: {
            async fetchAttributes() {
                //console.log('fetchAttributes')

                try {

                    this.$root.loadingTxn = true

                    return await axios.get(this.$route.fullPath)
                        .then(response => {

                            this.$root.loadingTxn = false
                            this.pageTitle = response.data.pageTitle
                            this.urlPost = response.data.urlPost
                            this.attributes = response.data.attributes
                            this.countries = response.data.countries
                            this.currencies = response.data.currencies
                            this.taxes = response.data.taxes
                            this.accounts = response.data.accounts

                        })
                        .catch(function (error) {
                            // handle error
                            console.log(error); //test
                        })
                        .finally(function (response) {
                            // always executed this is supposed
                        })

                } catch (e) {
                    console.log(e); //test
                }
            },
            itemTypeChange() {},
            formSubmit(e) {

                e.preventDefault();

                // console.log(this.attributes);

                let currentObj = this;

                PNotify.removeAll();

                let PNotifySettings = {
                    title: false, //'Processing',
                    text: 'Please wait as we do our thing',
                    addclass: 'bg-warning-400 border-warning-400',
                    hide: false,
                    buttons: {
                        closer: false,
                        sticker: false
                    }
                };

                let notice = new PNotify(PNotifySettings);

                this.payload = JSON.parse(JSON.stringify(this.attributes));

                delete this.payload.image;
                delete this.payload.images;
                console.log(this.payload);

                let formData = rgFormData(this.payload);

                if ( typeof currentObj.$refs.image.files[0] !== "undefined") formData.append('image', currentObj.$refs.image.files[0]);
                if ( typeof currentObj.$refs.images0.files[0] !== "undefined") formData.append('images0', currentObj.$refs.images0.files[0]);
                if ( typeof currentObj.$refs.images1.files[0] !== "undefined") formData.append('images1', currentObj.$refs.images1.files[0]);
                if ( typeof currentObj.$refs.images2.files[0] !== "undefined") formData.append('images2', currentObj.$refs.images2.files[0]);
                if ( typeof currentObj.$refs.images3.files[0] !== "undefined") formData.append('images3', currentObj.$refs.images3.files[0]);
                if ( typeof currentObj.$refs.images4.files[0] !== "undefined") formData.append('images4', currentObj.$refs.images4.files[0]);
                if ( typeof currentObj.$refs.images5.files[0] !== "undefined") formData.append('images5', currentObj.$refs.images5.files[0]);
                if ( typeof currentObj.$refs.images6.files[0] !== "undefined") formData.append('images6', currentObj.$refs.images6.files[0]);
                if ( typeof currentObj.$refs.images7.files[0] !== "undefined") formData.append('images7', currentObj.$refs.images7.files[0]);

                //this.attributes

                axios.post(currentObj.urlPost, formData)
                    .then(function (response) {

                        //PNotify.removeAll();

                        PNotifySettings.text = response.data.message;

                        if(response.data.status === true) {
                            PNotifySettings.title = 'Success';
                            PNotifySettings.type = 'success';
                            PNotifySettings.addclass = 'bg-success-400 border-success-400';
                        } else {
                            PNotifySettings.title = '! Error';
                            PNotifySettings.type = 'error';
                            PNotifySettings.addclass = 'bg-warning-400 border-warning-400';
                        }

                        //let notice = new PNotify(PNotifySettings);
                        notice.update(PNotifySettings);

                        notice.get().click(function() {
                            notice.remove();
                        });

                        //currentObj.response = response.data;
                    })
                    .catch(function (error) {
                        currentObj.response = error;
                    });
            },
            onFileChange(e, key) {
                // console.log('called onFileChange: ' + key);
                const file = e.target.files[0];
                if (key === null)
                {
                    this.attributes.image = URL.createObjectURL(file);
                }
                else
                {
                    this.attributes.images[key] = URL.createObjectURL(file);
                }
            },
            onResize() {
                this.imagesMaxHeight = this.$refs["imagesHolderDimensions"].offsetWidth;
                this.imagesMaxWidth = this.$refs["imagesHolderDimensions"].offsetWidth;

                this.imageMaxHeight = this.$refs["imageHolderDimensions"].offsetWidth;
                this.imageMaxWidth = this.$refs["imageHolderDimensions"].offsetWidth;

            }
        },
        created() {
            window.addEventListener("resize", this.onResize);
        },
        destroyed() {
            window.removeEventListener("resize", this.onResize);
        },
        updated: function () {
            // window.removeEventListener("resize", this.onResize);
        }
    }
</script>
