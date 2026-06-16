export function BlogArchiveSort() {
  document.querySelectorAll("[data-blog-archive-sort]").forEach((select) => {
    if (!(select instanceof HTMLSelectElement)) {
      return;
    }

    select.addEventListener("change", () => {
      if (select.value) {
        window.location.href = select.value;
      }
    });
  });
}
