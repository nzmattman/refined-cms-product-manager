@include('products::templates.includes.message')

@if(cart()->get()->items->count())
  @if ($page->form_id)
    @php
      $cart = cart()->get();
      $defaultFields = [];

      if($cart->delivery && $cart->delivery->postcode) {
          $defaultFields['cart__field--postcode'] = $cart->delivery->postcode;
      }

      if (auth()->check()) {
          $user = auth()->user();
          $defaultFields['cart__field--first-name'] = $user->first_name;
          $defaultFields['cart__field--last-name'] = $user->last_name;
          $defaultFields['cart__field--company-name'] = $user->company;
          $defaultFields['cart__field--address'] = $user->address;
          $defaultFields['cart__field--address-2'] = $user->address_2;
          $defaultFields['cart__field--suburb'] = $user->city;
          $defaultFields['cart__field--phone'] = $user->phone;
          $defaultFields['cart__field--email'] = $user->email;
          if (!isset($defaultFields['cart__field--postcode'])) {
            $defaultFields['cart__field--postcode'] = $user->postcode;
          }
      }
    @endphp
    {!!
      forms()
        ->form($page->form_id)
        ->setHasPayments(true)
        ->setTemplateNamespace('products')
        ->setTemplate('forms.front-end.form')
        ->setDefaultFields($defaultFields)
        ->setButtonText('Place Order')
        ->render()
    !!}
  @else
    NO FORM SELECTED
  @endif
@else
  Your cart is currently empty.
@endif

@section('scripts')
  <script src="{{ asset('vendor/refined/product-manager/js/app.js') }}"></script>
@append

@section('styles')
  <link href="{{ asset('vendor/refined/product-manager/css/cart.css?v='.uniqid()) }}" rel="stylesheet">
@append
