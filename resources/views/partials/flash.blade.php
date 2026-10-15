@if (session('ok'))<div class="flash flashm">{{ session('ok') }}</div>@endif
@if (session('error'))<div class="flash bad flashm">{{ session('error') }}</div>@endif
@if (isset($errors) && $errors->any())<div class="flash bad flashm">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

