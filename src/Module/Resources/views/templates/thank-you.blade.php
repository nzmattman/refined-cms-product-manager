@if (isset($page))
  @if (session()->has('content'))
    @php $cont = session()->get('content'); @endphp
    {!! str_replace($cont['search'], $cont['replace'], $page->getContentBySource('content')) !!}
  @else
    {!! $page->getContentBySource('content') !!}
  @endif
@endif
