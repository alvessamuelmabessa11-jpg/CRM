/**
 * Desktop sidebar collapse. theme.js stamps the stored state on <html> before
 * first paint; this module wires the toggle and gives collapsed icons tooltips.
 */
const STORAGE_KEY = "crm.sidebar.collapsed";
const COLLAPSED_CLASS = "sidebar-collapsed";
const DESKTOP = window.matchMedia("(min-width: 1200px)");

function store(collapsed) {
  try {
    window.localStorage.setItem(STORAGE_KEY, collapsed ? "1" : "0");
  } catch {
    // Private mode or blocked storage: the state lasts for this page only.
  }
}

function isCollapsed() {
  return document.documentElement.classList.contains(COLLAPSED_CLASS);
}

function syncTooltips(sidebar) {
  const enable = isCollapsed() && DESKTOP.matches && window.bootstrap?.Tooltip;
  sidebar
    .querySelectorAll(":scope .navbar-nav > .nav-item > .nav-row > .nav-link[data-sidebar-label]")
    .forEach((link) => {
      const existing = window.bootstrap?.Tooltip?.getInstance(link);
      if (enable && !existing) {
        new window.bootstrap.Tooltip(link, {
          title: link.dataset.sidebarLabel,
          placement: "right",
          trigger: "hover focus",
        });
      } else if (!enable && existing) {
        existing.dispose();
      }
    });
}

function syncToggle(toggle) {
  const collapsed = isCollapsed();
  toggle.setAttribute("aria-pressed", collapsed ? "true" : "false");
  toggle.setAttribute("aria-label", collapsed ? toggle.dataset.labelExpand : toggle.dataset.labelCollapse);
  toggle.title = toggle.getAttribute("aria-label");
}

function setCollapsed(collapsed, sidebar, toggle) {
  document.documentElement.classList.toggle(COLLAPSED_CLASS, collapsed);
  store(collapsed);
  syncToggle(toggle);
  syncTooltips(sidebar);
}

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const toggle = document.getElementById("sidebarCollapseToggle");
  if (!sidebar || !toggle) {
    return;
  }

  toggle.addEventListener("click", () => setCollapsed(!isCollapsed(), sidebar, toggle));

  // A folder opened from the icon rail expands the sidebar so its items are readable.
  sidebar.addEventListener("click", (event) => {
    const folder = event.target.closest('[data-bs-toggle="collapse"][data-sidebar-label]');
    if (folder && isCollapsed() && DESKTOP.matches) {
      setCollapsed(false, sidebar, toggle);
    }
  });

  DESKTOP.addEventListener("change", () => syncTooltips(sidebar));
  syncToggle(toggle);
  syncTooltips(sidebar);
});
