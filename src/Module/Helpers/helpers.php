<?php

use \RefinedDigital\ProductManager\Module\Http\Repositories\ProductRepository;
use \RefinedDigital\ProductManager\Module\Http\Repositories\CartRepository;
use \RefinedDigital\ProductManager\Module\Http\Repositories\VariationRepository;

if (!function_exists('products')) {
    function products()
    {
        return app(ProductRepository::class);
    }
}

if (!function_exists('cart')) {
    function cart()
    {
        return app(CartRepository::class);
    }
}

if (!function_exists('productVariations')) {
    function productVariations()
    {
        return app(VariationRepository::class);
    }
}
