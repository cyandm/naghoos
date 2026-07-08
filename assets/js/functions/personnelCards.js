export function PersonnelCards() {
  const cards = document.querySelectorAll(".personnel-card");
  if (!cards.length) return;

  const isTouchLike = () =>
    window.matchMedia("(hover: none), (pointer: coarse)").matches;

  cards.forEach((card) => {
    card.addEventListener("click", (e) => {
      if (!isTouchLike()) return;

      e.preventDefault();
      e.stopPropagation();

      const wasActive = card.classList.contains("is-active");

      cards.forEach((other) => other.classList.remove("is-active"));

      if (!wasActive) {
        card.classList.add("is-active");
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (!isTouchLike()) return;
    if (e.target.closest(".personnel-card")) return;

    cards.forEach((card) => card.classList.remove("is-active"));
  });
}
