@if(request()->has('clear'))
  {{ cart()->clear() }}
@endif

<div id="product-manager">
  {!! view()->make('products::templates.cart.add-to-cart')->with('product', $page) !!}
  {{ help()->trace(cart()->get()) }}
</div>

@section('scripts')
  <script src="{{ asset('vendor/refined/product-manager/js/app.js') }}"></script>
@append
