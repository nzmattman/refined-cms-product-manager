<?php

Route::namespace('ProductManager\Module\Http\Controllers')
    ->group(function() {
        Route::post('refined/products/{product}/add-to-cart', [
            'as' => 'products.cart.add',
            'uses' => 'CartController@add'
        ]);
    })
;
