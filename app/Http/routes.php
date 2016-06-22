<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => config('kommercio.backend_prefix'), 'namespace' => 'Backend', 'middleware' => 'backend.access'], function(){
        // Authentication Routes...
        Route::get('login', [
            'as' => 'backend.login_form',
            'uses' => 'Auth\AuthController@showLoginForm'
        ]);

        Route::post('login', [
            'as' => 'backend.login',
            'uses' => 'Auth\AuthController@login'
        ]);

        Route::get('logout', [
            'as' => 'backend.logout',
            'uses' => 'Auth\AuthController@logout'
        ]);

        // Password Reset Routes...
        Route::get('password/reset/{token?}', [
            'as' => 'backend.password.form',
            'uses' => 'Auth\PasswordController@showResetForm'
        ]);

        Route::post('password/email', [
            'as' => 'backend.password.email',
            'uses' => 'Auth\PasswordController@sendResetLinkEmail'
        ]);

        Route::post('password/reset', [
            'as' => 'backend.password.reset',
            'uses' => 'Auth\PasswordController@reset'
        ]);

        Route::group(['middleware' => ['backend.auth']], function(){
            Route::get('/', [
                'as' => 'backend.dashboard',
                'uses' => 'ChamberController@dashboard'
            ]);

            Route::group(['prefix' => 'account'], function(){
                Route::any('settings/credentials', [
                    'as' => 'backend.account.credentials',
                    'uses' => 'AccountController@credentials'
                ]);

                Route::any('settings/profile', [
                    'as' => 'backend.account.profile',
                    'uses' => 'AccountController@profile'
                ]);
            });

            Route::group(['prefix' => 'file'], function(){
                Route::post('upload', [
                    'as' => 'backend.file.upload',
                    'uses' => 'FileController@upload'
                ]);
            });

            //Catalog
            Route::group(['prefix' => 'catalog', 'namespace' => 'Catalog'], function(){
                Route::group(['prefix' => 'category'], function(){
                    Route::get('index/{parent?}', [
                        'as' => 'backend.catalog.category.index',
                        'uses' => 'CategoryController@index',
                        'permissions' => ['view_product_category']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.catalog.category.create',
                        'uses' => 'CategoryController@create',
                        'permissions' => ['create_product_category']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.catalog.category.store',
                        'uses' => 'CategoryController@store',
                        'permissions' => ['create_product_category']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.catalog.category.edit',
                        'uses' => 'CategoryController@edit',
                        'permissions' => ['edit_product_category']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.catalog.category.update',
                        'uses' => 'CategoryController@update',
                        'permissions' => ['edit_product_category']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.catalog.category.delete',
                        'uses' => 'CategoryController@delete',
                        'permissions' => ['delete_product_category']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.catalog.category.reorder',
                        'uses' => 'CategoryController@reorder',
                        'permissions' => ['edit_product_category']
                    ]);

                    Route::get('autocomplete', [
                        'as' => 'backend.catalog.category.autocomplete',
                        'uses' => 'CategoryController@autocomplete',
                        'permissions' => ['view_product_category']
                    ]);
                });

                Route::group(['prefix' => 'product'], function(){
                    Route::any('index', [
                        'as' => 'backend.catalog.product.index',
                        'uses' => 'ProductController@index',
                        'permissions' => ['view_product']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.catalog.product.create',
                        'uses' => 'ProductController@create',
                        'permissions' => ['create_product']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.catalog.product.store',
                        'uses' => 'ProductController@store',
                        'permissions' => ['create_product']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.catalog.product.edit',
                        'uses' => 'ProductController@edit',
                        'permissions' => ['edit_product']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.catalog.product.update',
                        'uses' => 'ProductController@update',
                        'permissions' => ['edit_product']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.catalog.product.delete',
                        'uses' => 'ProductController@delete',
                        'permissions' => ['delete_product']
                    ]);

                    Route::post('{id}/feature/index', [
                        'as' => 'backend.catalog.product.feature_index',
                        'uses' => 'ProductController@featureIndex',
                        'permissions' => ['edit_product']
                    ]);

                    Route::get('{id}/variation/index', [
                        'as' => 'backend.catalog.product.variation_index',
                        'uses' => 'ProductController@variationIndex',
                        'permissions' => ['view_product']
                    ]);

                    Route::post('{id}/variation/form/{variation_id?}', [
                        'as' => 'backend.catalog.product.variation_form',
                        'uses' => 'ProductController@variationForm',
                        'permissions' => ['edit_product']
                    ]);

                    Route::post('{id}/variation/save/{variation_id?}', [
                        'as' => 'backend.catalog.product.variation_save',
                        'uses' => 'ProductController@variationSave',
                        'permissions' => ['edit_product']
                    ]);

                    Route::get('autocomplete', [
                        'as' => 'backend.catalog.product.autocomplete',
                        'uses' => 'ProductController@autocomplete',
                        'permissions' => ['view_product']
                    ]);

                    Route::get('availability/{id}', [
                        'as' => 'backend.catalog.product.availability',
                        'uses' => 'ProductController@availability',
                        'permissions' => ['view_product']
                    ]);
                });

                Route::group(['prefix' => 'product-attribute'], function(){
                    Route::any('index', [
                        'as' => 'backend.catalog.product_attribute.index',
                        'uses' => 'ProductAttributeController@index',
                        'permissions' => ['view_product_attribute']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.catalog.product_attribute.create',
                        'uses' => 'ProductAttributeController@create',
                        'permissions' => ['create_product_attribute']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.catalog.product_attribute.store',
                        'uses' => 'ProductAttributeController@store',
                        'permissions' => ['create_product_attribute']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.catalog.product_attribute.edit',
                        'uses' => 'ProductAttributeController@edit',
                        'permissions' => ['edit_product_attribute']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.catalog.product_attribute.update',
                        'uses' => 'ProductAttributeController@update',
                        'permissions' => ['edit_product_attribute']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.catalog.product_attribute.delete',
                        'uses' => 'ProductAttributeController@delete',
                        'permissions' => ['delete_product_attribute']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.catalog.product_attribute.reorder',
                        'uses' => 'ProductAttributeController@reorder',
                        'permissions' => ['edit_product_attribute']
                    ]);

                    Route::group(['prefix' => 'value/{attribute_id}', 'permissions' => ['edit_product_attribute']], function(){
                        Route::any('index', [
                            'as' => 'backend.catalog.product_attribute.value.index',
                            'uses' => 'ProductAttributeValueController@index'
                        ]);

                        Route::get('create', [
                            'as' => 'backend.catalog.product_attribute.value.create',
                            'uses' => 'ProductAttributeValueController@create'
                        ]);

                        Route::post('store', [
                            'as' => 'backend.catalog.product_attribute.value.store',
                            'uses' => 'ProductAttributeValueController@store'
                        ]);

                        Route::get('edit/{id}', [
                            'as' => 'backend.catalog.product_attribute.value.edit',
                            'uses' => 'ProductAttributeValueController@edit'
                        ]);

                        Route::post('update/{id}', [
                            'as' => 'backend.catalog.product_attribute.value.update',
                            'uses' => 'ProductAttributeValueController@update'
                        ]);

                        Route::post('delete/{id}', [
                            'as' => 'backend.catalog.product_attribute.value.delete',
                            'uses' => 'ProductAttributeValueController@delete'
                        ]);

                        Route::post('reorder', [
                            'as' => 'backend.catalog.product_attribute.value.reorder',
                            'uses' => 'ProductAttributeValueController@reorder'
                        ]);
                    });
                });

                Route::group(['prefix' => 'product-feature'], function(){
                    Route::any('index', [
                        'as' => 'backend.catalog.product_feature.index',
                        'uses' => 'ProductFeatureController@index',
                        'permissions' => ['view_product_feature']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.catalog.product_feature.create',
                        'uses' => 'ProductFeatureController@create',
                        'permissions' => ['create_product_feature']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.catalog.product_feature.store',
                        'uses' => 'ProductFeatureController@store',
                        'permissions' => ['create_product_feature']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.catalog.product_feature.edit',
                        'uses' => 'ProductFeatureController@edit',
                        'permissions' => ['edit_product_feature']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.catalog.product_feature.update',
                        'uses' => 'ProductFeatureController@update',
                        'permissions' => ['edit_product_feature']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.catalog.product_feature.delete',
                        'uses' => 'ProductFeatureController@delete',
                        'permissions' => ['delete_product_feature']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.catalog.product_feature.reorder',
                        'uses' => 'ProductFeatureController@reorder',
                        'permissions' => ['edit_product_feature']
                    ]);

                    Route::group(['prefix' => 'value/{feature_id}', 'permissions' => ['edit_product_feature']], function(){
                        Route::any('index', [
                            'as' => 'backend.catalog.product_feature.value.index',
                            'uses' => 'ProductFeatureValueController@index'
                        ]);

                        Route::get('create', [
                            'as' => 'backend.catalog.product_feature.value.create',
                            'uses' => 'ProductFeatureValueController@create'
                        ]);

                        Route::post('store', [
                            'as' => 'backend.catalog.product_feature.value.store',
                            'uses' => 'ProductFeatureValueController@store'
                        ]);

                        Route::get('edit/{id}', [
                            'as' => 'backend.catalog.product_feature.value.edit',
                            'uses' => 'ProductFeatureValueController@edit'
                        ]);

                        Route::post('update/{id}', [
                            'as' => 'backend.catalog.product_feature.value.update',
                            'uses' => 'ProductFeatureValueController@update'
                        ]);

                        Route::post('delete/{id}', [
                            'as' => 'backend.catalog.product_feature.value.delete',
                            'uses' => 'ProductFeatureValueController@delete'
                        ]);

                        Route::post('reorder', [
                            'as' => 'backend.catalog.product_feature.value.reorder',
                            'uses' => 'ProductFeatureValueController@reorder'
                        ]);
                    });
                });

                Route::group(['prefix' => 'manufacturer'], function(){
                    Route::get('index', [
                        'as' => 'backend.catalog.manufacturer.index',
                        'uses' => 'ManufacturerController@index',
                        'permissions' => ['view_manufacturer']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.catalog.manufacturer.create',
                        'uses' => 'ManufacturerController@create',
                        'permissions' => ['create_manufacturer']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.catalog.manufacturer.store',
                        'uses' => 'ManufacturerController@store',
                        'permissions' => ['create_manufacturer']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.catalog.manufacturer.edit',
                        'uses' => 'ManufacturerController@edit',
                        'permissions' => ['edit_manufacturer']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.catalog.manufacturer.update',
                        'uses' => 'ManufacturerController@update',
                        'permissions' => ['edit_manufacturer']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.catalog.manufacturer.delete',
                        'uses' => 'ManufacturerController@delete',
                        'permissions' => ['delete_manufacturer']
                    ]);
                });
            });

            //Price Rule
            Route::group(['prefix' => 'price-rule', 'namespace' => 'PriceRule'], function(){
                Route::group(['prefix' => 'product'], function(){
                    Route::get('{product_id}/mini-index', [
                        'as' => 'backend.price_rule.product.mini_index',
                        'uses' => 'ProductPriceRuleController@mini_index',
                        'permissions' => ['view_product_price_rule']
                    ]);

                    Route::post('{product_id}/mini-form/{id?}', [
                        'as' => 'backend.price_rule.product.mini_form',
                        'uses' => 'ProductPriceRuleController@mini_form',
                        'permissions' => ['edit_product_price_rule']
                    ]);

                    Route::post('{product_id}/mini-save/{id?}', [
                        'as' => 'backend.price_rule.product.mini_save',
                        'uses' => 'ProductPriceRuleController@mini_save',
                        'permissions' => ['edit_product_price_rule']
                    ]);

                    Route::get('index', [
                        'as' => 'backend.price_rule.product.index',
                        'uses' => 'ProductPriceRuleController@index',
                        'permissions' => ['view_product_price_rule']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.price_rule.product.create',
                        'uses' => 'ProductPriceRuleController@create',
                        'permissions' => ['create_product_price_rule']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.price_rule.product.store',
                        'uses' => 'ProductPriceRuleController@store',
                        'permissions' => ['create_product_price_rule']
                    ]);

                    Route::get('{id}/edit', [
                        'as' => 'backend.price_rule.product.edit',
                        'uses' => 'ProductPriceRuleController@edit',
                        'permissions' => ['edit_product_price_rule']
                    ]);

                    Route::post('{id}/update', [
                        'as' => 'backend.price_rule.product.update',
                        'uses' => 'ProductPriceRuleController@update',
                        'permissions' => ['edit_product_price_rule']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.price_rule.product.delete',
                        'uses' => 'ProductPriceRuleController@delete',
                        'permissions' => ['delete_product_price_rule']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.price_rule.product.reorder',
                        'uses' => 'ProductPriceRuleController@reorder',
                        'permissions' => ['edit_product_price_rule']
                    ]);
                });

                Route::group(['prefix' => 'cart'], function(){
                    Route::get('index', [
                        'as' => 'backend.price_rule.cart.index',
                        'uses' => 'CartPriceRuleController@index',
                        'permissions' => ['view_cart_price_rule']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.price_rule.cart.create',
                        'uses' => 'CartPriceRuleController@create',
                        'permissions' => ['create_cart_price_rule']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.price_rule.cart.store',
                        'uses' => 'CartPriceRuleController@store',
                        'permissions' => ['create_cart_price_rule']
                    ]);

                    Route::get('{id}/edit', [
                        'as' => 'backend.price_rule.cart.edit',
                        'uses' => 'CartPriceRuleController@edit',
                        'permissions' => ['edit_cart_price_rule']
                    ]);

                    Route::post('{id}/update', [
                        'as' => 'backend.price_rule.cart.update',
                        'uses' => 'CartPriceRuleController@update',
                        'permissions' => ['edit_cart_price_rule']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.price_rule.cart.delete',
                        'uses' => 'CartPriceRuleController@delete',
                        'permissions' => ['delete_cart_price_rule']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.price_rule.cart.reorder',
                        'uses' => 'CartPriceRuleController@reorder',
                        'permissions' => ['edit_cart_price_rule']
                    ]);
                });
            });

            //Order
            Route::group(['prefix' => 'sales', 'namespace' => 'Sales'], function(){
                Route::group(['prefix' => 'order'], function(){
                    Route::any('index', [
                        'as' => 'backend.sales.order.index',
                        'uses' => 'OrderController@index',
                        'permissions' => ['view_order']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.sales.order.create',
                        'uses' => 'OrderController@create',
                        'permissions' => ['create_order']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.sales.order.store',
                        'uses' => 'OrderController@store',
                        'permissions' => ['create_order']
                    ]);

                    Route::get('view/{id}', [
                        'as' => 'backend.sales.order.view',
                        'uses' => 'OrderController@view',
                        'permissions' => ['view_order']
                    ]);

                    Route::get('print/{id}', [
                        'as' => 'backend.sales.order.print',
                        'uses' => 'OrderController@printOrder',
                        'permissions' => ['view_order']
                    ]);

                    Route::get('delete/all', [
                        'as' => 'backend.sales.order.delete_all',
                        'uses' => 'OrderController@deleteAll',
                        'permissions' => ['delete_order']
                    ]);

                    Route::group(['middleware' => ['backend.order_editable']], function(){
                        Route::get('edit/{id}', [
                            'as' => 'backend.sales.order.edit',
                            'uses' => 'OrderController@edit',
                            'permissions' => ['edit_order']
                        ]);

                        Route::post('update/{id}', [
                            'as' => 'backend.sales.order.update',
                            'uses' => 'OrderController@update',
                            'permissions' => ['edit_order']
                        ]);

                        Route::post('delete/{id}', [
                            'as' => 'backend.sales.order.delete',
                            'uses' => 'OrderController@delete',
                            'middleware' => ['backend.order_deleteable'],
                            'permissions' => ['delete_order']
                        ]);
                    });

                    Route::any('process/{action}/{id?}', [
                        'as' => 'backend.sales.order.process',
                        'uses' => 'OrderController@process',
                        'permissions' => ['process_order']
                    ]);

                    Route::post('copy/customer_information/{type}/{profile_id?}', [
                        'as' => 'backend.sales.order.copy_customer_information',
                        'uses' => 'OrderController@copyCustomerInformation',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::post('line_item/{type}/row/{id?}', [
                        'as' => 'backend.sales.order.line_item.row',
                        'uses' => 'OrderController@lineItemRow',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::any('shipping/options', [
                        'as' => 'backend.sales.order.shipping_options',
                        'uses' => 'OrderController@shippingOptions',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::post('order-cart-rules/get', [
                        'as' => 'backend.sales.order.get_cart_rules',
                        'uses' => 'OrderController@getCartRules',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::post('coupon/add', [
                        'as' => 'backend.sales.order.add_coupon',
                        'uses' => 'OrderController@addCoupon',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::post('coupon/{id}/remove', [
                        'as' => 'backend.sales.order.remove_coupon',
                        'uses' => 'OrderController@removeCoupon',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::group(['prefix' => 'payment'], function(){
                        Route::get('{order_id}/index', [
                            'as' => 'backend.sales.order.payment.index',
                            'uses' => 'PaymentController@orderPaymentIndex',
                            'permissions' => ['view_payment']
                        ]);

                        Route::get('{order_id}/form', [
                            'as' => 'backend.sales.order.payment.form',
                            'uses' => 'PaymentController@orderPaymentForm',
                            'permissions' => ['create_payment']
                        ]);

                        Route::post('{order_id}/save', [
                            'as' => 'backend.sales.order.payment.save',
                            'uses' => 'PaymentController@orderPaymentSave',
                            'permissions' => ['create_payment']
                        ]);

                        Route::any('process/{action}/{id}', [
                            'as' => 'backend.sales.order.payment.process',
                            'uses' => 'PaymentController@process',
                            'permissions' => ['confirm_payment', 'void_payment']
                        ]);
                    });
                });

                //Order Limits
                Route::group(['prefix' => 'order-limit'], function(){
                    Route::get('{type}/index', [
                        'as' => 'backend.order_limit.index',
                        'uses' => 'OrderLimitController@index',
                        'permissions' => ['view_order_limit']
                    ]);

                    Route::get('{type}/create', [
                        'as' => 'backend.order_limit.create',
                        'uses' => 'OrderLimitController@create',
                        'permissions' => ['create_order_limit']
                    ]);

                    Route::post('{type}/store', [
                        'as' => 'backend.order_limit.store',
                        'uses' => 'OrderLimitController@store',
                        'permissions' => ['create_order_limit']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.order_limit.edit',
                        'uses' => 'OrderLimitController@edit',
                        'permissions' => ['edit_order_limit']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.order_limit.update',
                        'uses' => 'OrderLimitController@update',
                        'permissions' => ['edit_order_limit']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.order_limit.delete',
                        'uses' => 'OrderLimitController@delete',
                        'permissions' => ['delete_order_limit']
                    ]);
                });
            });

            //Configurations
            Route::group(['prefix' => 'configuration'], function(){
                //Store
                Route::group(['prefix' => 'store', 'namespace' => 'Store'], function(){
                    Route::get('index', [
                        'as' => 'backend.store.index',
                        'uses' => 'StoreController@index',
                        'permissions' => ['view_store']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.store.create',
                        'uses' => 'StoreController@create',
                        'permissions' => ['create_store']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.store.store',
                        'uses' => 'StoreController@store',
                        'permissions' => ['create_store']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.store.edit',
                        'uses' => 'StoreController@edit',
                        'permissions' => ['edit_store']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.store.update',
                        'uses' => 'StoreController@update',
                        'permissions' => ['edit_store']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.store.delete',
                        'uses' => 'StoreController@delete',
                        'permissions' => ['delete_store']
                    ]);
                });

                //Payment Methods
                Route::group(['prefix' => 'payment-method', 'namespace' => 'PaymentMethod'], function(){
                    Route::get('index', [
                        'as' => 'backend.payment_method.index',
                        'uses' => 'PaymentMethodController@index',
                        'permissions' => ['view_payment_method']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.payment_method.create',
                        'uses' => 'PaymentMethodController@create',
                        'permissions' => ['create_payment_method']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.payment_method.store',
                        'uses' => 'PaymentMethodController@store',
                        'permissions' => ['create_payment_method']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.payment_method.edit',
                        'uses' => 'PaymentMethodController@edit',
                        'permissions' => ['edit_payment_method']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.payment_method.update',
                        'uses' => 'PaymentMethodController@update',
                        'permissions' => ['edit_payment_method']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.payment_method.delete',
                        'uses' => 'PaymentMethodController@delete',
                        'permissions' => ['delete_payment_method']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.payment_method.reorder',
                        'uses' => 'PaymentMethodController@reorder',
                        'permissions' => ['edit_payment_method']
                    ]);
                });

                //Shipping Methods
                Route::group(['prefix' => 'shipping-method', 'namespace' => 'ShippingMethod'], function(){
                    Route::get('index', [
                        'as' => 'backend.shipping_method.index',
                        'uses' => 'ShippingMethodController@index',
                        'permissions' => ['view_shipping_method']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.shipping_method.create',
                        'uses' => 'ShippingMethodController@create',
                        'permissions' => ['create_shipping_method']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.shipping_method.store',
                        'uses' => 'ShippingMethodController@store',
                        'permissions' => ['create_shipping_method']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.shipping_method.edit',
                        'uses' => 'ShippingMethodController@edit',
                        'permissions' => ['edit_shipping_method']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.shipping_method.update',
                        'uses' => 'ShippingMethodController@update',
                        'permissions' => ['edit_shipping_method']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.shipping_method.delete',
                        'uses' => 'ShippingMethodController@delete',
                        'permissions' => ['delete_shipping_method']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.shipping_method.reorder',
                        'uses' => 'ShippingMethodController@reorder',
                        'permissions' => ['edit_shipping_method']
                    ]);
                });

                //Address
                Route::group(['prefix' => 'address', 'namespace' => 'Address'], function(){
                    Route::any('{type}/index', [
                        'as' => 'backend.configuration.address.index',
                        'uses' => 'AddressController@index',
                        'permissions' => ['view_address']
                    ]);

                    Route::get('{type}/create', [
                        'as' => 'backend.configuration.address.create',
                        'uses' => 'AddressController@create',
                        'permissions' => ['create_address']
                    ]);

                    Route::post('{type}/store', [
                        'as' => 'backend.configuration.address.store',
                        'uses' => 'AddressController@store',
                        'permissions' => ['create_address']
                    ]);

                    Route::get('{type}/edit/{id}', [
                        'as' => 'backend.configuration.address.edit',
                        'uses' => 'AddressController@edit',
                        'permissions' => ['edit_address']
                    ]);

                    Route::post('{type}/update/{id}', [
                        'as' => 'backend.configuration.address.update',
                        'uses' => 'AddressController@update',
                        'permissions' => ['edit_address']
                    ]);

                    Route::post('{type}/delete/{id}', [
                        'as' => 'backend.configuration.address.delete',
                        'uses' => 'AddressController@delete',
                        'permissions' => ['delete_address']
                    ]);

                    Route::post('{type}/reorder', [
                        'as' => 'backend.configuration.address.reorder',
                        'uses' => 'AddressController@reorder',
                        'permissions' => ['edit_address']
                    ]);
                });

                //Taxes
                Route::group(['prefix' => 'tax', 'namespace' => 'Tax'], function(){
                    Route::get('index', [
                        'as' => 'backend.tax.index',
                        'uses' => 'TaxController@index',
                        'permissions' => ['view_tax']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.tax.create',
                        'uses' => 'TaxController@create',
                        'permissions' => ['create_tax']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.tax.store',
                        'uses' => 'TaxController@store',
                        'permissions' => ['create_tax']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.tax.edit',
                        'uses' => 'TaxController@edit',
                        'permissions' => ['edit_tax']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.tax.update',
                        'uses' => 'TaxController@update',
                        'permissions' => ['edit_tax']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.tax.delete',
                        'uses' => 'TaxController@delete',
                        'permissions' => ['delete_tax']
                    ]);

                    Route::post('reorder', [
                        'as' => 'backend.tax.reorder',
                        'uses' => 'TaxController@reorder',
                        'permissions' => ['edit_tax']
                    ]);

                    Route::get('get', [
                        'as' => 'backend.tax.get',
                        'uses' => 'TaxController@get',
                        'permissions' => ['create_order', 'edit_order']
                    ]);

                    Route::get('country_children/{country_id?}', [
                        'as' => 'backend.tax.country_children',
                        'uses' => 'TaxController@countryChildren',
                        'permissions' => ['create_tax', 'edit_tax']
                    ]);
                });
            });

            //Warehouse
            Route::group(['prefix' => 'warehouse', 'namespace' => 'Warehouse'], function(){
                Route::get('index', [
                    'as' => 'backend.warehouse.index',
                    'uses' => 'WarehouseController@index',
                    'permissions' => ['view_warehouse']
                ]);

                Route::get('create', [
                    'as' => 'backend.warehouse.create',
                    'uses' => 'WarehouseController@create',
                    'permissions' => ['create_warehouse']
                ]);

                Route::post('store', [
                    'as' => 'backend.warehouse.store',
                    'uses' => 'WarehouseController@store',
                    'permissions' => ['create_warehouse']
                ]);

                Route::get('edit/{id}', [
                    'as' => 'backend.warehouse.edit',
                    'uses' => 'WarehouseController@edit',
                    'permissions' => ['edit_warehouse']
                ]);

                Route::post('update/{id}', [
                    'as' => 'backend.warehouse.update',
                    'uses' => 'WarehouseController@update',
                    'permissions' => ['edit_warehouse']
                ]);

                Route::post('delete/{id}', [
                    'as' => 'backend.warehouse.delete',
                    'uses' => 'WarehouseController@delete',
                    'permissions' => ['delete_warehouse']
                ]);
            });

            //Customers
            Route::group(['prefix' => 'customer', 'namespace' => 'Customer'], function(){
                Route::any('index', [
                    'as' => 'backend.customer.index',
                    'uses' => 'CustomerController@index',
                    'permissions' => ['view_customer']
                ]);

                Route::get('autocomplete', [
                    'as' => 'backend.customer.autocomplete',
                    'uses' => 'CustomerController@autocomplete',
                ]);

                Route::get('create', [
                    'as' => 'backend.customer.create',
                    'uses' => 'CustomerController@create',
                    'permissions' => ['create_customer']
                ]);

                Route::post('store', [
                    'as' => 'backend.customer.store',
                    'uses' => 'CustomerController@store',
                    'permissions' => ['create_customer']
                ]);

                Route::get('edit/{id}', [
                    'as' => 'backend.customer.edit',
                    'uses' => 'CustomerController@edit',
                    'permissions' => ['edit_customer']
                ]);

                Route::post('update/{id}', [
                    'as' => 'backend.customer.update',
                    'uses' => 'CustomerController@update',
                    'permissions' => ['edit_customer']
                ]);

                Route::post('delete/{id}', [
                    'as' => 'backend.customer.delete',
                    'uses' => 'CustomerController@delete',
                    'permissions' => ['delete_customer']
                ]);
            });

            //Report
            Route::group(['prefix' => 'report', 'namespace' => 'Report'], function(){
                Route::get('sales/year', [
                    'as' => 'backend.report.sales_year',
                    'uses' => 'ReportController@salesYear',
                    'permissions' => ['view_sales_report']
                ]);

                Route::get('sales', [
                    'as' => 'backend.report.sales',
                    'uses' => 'ReportController@sales',
                    'permissions' => ['view_sales_report']
                ]);

                Route::get('delivery', [
                    'as' => 'backend.report.delivery',
                    'uses' => 'ReportController@delivery',
                    'permissions' => ['view_delivery_report']
                ]);
            });

            //Users
            Route::group(['prefix' => 'user', 'namespace' => 'User'], function(){
                Route::get('index', [
                    'as' => 'backend.user.index',
                    'uses' => 'UserController@index',
                    'permissions' => ['view_user']
                ]);

                Route::get('create', [
                    'as' => 'backend.user.create',
                    'uses' => 'UserController@create',
                    'permissions' => ['create_user']
                ]);

                Route::post('store', [
                    'as' => 'backend.user.store',
                    'uses' => 'UserController@store',
                    'permissions' => ['create_user']
                ]);

                Route::get('edit/{id}', [
                    'as' => 'backend.user.edit',
                    'uses' => 'UserController@edit',
                    'permissions' => ['edit_user']
                ]);

                Route::post('update/{id}', [
                    'as' => 'backend.user.update',
                    'uses' => 'UserController@update',
                    'permissions' => ['edit_user']
                ]);

                Route::post('delete/{id}', [
                    'as' => 'backend.user.delete',
                    'uses' => 'UserController@delete',
                    'permissions' => ['delete_user']
                ]);

                Route::group(['prefix' => 'role'], function(){
                    Route::get('index', [
                        'as' => 'backend.user.role.index',
                        'uses' => 'RoleController@index',
                        'permissions' => ['view_role']
                    ]);

                    Route::get('create', [
                        'as' => 'backend.user.role.create',
                        'uses' => 'RoleController@create',
                        'permissions' => ['create_role']
                    ]);

                    Route::post('store', [
                        'as' => 'backend.user.role.store',
                        'uses' => 'RoleController@store',
                        'permissions' => ['create_role']
                    ]);

                    Route::get('edit/{id}', [
                        'as' => 'backend.user.role.edit',
                        'uses' => 'RoleController@edit',
                        'permissions' => ['edit_role']
                    ]);

                    Route::post('update/{id}', [
                        'as' => 'backend.user.role.update',
                        'uses' => 'RoleController@update',
                        'permissions' => ['edit_role']
                    ]);

                    Route::post('delete/{id}', [
                        'as' => 'backend.user.role.delete',
                        'uses' => 'RoleController@delete',
                        'permissions' => ['delete_role']
                    ]);
                });
            });
        });
    });

    Route::get('images/{style}/{image}', 'ImageController@style')->where('image', '.*');

    Route::get('address/{type}/options/{parent?}', 'AddressController@options');
});