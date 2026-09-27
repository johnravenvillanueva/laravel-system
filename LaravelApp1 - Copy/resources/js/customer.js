/* =========================================================
   CUSTOMER JS — cart item qty stepper behavior
   (base.js handles the buttons; this submits the form on change)
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    // Auto-submit cart update form when the qty input changes
    document
        .querySelectorAll('form[data-cart-update] input[name="quantity"]')
        .forEach((input) => {
            input.addEventListener("change", () =>
                input.closest("form").submit(),
            );
        });
});
