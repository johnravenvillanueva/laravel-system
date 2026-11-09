{{--
  Ingredients / stock deduction editor.
  Expects: $inventoryItems (collection of InventoryItem)
  Optional: $product (when editing)
--}}
@php
  // Rows to show: old input (after validation error) > saved ingredients > one empty row
  $rows = old('ingredients');
  if ($rows === null) {
      $rows = isset($product)
          ? $product->ingredients->map(fn ($i) => [
                'inventory_id' => $i->inventory_id,
                'quantity'     => rtrim(rtrim(number_format((float) $i->quantity, 2, '.', ''), '0'), '.'),
            ])->all()
          : [];
  }
  if (empty($rows)) $rows = [['inventory_id' => '', 'quantity' => '']];
@endphp

<div class="form-group" id="ingredients-editor">
  <label class="form-label">Ingredients used (stock deducted per 1 sold)</label>
  <div class="text-sm text-muted" style="margin-bottom:8px">
    Every time this product is sold, these amounts are deducted from inventory
    (e.g. Flour — 0.05 kg per cookie). Leave empty if it should not deduct anything.
  </div>

  @error('ingredients') <div class="text-sm" style="color:#c0392b;margin-bottom:6px">{{ $message }}</div> @enderror
  @foreach($errors->get('ingredients.*') as $messages)
    @foreach($messages as $m)
      <div class="text-sm" style="color:#c0392b;margin-bottom:6px">{{ $m }}</div>
    @endforeach
  @endforeach

  <div id="ingredient-rows">
    @foreach($rows as $i => $row)
      <div class="ingredient-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
        <select name="ingredients[{{ $i }}][inventory_id]" class="form-control ingredient-select" style="flex:2">
          <option value="">— Select inventory item —</option>
          @foreach($inventoryItems as $inv)
            <option value="{{ $inv->inventory_id }}" data-unit="{{ $inv->unit }}"
              @selected((string)($row['inventory_id'] ?? '') === (string)$inv->inventory_id)>
              {{ $inv->name }} ({{ $inv->unit }})
            </option>
          @endforeach
        </select>
        <input type="number" step="0.01" min="0.01" name="ingredients[{{ $i }}][quantity]"
               class="form-control" style="flex:1" placeholder="Amount"
               value="{{ $row['quantity'] ?? '' }}">
        <span class="ingredient-unit text-sm text-muted" style="min-width:36px"></span>
        <button type="button" class="btn btn-danger btn-sm ingredient-remove" title="Remove">✕</button>
      </div>
    @endforeach
  </div>

  <button type="button" class="btn btn-outline btn-sm" id="add-ingredient">+ Add ingredient</button>
</div>

@push('scripts')
<script>
(function () {
  var wrap = document.getElementById('ingredient-rows');
  var addBtn = document.getElementById('add-ingredient');
  if (!wrap || !addBtn) return;

  function refreshUnit(row) {
    var sel = row.querySelector('.ingredient-select');
    var opt = sel.options[sel.selectedIndex];
    row.querySelector('.ingredient-unit').textContent = opt && opt.dataset.unit ? opt.dataset.unit : '';
  }

  function reindex() {
    wrap.querySelectorAll('.ingredient-row').forEach(function (row, i) {
      row.querySelector('.ingredient-select').name = 'ingredients[' + i + '][inventory_id]';
      row.querySelector('input[type=number]').name = 'ingredients[' + i + '][quantity]';
    });
  }

  wrap.addEventListener('change', function (e) {
    if (e.target.classList.contains('ingredient-select')) refreshUnit(e.target.closest('.ingredient-row'));
  });

  wrap.addEventListener('click', function (e) {
    if (!e.target.classList.contains('ingredient-remove')) return;
    var rows = wrap.querySelectorAll('.ingredient-row');
    var row = e.target.closest('.ingredient-row');
    if (rows.length === 1) {               // keep one blank row instead of none
      row.querySelector('.ingredient-select').value = '';
      row.querySelector('input[type=number]').value = '';
      refreshUnit(row);
    } else {
      row.remove();
      reindex();
    }
  });

  addBtn.addEventListener('click', function () {
    var clone = wrap.querySelector('.ingredient-row').cloneNode(true);
    clone.querySelector('.ingredient-select').value = '';
    clone.querySelector('input[type=number]').value = '';
    clone.querySelector('.ingredient-unit').textContent = '';
    wrap.appendChild(clone);
    reindex();
  });

  wrap.querySelectorAll('.ingredient-row').forEach(refreshUnit);
})();
</script>
@endpush
