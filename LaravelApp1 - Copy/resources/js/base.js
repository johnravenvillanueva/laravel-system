/* ==========================================================================
   Shared UI behaviour (loaded on every page)
   Exposes window.IssangUI = { toast, confirm } for page scripts.
   ========================================================================== */

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
const ready = (fn) =>
    document.readyState === "loading"
        ? document.addEventListener("DOMContentLoaded", fn)
        : fn();

const ICONS = {
    "check-circle":
        '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
    alert:
        '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    x: '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
    eye: '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
    "eye-off":
        '<path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/>',
};
const svg = (name, size = 18) =>
    `<svg class="icon" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[name]}</svg>`;

/* ---------- Toasts ------------------------------------------------------- */
let toastHost;
function getToastHost() {
    if (!toastHost) {
        toastHost = document.createElement("div");
        toastHost.className = "toasts";
        toastHost.setAttribute("aria-live", "polite");
        document.body.appendChild(toastHost);
    }
    return toastHost;
}

function dismissToast(el) {
    if (!el.isConnected || el.classList.contains("is-leaving")) return;
    el.classList.add("is-leaving");
    el.addEventListener("animationend", () => el.remove(), { once: true });
    setTimeout(() => el.remove(), 400);
}

function adoptFlash(el) {
    const host = getToastHost();
    const isError = el.classList.contains("flash-error");

    if (!el.querySelector(".flash-close")) {
        const close = document.createElement("button");
        close.type = "button";
        close.className = "flash-close";
        close.setAttribute("aria-label", "Dismiss message");
        close.innerHTML = svg("x", 16);
        el.appendChild(close);
    }
    el.querySelector(".flash-close").addEventListener("click", () =>
        dismissToast(el),
    );

    host.appendChild(el);

    // Errors (e.g. validation lists) stay until read; everything else clears itself.
    if (!isError) {
        const ms = 5000;
        el.style.setProperty("--t", ms + "ms");
        el.setAttribute("data-autodismiss", "");
        let timer = setTimeout(() => dismissToast(el), ms);
        el.addEventListener("mouseenter", () => {
            clearTimeout(timer);
            el.style.setProperty("animation-play-state", "paused");
        });
        el.addEventListener("mouseleave", () => {
            timer = setTimeout(() => dismissToast(el), 1500);
        });
    }
}

function toast(message, type = "info") {
    const iconName =
        type === "success" ? "check-circle" : type === "error" ? "alert" : "info";
    const el = document.createElement("div");
    el.className = `flash flash-${type}`;
    el.setAttribute("role", type === "error" ? "alert" : "status");
    el.innerHTML = `<span class="flash-icon">${svg(iconName)}</span><div class="flash-body"></div>`;
    el.querySelector(".flash-body").textContent = message;
    adoptFlash(el);
    return el;
}

