<div class="modal-backdrop" id="{{ $id }}">
  <div class="modal">
    <div class="modal-header">
      {{ $title }}
      <button data-modal-close style="font-size:1.2rem">✕</button>
    </div>
    <div class="modal-body">{{ $slot }}</div>
    @isset($footer)
      <div class="modal-footer">{{ $footer }}</div>
    @endisset
  </div>
</div>