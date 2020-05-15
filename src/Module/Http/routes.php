<?php

Route::namespace('ProductManager\Module\Http\Controllers')
    ->group(function() {
        Route::resource('products', 'ProductController');
        Route::resource('product-variations', 'VariationController');
        Route::resource('product-statuses', 'ProductStatusController');
        Route::resource('delivery-zones', 'DeliveryController');
        Route::resource('order-notifications', 'OrderNotificationController');
        if (config('products.orders.active')) {
            Route::resource('orders', 'OrderController' );
        }
    })
;