/* ---------- Confirm dialog (replaces window.confirm) --------------------- */
function confirmDialog({
    title = "Are you sure?",
    message = "",
    confirmText = "Confirm",
    cancelText = "Cancel",
    danger = false,
} = {}) {
    return new Promise((resolve) => {
        const previouslyFocused = document.activeElement;
        const backdrop = document.createElement("div");
        backdrop.className = "modal-backdrop open";
        backdrop.innerHTML = `
          <div class="modal" role="alertdialog" aria-modal="true" aria-labelledby="cd-title" aria-describedby="cd-msg" style="max-width:420px">
            <div class="modal-header"><span id="cd-title"></span></div>
            <div class="modal-body"><p id="cd-msg"></p></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline" data-cd-cancel></button>
              <button type="button" class="btn ${danger ? "btn-danger" : "btn-primary"}" data-cd-ok></button>
            </div>
          </div>`;
        $("#cd-title", backdrop).textContent = title;
        $("#cd-msg", backdrop).textContent = message;
        $("[data-cd-cancel]", backdrop).textContent = cancelText;
        $("[data-cd-ok]", backdrop).textContent = confirmText;
        document.body.appendChild(backdrop);
        const ok = $("[data-cd-ok]", backdrop);
        ok.focus();

        const close = (result) => {
            document.removeEventListener("keydown", onKey, true);
            backdrop.remove();
            previouslyFocused?.focus?.();
            resolve(result);
        };
        const onKey = (e) => {
            if (e.key === "Escape") {
                e.preventDefault();
                close(false);
            }
            if (e.key === "Tab") {
                const f = $$("button", backdrop);
                const first = f[0];
                const last = f[f.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        };
        document.addEventListener("keydown", onKey, true);
        $("[data-cd-cancel]", backdrop).addEventListener("click", () => close(false));
        ok.addEventListener("click", () => close(true));
        backdrop.addEventListener("mousedown", (e) => {
            if (e.target === backdrop) close(false);
        });
    });
}

window.IssangUI = { toast, confirm: confirmDialog };

ready(() => {
    /* ---------- Flash messages become toasts ----------------------------- */
    $$(".flash").forEach(adoptFlash);

    /* ---------- Sticky header shadow on scroll ---------------------------- */
    const navbar = $(".navbar");
    if (navbar) {
        const onScroll = () =>
            navbar.classList.toggle("is-scrolled", window.scrollY > 8);
        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });
    }

    /* ---------- Public mobile nav ---------------------------------------- */
    const navToggle = $("[data-toggle-nav]");
    const navLinks = $(".nav-links");
    if (navToggle && navLinks) {
        navToggle.setAttribute("aria-expanded", "false");
        navToggle.setAttribute("aria-controls", "primary-nav");
        navLinks.id = navLinks.id || "primary-nav";
        const setNav = (open) => {
            navLinks.classList.toggle("open", open);
            navToggle.setAttribute("aria-expanded", String(open));
        };
        navToggle.addEventListener("click", () =>
            setNav(!navLinks.classList.contains("open")),
        );
        document.addEventListener("click", (e) => {
            if (!navLinks.contains(e.target) && !navToggle.contains(e.target))
                setNav(false);
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") setNav(false);
        });
        window.matchMedia("(min-width: 901px)").addEventListener("change", () =>
            setNav(false),
        );
    }

    /* ---------- Admin sidebar (drawer on mobile) ------------------------- */
    const sidebarToggle = $("[data-toggle-sidebar]");
    const sidebar = $(".sidebar");
    if (sidebarToggle && sidebar) {
        const backdrop = document.createElement("div");
        backdrop.className = "sidebar-backdrop";
        document.body.appendChild(backdrop);
        const setSidebar = (open) => {
            sidebar.classList.toggle("open", open);
            backdrop.classList.toggle("show", open);
            sidebarToggle.setAttribute("aria-expanded", String(open));
            document.body.style.overflow = open ? "hidden" : "";
        };
        sidebarToggle.setAttribute("aria-expanded", "false");
        sidebarToggle.addEventListener("click", () =>
            setSidebar(!sidebar.classList.contains("open")),
        );
        backdrop.addEventListener("click", () => setSidebar(false));
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") setSidebar(false);
        });
        window.matchMedia("(min-width: 901px)").addEventListener("change", () =>
            setSidebar(false),
        );
    }
    // Keep the active admin link visible in a long sidebar
    $(".sidebar-nav a.active")?.scrollIntoView({ block: "nearest" });

    /* ---------- Quantity steppers ---------------------------------------- */
    $$(".qty").forEach((qty) => {
        const input = $("input", qty);
        if (!input) return;
        const min = parseInt(input.min || "1", 10);
        const emit = () =>
            input.dispatchEvent(new Event("change", { bubbles: true }));
        $("[data-dec]", qty)?.addEventListener("click", () => {
            input.value = Math.max(min, (parseInt(input.value, 10) || min) - 1);
            emit();
        });
        $("[data-inc]", qty)?.addEventListener("click", () => {
            input.value = (parseInt(input.value, 10) || min) + 1;
            emit();
        });
        input.addEventListener("blur", () => {
            if (!(parseInt(input.value, 10) >= min)) input.value = min;
        });
    });

    /* ---------- Modals ---------------------------------------------------- */
    let lastTrigger = null;
    const openModal = (modal, trigger) => {
        lastTrigger = trigger || document.activeElement;
        modal.classList.add("open");
        const box = $(".modal", modal);
        box?.setAttribute("role", "dialog");
        box?.setAttribute("aria-modal", "true");
        ($("input, select, textarea, button:not([data-modal-close])", modal) || box)?.focus?.();
    };
    const closeModal = (modal) => {
        modal.classList.remove("open");
        lastTrigger?.focus?.();
    };
    $$("[data-modal-open]").forEach((btn) =>
        btn.addEventListener("click", () => {
            const m = document.getElementById(btn.getAttribute("data-modal-open"));
            if (m) openModal(m, btn);
        }),
    );
    $$("[data-modal-close]").forEach((btn) => {
        btn.setAttribute("aria-label", btn.getAttribute("aria-label") || "Close");
        btn.addEventListener("click", () => {
            const m = btn.closest(".modal-backdrop");
            if (m) closeModal(m);
        });
    });
    $$(".modal-backdrop").forEach((bd) =>
        bd.addEventListener("mousedown", (e) => {
            if (e.target === bd) closeModal(bd);
        }),
    );
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            const open = $(".modal-backdrop.open:not([data-confirm-dialog])");
            if (open && !open.querySelector("[data-cd-ok]")) closeModal(open);
        }
    });

    /* ---------- Confirmations -------------------------------------------- */
    // <form data-confirm="Delete this product?"> and any .logout-form
    const guarded = $$("form[data-confirm], form.logout-form");
    guarded.forEach((form) => {
        form.addEventListener("submit", async (e) => {
            if (form.dataset.confirmed === "1") return;
            e.preventDefault();
            const isLogout = form.classList.contains("logout-form");
            const ok = await confirmDialog({
                title: isLogout ? "Log out?" : "Please confirm",
                message: isLogout
                    ? "You'll need to sign in again to place or manage orders."
                    : form.getAttribute("data-confirm"),
                confirmText: isLogout ? "Log out" : "Yes, continue",
                danger: !isLogout,
            });
            if (ok) {
                form.dataset.confirmed = "1";
                form.requestSubmit();
            }
        });
    });

    /* ---------- Loading state on submit (stops double orders) ------------ */
    $$("form").forEach((form) => {
        form.addEventListener("submit", (e) => {
            if (e.defaultPrevented || form.hasAttribute("data-no-loading")) return;
            const btn = e.submitter || $('button:not([type]), button[type="submit"]', form);
            if (!btn || btn.classList.contains("is-loading")) return;
            // Wait a tick so the browser still serialises the button's value.
            setTimeout(() => {
                btn.classList.add("is-loading");
                btn.setAttribute("aria-busy", "true");
            }, 0);
        });
    });
    window.addEventListener("pageshow", (e) => {
        if (e.persisted)
            $$(".is-loading").forEach((b) => {
                b.classList.remove("is-loading");
                b.removeAttribute("aria-busy");
            });
    });

    /* ---------- Password show / hide ------------------------------------- */
    $$('input[type="password"]').forEach((input) => {
        const wrap = document.createElement("div");
        wrap.className = "input-affix";
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "affix-btn";
        btn.setAttribute("aria-label", "Show password");
        btn.innerHTML = svg("eye");
        btn.addEventListener("click", () => {
            const show = input.type === "password";
            input.type = show ? "text" : "password";
            btn.innerHTML = svg(show ? "eye-off" : "eye");
            btn.setAttribute("aria-label", show ? "Hide password" : "Show password");
        });
        wrap.appendChild(btn);
    });
});
