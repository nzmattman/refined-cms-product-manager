@php
  if (isset($page) && session()->has('content')) {
    $orderContent = session()->get('content');
    $page->content = array_map(function($content) use($orderContent) {
        if (isset($content->content, $content->content->content)) {
            $content->content->content = str_replace(
              $orderContent['search'],
              $orderContent['replace'],
              $content->content->content
            );
        }

        return $content;
    }, $page->content);
  }
@endphp
@if (isset($page, $page->content))
  @include('templates.includes.content')
@endif
