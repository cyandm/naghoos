export function HeaderLoginMenu() {
  const loginBtn = document.getElementById("login-btn");
  const navLogged = document.getElementById("navlogged");
  if (!loginBtn || !navLogged) return;

  const toggleLink = loginBtn.querySelector("a");
  if (!toggleLink) return;

  const isTouchLike = () =>
    window.matchMedia("(hover: none), (pointer: coarse)").matches;

  const setOpen = (open) => {
    loginBtn.classList.toggle("is-open", open);
    toggleLink.setAttribute("aria-expanded", open ? "true" : "false");
  };

  toggleLink.addEventListener("click", (e) => {
    if (!isTouchLike()) return;

    e.preventDefault();
    e.stopPropagation();

    const isOpen = loginBtn.classList.contains("is-open");
    setOpen(!isOpen);
  });

  document.addEventListener("click", (e) => {
    if (!isTouchLike()) return;
    if (e.target.closest("#login-btn")) return;

    setOpen(false);
  });
}
