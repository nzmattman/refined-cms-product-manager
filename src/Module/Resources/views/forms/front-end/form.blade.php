<div class="form">
  @include('formBuilder::front-end.includes.opener')
  <div class="cart__checkout">
    <div class="cart__form">
      @include('formBuilder::front-end.includes.errors')
      @include('formBuilder::front-end.includes.fields')
    </div>
    <div class="cart__mini-cart">
      <mini-cart
        :cart="{{ json_encode(cart()->get()) }}"
        :config="{{ json_encode(config('products')) }}"
        :shipping="{{ json_encode(products()->getDeliveryZones()) }}"
        path="{{ request()->path() }}"
      ></mini-cart>

      @include('formBuilder::front-end.includes.captcha')
      @include('formBuilder::front-end.includes.submit')
    </div>
  </div><!-- / form -->
  @include('formBuilder::front-end.includes.closer')
</div>


@php
  if (!session()->has('loaded_forms')) {
    session()->put('loaded_forms', []);
  }
@endphp

@if (!in_array($form->id, session()->get('loaded_forms')))
  @php
    session()->push('loaded_forms', $form->id);
  @endphp

  @section('scripts')
    @include('formBuilder::front-end.includes.scripts')
  @append
@endif
