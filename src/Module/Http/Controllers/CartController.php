<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\ProductManager\Module\Http\Repositories\CartRepository;
use RefinedDigital\ProductManager\Module\Models\Product;

class CartController
{

    protected $cartRepository;

    public function __construct()
    {
        $this->cartRepository = new CartRepository();
    }

    public function add(Request $request, Product $product)
    {
        $this->cartRepository->add($product, $request);

        if ($request->ajax()) {
            return response()->json(['success' => 'true']);
        } else {
            return redirect()->back()->with('success', 'true');
        }

    }

    public function remove(Request $request, Product $product)
    {
        $this->cartRepository->remove($product, $request);

        if ($request->ajax()) {
            return response()->json(['success' => 'true']);
        } else {
            return redirect()->back()->with('success', 'true');
        }

    }
}
