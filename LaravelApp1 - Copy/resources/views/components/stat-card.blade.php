{{-- Usage: <x-stat-card label="Products" :value="$count" icon="coffee" /> --}}
<div class="stat-card {{ $class ?? '' }}">
  <div>
    <div class="label">{{ $label }}</div>
    <div class="value">{{ $value }}</div>
  </div>
  <div class="stat-icon">@include('partials.icon', ['ico' => $icon ?? 'chart'])</div>
</div>
