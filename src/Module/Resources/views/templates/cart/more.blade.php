@if (config('products.orders.active'))
  @if (isset($product->variation_options) && sizeof($product->variation_options))
    <a href="{{ $link }}" class="button">Select Options</a>
  @else
    @include('products::templates.cart.add-to-cart-quick')
  @endif
@else
  <a href="{{ $link }}" class="button">View More</a>
@endif
