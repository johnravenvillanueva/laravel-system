/* Storefront behaviour: cart quantity auto-update */
const ready = (fn) =>
    document.readyState === "loading"
        ? document.addEventListener("DOMContentLoaded", fn)
        : fn();

ready(() => {
    // Submit the cart update form shortly after the quantity stops changing,
    // so tapping "+" three times sends one request instead of three.
    document
        .querySelectorAll('form[data-cart-update] input[name="quantity"]')
        .forEach((input) => {
            let timer;
            input.addEventListener("change", () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const form = input.closest("form");
                    input.closest(".cart-item")?.classList.add("is-updating");
                    form.requestSubmit ? form.requestSubmit() : form.submit();
                }, 450);
            });
        });
});
