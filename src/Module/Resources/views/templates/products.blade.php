@php
  $products = products()->getForFront(12);
@endphp

@if ($products->count())
  <div class="holder">
    <div class="products__listing">
      @foreach ($products as $product)
        <?php /*
        @php
          $image = '';
          if ($product->image) {
            $image = asset(image()->load($product->image)->width(420)->height(420)->string());
          }

          $link = request()->path().'/'.$product->meta->uri;
        @endphp
        <article class="product__item">
          <figure class="product__image image"{!! $image ? ' style="background-image: url('.$image.')"' : '' !!}>
            <a href="{{ $link }}">
              <img src="{{ asset('img/ui/square-holder.png') }}" class="image__placeholder" alt="{{ $product->name }}"/>
            </a>
          </figure>
          <header class="product__header">
            <h3>{{ $product->name }}</h3>
            @if ($product->code)
              <h4>{{ $product->code }}</h4>
            @endif
          </header>

          <div class="product__excerpt">
            {!! $product->excerpt !!}
          </div>

          <footer class="product__footer">
            @include('productManager::cart.more')
          </footer>
        </article>
        */ ?>
      @endforeach
    </div>
  </div>
@endif
