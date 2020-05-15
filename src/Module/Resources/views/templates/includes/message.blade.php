@if (session()->has('message'))
  <div class="alert">{{ session()->get('message') }}</div>
@endif
