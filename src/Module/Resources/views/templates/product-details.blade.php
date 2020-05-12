<div id="product-manager">
  @php
    // cart()->clear();
  @endphp
  <h1 style="color:#f00">Cart add / update / remove not working correctly</h1>
  <p style="color:#f00">Need to check the keys are different</p>
  {!! view()->make('products::templates.cart.add-to-cart')->with('product', $page) !!}
  {{ help()->trace(cart()->get()) }}
</div>

@section('scripts')
  <script src="{{ asset('vendor/refined/product-manager/js/app.js') }}"></script>
@append
