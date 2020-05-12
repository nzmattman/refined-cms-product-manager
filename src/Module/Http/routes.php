<?php

Route::namespace('ProductManager\Module\Http\Controllers')
    ->group(function() {
        Route::resource('products', 'ProductController');
        Route::resource('product-variations', 'VariationController');
        Route::resource('delivery-zones', 'DeliveryController');
        if (config('products.orders.active')) {
            Route::resource('orders', 'OrderController' );
        }
    })
;
