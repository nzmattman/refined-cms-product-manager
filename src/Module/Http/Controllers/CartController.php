<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\ProductManager\Module\Http\Repositories\CartRepository;
use RefinedDigital\ProductManager\Module\Models\DeliveryZone;
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
        $message = 'Product has been added to your cart';

        if ($request->ajax()) {
            return response()
                ->json(['success' => 'true', 'message', $message]);
        } else {
            return redirect()
                ->back()
                ->with('success', 'true')
                ->with(compact('message'));
        }

    }

    public function remove(Request $request, Product $product, $itemKey)
    {
        $this->cartRepository->remove($itemKey);

        $message = 'Product has been removed to your cart';

        if ($request->ajax()) {
            return response()
                ->json(['success' => 'true', 'message', $message]);
        } else {
            return redirect()
                ->back()
                ->with('success', 'true')
                ->with(compact('message'));
        }

    }

    public function updateQuantity(Request $request)
    {
        $this->cartRepository->updateQuantity($request);

        if ($request->ajax()) {
            return response()->json(['success' => 'true']);
        } else {
            return redirect()->back()->with('success', 'true');
        }
    }

    public function setDelivery(DeliveryZone $zone, Request $request)
    {
        $this->cartRepository->setDeliveryZone($zone, $request->get('postcode'));

        if ($request->ajax()) {
            return response()->json(['success' => 'true']);
        } else {
            return redirect()->back()->with('success', 'true');
        }
    }
}
