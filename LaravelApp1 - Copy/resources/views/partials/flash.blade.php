@if(session('success'))
  <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="flash flash-error">{{ session('error') }}</div>
@endif
@if(session('info'))
  <div class="flash flash-info">{{ session('info') }}</div>
@endif
@if($errors->any())
  <div class="flash flash-error">
    <ul style="padding-left:18px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif