/**
 * Handles product review star rating functionality.
 * Manages interactive star rating for WooCommerce product reviews.
 *
 * @function
 * @name ProductReviews
 * @returns {void}
 */
export function ProductReviews() {
  document.addEventListener("DOMContentLoaded", function () {
    const starRating = document.querySelector(".star-rating");
    if (!starRating) {
      return;
    }

    const stars = starRating.querySelectorAll(".star");
    const ratingSelect = document.getElementById("rating");

    if (!ratingSelect || !stars.length) {
      return;
    }

    stars.forEach((star) => {
      star.addEventListener("click", function () {
        const value = parseInt(this.getAttribute("data-value"), 10);
        ratingSelect.value = value;
        updateStars(value);
      });

      star.addEventListener("mouseenter", function () {
        const value = parseInt(this.getAttribute("data-value"), 10);
        highlightStars(value);
      });
    });

    starRating.addEventListener("mouseleave", function () {
      const currentValue = parseInt(ratingSelect.value, 10) || 0;
      updateStars(currentValue);
    });

    function updateStars(value) {
      stars.forEach((star, index) => {
        star.classList.remove("is-highlighted");
        star.classList.toggle("is-filled", index < value);
      });
    }

    function highlightStars(value) {
      stars.forEach((star, index) => {
        star.classList.toggle("is-highlighted", index < value);
      });
    }
  });
}
