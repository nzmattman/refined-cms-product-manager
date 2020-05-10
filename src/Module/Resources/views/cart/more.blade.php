@if (config('product-manager.orders.active'))
  @if (isset($product->variations) && sizeof($product->variations))
    <a href="{{ $link }}" class="button">Select Options</a>
  @else
    @include('productManager::cart.add-to-cart-quick')
  @endif
@else
  <a href="{{ $link }}" class="button">View More</a>
@endif
