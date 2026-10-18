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
