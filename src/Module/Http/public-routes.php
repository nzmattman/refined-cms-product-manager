<?php

Route::namespace('ProductManager\Module\Http\Controllers')
    ->group(function() {
        Route::post('refined/products/{product}/cart/add', [
            'as' => 'products.cart.add',
            'uses' => 'CartController@add'
        ]);
        Route::put('refined/products/{product}/cart/update-quantity', [
            'as' => 'products.cart.update-quantity',
            'uses' => 'CartController@updateQuantity'
        ]);
        Route::put('refined/products/cart/{zone}/set-delivery', [
            'as' => 'products.cart.set-delivery',
            'uses' => 'CartController@setDelivery'
        ]);
        Route::delete('refined/products/{product}/cart/{key}', [
            'as' => 'products.cart.destroy',
            'uses' => 'CartController@remove'
        ]);
    })
;
