/* Load project-owned HTML components through Live Server or static hosting. */
(() => {
  "use strict";

  const componentRoot = new URL("src/components/", document.baseURI);

  async function loadComponent(placeholder, ancestors = []) {
    // All component paths are relative to the page, including nested includes.
    const url = new URL(placeholder.dataset.include, document.baseURI);
    if (url.origin !== componentRoot.origin || !url.pathname.startsWith(componentRoot.pathname)) {
      throw new Error(`Component must be inside src/components/: ${url.href}`);
    }
    if (ancestors.includes(url.href)) {
      throw new Error(`Circular component include: ${url.href}`);
    }

    const response = await fetch(url, { cache: "no-cache" });
    if (!response.ok) {
      throw new Error(`Unable to load ${url.pathname}: HTTP ${response.status}`);
    }

    const template = document.createElement("template");
    template.innerHTML = await response.text();

    // Resolve children before inserting the parent so a partial form cannot submit.
    await Promise.all(
      [...template.content.querySelectorAll("[data-include]")].map(child =>
        loadComponent(child, [...ancestors, url.href])
      )
    );
    placeholder.replaceWith(template.content);
  }

  for (const placeholder of document.querySelectorAll("[data-include]")) {
    loadComponent(placeholder).catch(error => {
      console.error("Component loading failed:", error);
      const message = document.createElement("p");
      message.setAttribute("role", "alert");
      message.textContent = "This section could not be loaded. Please refresh the page to try again.";
      placeholder.replaceWith(message);
    });
  }
})();
