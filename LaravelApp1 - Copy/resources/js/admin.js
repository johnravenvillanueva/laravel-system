/* Admin behaviour: walk-in sale builder (only runs on that page) */
(function () {
    const grid = document.getElementById("productGrid");
    const wrap = document.getElementById("saleItems");
    const totalEl = document.getElementById("saleTotal");
    const itemsIn = document.getElementById("itemsInput");
    const searchIn = document.getElementById("productSearch");

    if (!grid || !wrap || !totalEl || !itemsIn) return;

    const form = itemsIn.closest("form");
    const saleItems = {};
    const peso = (n) =>
        "₱" +
        Number(n).toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    const esc = (s) =>
        String(s).replace(
            /[&<>"']/g,
            (c) =>
                ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c],
        );

    // Kept on window because the product cards call these from inline onclick.
    window.addToSale = function (id, name, price) {
        if (saleItems[id]) saleItems[id].qty++;
        else saleItems[id] = { id, name, price: Number(price), qty: 1 };
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
        const items = Object.values(saleItems);
        const total = items.reduce((sum, i) => sum + i.price * i.qty, 0);

        wrap.innerHTML = items.length
            ? items
                  .map(
                      (i) => `
        <div class="sale-line">
          <div>
            <div class="sale-line-name">${esc(i.name)}</div>
            <div class="sale-line-sub">${peso(i.price)} each · ${peso(i.price * i.qty)}</div>
          </div>
          <div class="qty">
            <button type="button" data-delta="-1" data-id="${i.id}" aria-label="Decrease quantity of ${esc(i.name)}">−</button>
            <input value="${i.qty}" readonly aria-label="Quantity of ${esc(i.name)}">
            <button type="button" data-delta="1" data-id="${i.id}" aria-label="Increase quantity of ${esc(i.name)}">+</button>
          </div>
          <button type="button" class="sale-remove" data-remove="${i.id}" aria-label="Remove ${esc(i.name)}">✕</button>
        </div>`,
                  )
                  .join("")
            : '<p class="empty-note">Tap a product to add it to this sale.</p>';

        totalEl.textContent = peso(total);
        itemsIn.value = JSON.stringify(items);
    }

    // One delegated listener instead of inline handlers in generated HTML
    wrap.addEventListener("click", (e) => {
        const dec = e.target.closest("[data-delta]");
        const rm = e.target.closest("[data-remove]");
        if (dec) changeQty(Number(dec.dataset.id), Number(dec.dataset.delta));
        if (rm) removeFromSale(Number(rm.dataset.remove));
    });

    // Product cards: keyboard access + a quick "added" cue
    grid.querySelectorAll(".product-card").forEach((card) => {
        card.setAttribute("tabindex", "0");
        card.setAttribute("role", "button");
        card.setAttribute("aria-label", `Add ${card.dataset.name || "product"} to sale`);
        card.addEventListener("click", () => {
            card.classList.remove("is-added");
            void card.offsetWidth;
            card.classList.add("is-added");
        });
        card.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                card.click();
            }
        });
    });

    // Don't let staff submit an empty sale
    form?.addEventListener("submit", (e) => {
        if (!Object.keys(saleItems).length) {
            e.preventDefault();
            window.IssangUI?.toast("Add at least one product before completing the sale.", "error");
        }
    });

    // Product search filter
    searchIn?.addEventListener("input", (e) => {
        const q = e.target.value.trim().toLowerCase();
        grid.querySelectorAll(".product-card").forEach((card) => {
            card.style.display = (card.dataset.name || "").includes(q) ? "" : "none";
        });
    });

    renderSale();
})();
