<div id="product-manager">
  @if(cart()->get()->items->count())
    @if ($page->form_id)
      @php
        $cart = cart()->get();
        $defaultFields = [];
        // todo: update this so the field ids are correct
        if($cart->delivery && $cart->delivery->postcode) {
        $defaultFields['field12'] = $cart->delivery->postcode;
        }
      @endphp
      {!!
        forms()
          ->form($page->form_id)
          ->setDefaultFields($defaultFields)
          ->render()
      !!}
    @else
      NO FORM SELECTED
    @endif
  @else
    Your cart is currently empty.
  @endif
</div>

@section('scripts')
  <script src="{{ asset('vendor/refined/product-manager/js/app.js') }}"></script>
@append

@section('styles')
  <link href="{{ asset('vendor/refined/product-manager/css/cart.css?v='.uniqid()) }}" rel="stylesheet">
@append
