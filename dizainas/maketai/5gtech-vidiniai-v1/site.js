(() => {
  const section = document.body.dataset.section || "";
  const headerTarget = document.querySelector("[data-site-header]");
  const footerTarget = document.querySelector("[data-site-footer]");

  const navItems = [
    ["services", "Paslaugos", "paslaugos.html"],
    ["experience", "Patirtis", "patirtis.html"],
    ["about", "Apie mus", "../5gtech-komanda-v1/index.html"],
    ["career", "Karjera", "karjera.html"]
  ];

  if (headerTarget) {
    headerTarget.innerHTML = `
      <a class="skip-link" href="#content">Pereiti prie turinio</a>
      <header class="page-header">
        <div class="g5-container page-header__inner">
          <div class="page-brand-cluster">
            <a href="../5gtech-titulinis-v1/index.html" aria-label="5G TECH pagrindinis puslapis">
              <img class="page-logo" src="../client-ready-v4/assets/5gtech-logo-white.png" alt="5G TECH">
            </a>
            <nav class="language-switcher" aria-label="Pasirinkite kalbą">
              <details>
                <summary aria-label="Pasirinkite kalbą: LT"><span>LT</span><span class="language-switcher__chevron" aria-hidden="true">⌄</span></summary>
                <div class="language-switcher__menu">
                  <a href="${window.location.pathname}" lang="lt" hreflang="lt" aria-current="page">LT</a>
                  <a href="#" lang="en" hreflang="en" aria-disabled="true" title="Veikia WordPress versijoje">EN</a>
                  <a href="#" lang="de" hreflang="de" aria-disabled="true" title="Veikia WordPress versijoje">DE</a>
                </div>
              </details>
            </nav>
          </div>
          <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Meniu</button>
          <nav class="page-nav" id="site-nav" aria-label="Pagrindinė navigacija">
            ${navItems.map(([key, label, url]) => `<a href="${url}"${section === key ? ' aria-current="page"' : ""}>${label}</a>`).join("")}
            <a class="g5-button g5-button--primary" href="kontaktai.html"${section === "contact" ? ' aria-current="page"' : ""}>Kontaktai</a>
          </nav>
        </div>
      </header>`;

    const toggle = headerTarget.querySelector(".menu-toggle");
    const nav = headerTarget.querySelector(".page-nav");
    toggle.addEventListener("click", () => {
      const open = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!open));
      nav.classList.toggle("is-open", !open);
    });

    headerTarget.querySelectorAll('.language-switcher a[aria-disabled="true"]').forEach((link) => {
      link.addEventListener("click", (event) => event.preventDefault());
    });
  }

  document.addEventListener("click", (event) => {
    document.querySelectorAll(".language-switcher details[open]").forEach((details) => {
      if (!details.contains(event.target)) details.removeAttribute("open");
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;
    document.querySelectorAll(".language-switcher details[open]").forEach((details) => {
      details.removeAttribute("open");
      details.querySelector("summary")?.focus();
    });
  });

  if (footerTarget) {
    footerTarget.innerHTML = `
      <footer class="page-footer">
        <div class="g5-container footer-grid">
          <div class="footer-brand">
            <img class="page-logo" src="../client-ready-v4/assets/5gtech-logo-white.png" alt="5G TECH">
            <p>Telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektai.</p>
          </div>
          <div>
            <strong>Įmonė</strong>
            <a href="../5gtech-komanda-v1/index.html">Apie mus</a>
            <a href="patirtis.html">Patirtis</a>
            <a href="naujienos.html">Naujienos</a>
            <a href="kontaktai.html">Kontaktai</a>
          </div>
          <div>
            <strong>Karjera</strong>
            <a href="karjera.html">Darbo pozicijos</a>
            <a href="akademija.html">5GTECH Academy</a>
            <a href="mokymai.html">Mokymai</a>
            <a href="duk.html">DUK kandidatams</a>
          </div>
          <div>
            <strong>Informacija</strong>
            <a href="privatumo-politika.html">Privatumo politika</a>
            <a href="slapukai.html">Slapukų politika</a>
            <a href="https://5gtech.lt/wp-content/uploads/2025/04/5GTech_Kodeksas_Elgesys_Etika_2025_V1.1.pdf" target="_blank" rel="noopener">Elgesio kodeksas ↗</a>
          </div>
        </div>
        <div class="g5-container footer-bottom">
          <span>© 2026 UAB „5GTECH“</span>
          <span>LT · EN · DE</span>
        </div>
      </footer>`;
  }

  document.querySelectorAll(".prototype-form").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const status = form.querySelector("[data-form-status]");
      if (status) {
        status.hidden = false;
        status.focus();
      }
    });
  });
})();
