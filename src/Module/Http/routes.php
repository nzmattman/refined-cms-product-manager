<?php

Route::namespace('ProductManager\Module\Http\Controllers')
    ->group(function() {
        Route::resource('products', 'ProductController');
        Route::resource('product-variations', 'VariationController');
        if (config('product-manager.orders.active')) {
            Route::resource('orders', 'OrderController' );
        }
    })
;
