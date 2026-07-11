export function CheckoutPaymentMethods() {
  if (!document.body.classList.contains("woocommerce-checkout")) {
    return;
  }

  function updatePaymentStyle() {
    document.querySelectorAll(".wc_payment_methods li").forEach((li) => {
      const input = li.querySelector('input[type="radio"]');
      const label = li.querySelector("label");
      if (!input || !label) return;

      label.classList.toggle("is-selected", input.checked);
    });
  }

  document.addEventListener("DOMContentLoaded", updatePaymentStyle);

  document.addEventListener("change", (e) => {
    if (e.target && e.target.name === "payment_method") {
      updatePaymentStyle();
    }
  });

  if (typeof jQuery !== "undefined") {
    jQuery(document.body).on(
      "updated_checkout payment_method_selected",
      updatePaymentStyle,
    );
  }

  // In case this module runs after DOMContentLoaded
  if (document.readyState !== "loading") {
    updatePaymentStyle();
  }
}
