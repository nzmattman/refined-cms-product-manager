@if (config('products.orders.active') && $product->for_sale)
  @if (config('products.variations.active') && isset($product->variation_options) && sizeof($product->variation_options))
    <a href="{{ $link }}" class="button">Select Options</a>
  @else
    @include('products::templates.cart.add-to-cart-quick')
  @endif
@else
  <a href="{{ $link }}" class="button">View More</a>
@endif
