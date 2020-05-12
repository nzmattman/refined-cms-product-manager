<div class="product__add-to-cart">
  {!! html()->form('POST', route('refined.products.cart.add', $product->id))->open() !!}
    {!! html()->input('hidden', 'quantity')->value(1) !!}
    <button class="button">Add to Cart</button>
  {!! html()->form()->close() !!}
</div>
