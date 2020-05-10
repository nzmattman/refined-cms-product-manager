
  {!! view()->make('productManger::cart.add-to-cart')->with('product', $page) !!}
  {{ help()->trace(cart()->get()) }}
  {{ help()->trace($page->toArray()) }}
