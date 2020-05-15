@include('products::templates.includes.message')

<div id="product-manager">
  <cart
    :cart="{{ json_encode(cart()->get()) }}"
    :config="{{ json_encode(config('products')) }}"
    path="{{ request()->path() }}"

  ></cart>
</div>

@section('scripts')
  <script src="{{ asset('vendor/refined/product-manager/js/app.js') }}"></script>
@append

@section('styles')
  <link href="{{ asset('vendor/refined/product-manager/css/cart.css?v='.uniqid()) }}" rel="stylesheet">
@append
