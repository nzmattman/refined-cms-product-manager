@if (isset($page))
  @php
    if (session()->has('content')) {
        $cont = session()->get('content');
        echo str_replace($cont['search'], $cont['replace'], $page->getContentBySource('content'));
    } else {
        echo $page->getContentBySource('content');
    }
  @endphp
@endif
