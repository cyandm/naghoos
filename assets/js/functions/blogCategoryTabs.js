const updateBlogTabUrl = (name) => {
  const url = new URL(window.location.href);

  url.searchParams.set("blog_tab", name);
  url.searchParams.delete("blog_paged");
  window.history.replaceState(null, "", url);
};

export function BlogCategoryTabs() {
  const tabs = document.querySelectorAll("[id^='tab-category-']");
  const contentsWrapper = document.querySelector(".blog-tab-contents");

  if (!tabs.length || !contentsWrapper) return;

  const panels = [...contentsWrapper.querySelectorAll("[id^='tab-content-']")];
  let isAnimating = false;

  const getContent = (name) => document.getElementById(`tab-content-${name}`);

  const setContainerHeight = (panel) => {
    if (!panel) return;
    contentsWrapper.style.minHeight = `${panel.scrollHeight}px`;
  };

  const updateTabButtons = (name) => {
    tabs.forEach((tab) => {
      const tabName = tab.id.replace("tab-category-", "");
      const isActive = tabName === name;

      tab.classList.toggle("bg-cynRed", isActive);
      tab.classList.toggle("text-white", isActive);
      tab.classList.toggle("text-cynBlack", !isActive);
      tab.classList.toggle("bg-cynBlack/10", !isActive);
      tab.setAttribute("aria-selected", isActive ? "true" : "false");
    });
  };

  const switchTab = (name) => {
    const current = contentsWrapper.querySelector(".blog-tab-content.is-active");
    const next = getContent(name);

    if (!next || current === next || isAnimating) return;

    isAnimating = true;
    updateTabButtons(name);

    const finishSwitch = () => {
      panels.forEach((panel) => {
        panel.classList.remove("is-leaving", "is-entering");

        if (panel !== next) {
          panel.classList.remove("is-active");
          panel.setAttribute("aria-hidden", "true");
        }
      });

      next.classList.add("is-active");
      next.setAttribute("aria-hidden", "false");
      setContainerHeight(next);
      updateBlogTabUrl(name);
      isAnimating = false;
    };

    if (!current) {
      finishSwitch();
      return;
    }

    current.classList.add("is-leaving");
    current.classList.remove("is-active");
    next.classList.add("is-entering");
    next.setAttribute("aria-hidden", "false");
    setContainerHeight(next);

    requestAnimationFrame(() => {
      next.classList.add("is-active");
    });

    window.setTimeout(() => {
      current.classList.remove("is-leaving");
      next.classList.remove("is-entering");
      finishSwitch();
    }, 380);
  };

  const activePanel = contentsWrapper.querySelector(".blog-tab-content.is-active");
  setContainerHeight(activePanel);

  window.addEventListener("resize", () => {
    const panel = contentsWrapper.querySelector(".blog-tab-content.is-active");
    setContainerHeight(panel);
  });

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const name = tab.id.replace("tab-category-", "");

      if (tab.getAttribute("aria-selected") === "true") return;

      switchTab(name);
    });
  });
}
