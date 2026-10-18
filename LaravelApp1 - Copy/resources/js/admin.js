(function () {
    const grid = document.getElementById("productGrid");
    const wrap = document.getElementById("saleItems");
    const totalEl = document.getElementById("saleTotal");
    const itemsIn = document.getElementById("itemsInput");
    const searchIn = document.getElementById("productSearch");

    // Bail if we're not on the sale-create page
    if (!grid || !wrap || !totalEl || !itemsIn) return;

    const saleItems = {};

    window.addToSale = function (id, name, price) {
        if (saleItems[id]) saleItems[id].qty++;
        else saleItems[id] = { id, name, price, qty: 1 };
        renderSale();
    };

    window.removeFromSale = function (id) {
        delete saleItems[id];
        renderSale();
    };

    window.changeQty = function (id, delta) {
        if (!saleItems[id]) return;
        saleItems[id].qty += delta;
        if (saleItems[id].qty <= 0) delete saleItems[id];
        renderSale();
    };

    function renderSale() {
        let total = 0;
        let html = "";

        Object.values(saleItems).forEach((i) => {
            total += i.price * i.qty;
            html += `
        <div class="flex between mb-16 center">
          <div>
            <div class="bold">${i.name}</div>
            <div class="text-sm text-muted">₱${i.price.toFixed(2)}</div>
          </div>
          <div class="qty">
            <button type="button" onclick="changeQty(${i.id},-1)">−</button>
            <input value="${i.qty}" readonly>
            <button type="button" onclick="changeQty(${i.id},1)">+</button>
          </div>
          <button type="button" class="btn btn-outline btn-sm"
                  style="color:var(--danger)"
                  onclick="removeFromSale(${i.id})">✕</button>
        </div>`;
        });

        wrap.innerHTML =
            html ||
            '<p class="text-muted text-sm">Click a product to add it.</p>';
        totalEl.textContent = "₱" + total.toFixed(2);
        itemsIn.value = JSON.stringify(Object.values(saleItems));
    }

    // Product search filter
    searchIn?.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase();
        grid.querySelectorAll(".product-card").forEach((card) => {
            card.style.display = (card.dataset.name || "").includes(q)
                ? ""
                : "none";
        });
    });
})();
