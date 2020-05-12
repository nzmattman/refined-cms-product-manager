<div class="product__add-to-cart">
  {!! html()->form('POST', route('refined.products.cart.add', $product->id))->open() !!}
    <product-variations :variations="{{ json_encode($product->variation_options) }}"></product-variations>
    <div class="add-to-cart__row">
      <label for="quantity" class="add-to-cart__label">Quantity</label>
      {!! html()->input('number', 'quantity')->attribute('class', 'add-to-cart__control')->value(1) !!}
    </div>
    <button class="button">Add to Cart</button>
  {!! html()->form()->close() !!}
</div>
