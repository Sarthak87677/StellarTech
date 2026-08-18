@if (session('status'))
  <div class="alert alert-ok">{{ session('status') }}</div>
@endif
@if (session('warning'))
  <div class="alert alert-warn">{{ session('warning') }}</div>
@endif
@if (session('notice'))
  <div class="alert alert-warn">{{ session('notice') }}</div>
@endif
@if ($errors->any() && $errors->count() > 1)
  <div class="alert alert-err">Please check the highlighted fields below.</div>
@endif
