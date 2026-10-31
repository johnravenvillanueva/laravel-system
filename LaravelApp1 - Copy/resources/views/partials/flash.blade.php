{{-- Flash messages. base.js lifts these into the toast stack; without JS they
     simply render as banners. Errors never auto-dismiss. --}}
@foreach(['success' => 'check-circle', 'error' => 'alert', 'info' => 'info'] as $type => $icon)
  @if(session($type))
    <div class="flash flash-{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
      <span class="flash-icon">@include('partials.icon', ['ico' => $icon])</span>
      <div class="flash-body">{{ session($type) }}</div>
    </div>
  @endif
@endforeach
@if($errors->any())
  <div class="flash flash-error" role="alert">
    <span class="flash-icon">@include('partials.icon', ['ico' => 'alert'])</span>
    <div class="flash-body">
      <ul>
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  </div>
@endif
