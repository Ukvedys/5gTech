(() => {
  const supportedLanguages = ["lt", "en", "de"];
  const params = new URLSearchParams(window.location.search);
  const requestedLanguage = params.get("lang") || "lt";
  const language = supportedLanguages.includes(requestedLanguage) ? requestedLanguage : "lt";
  const scriptUrl = document.currentScript ? document.currentScript.src : "";
  const dictionaryUrl = new URL("prototype-i18n.json", scriptUrl || window.location.href);

  function localizedUrl(href, nextLanguage) {
    if (!href || href.startsWith("#") || /^(mailto:|tel:|javascript:)/i.test(href)) return href;

    const url = new URL(href, window.location.href);
    if (url.origin !== window.location.origin) return href;

    if (nextLanguage === "lt") {
      url.searchParams.delete("lang");
    } else {
      url.searchParams.set("lang", nextLanguage);
    }

    return `${url.pathname}${url.search}${url.hash}`;
  }

  function configureLanguageSwitcher() {
    document.querySelectorAll(".language-switcher").forEach((switcher) => {
      const summary = switcher.querySelector("summary");
      const current = summary ? summary.querySelector("span") : null;
      if (current) current.textContent = language.toUpperCase();
      if (summary) summary.setAttribute("aria-label", `Pasirinkite kalbą: ${language.toUpperCase()}`);

      switcher.querySelectorAll("[data-language]").forEach((link) => {
        const nextLanguage = link.dataset.language;
        const url = new URL(window.location.href);
        if (nextLanguage === "lt") url.searchParams.delete("lang");
        else url.searchParams.set("lang", nextLanguage);
        link.href = `${url.pathname}${url.search}${url.hash}`;
        link.removeAttribute("aria-disabled");
        link.removeAttribute("title");
        if (nextLanguage === language) link.setAttribute("aria-current", "page");
        else link.removeAttribute("aria-current");
      });
    });
  }

  function preserveLanguageInLinks() {
    document.querySelectorAll("a[href]:not([data-language])").forEach((link) => {
      link.setAttribute("href", localizedUrl(link.getAttribute("href"), language));
    });
  }

  function translateTextNodes(dictionary) {
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
      acceptNode(node) {
        const parent = node.parentElement;
        if (!parent || parent.closest("script, style, noscript")) return NodeFilter.FILTER_REJECT;
        return node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
      }
    });
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);

    nodes.forEach((node) => {
      const value = node.nodeValue;
      const source = value.trim();
      if (!dictionary[source]) return;
      const leading = value.match(/^\s*/)?.[0] || "";
      const trailing = value.match(/\s*$/)?.[0] || "";
      node.nodeValue = `${leading}${dictionary[source]}${trailing}`;
    });
  }

  function translateAttributes(dictionary) {
    ["aria-label", "alt", "placeholder", "title"].forEach((attribute) => {
      document.querySelectorAll(`[${attribute}]`).forEach((element) => {
        const source = element.getAttribute(attribute);
        if (source && dictionary[source]) element.setAttribute(attribute, dictionary[source]);
      });
    });

    if (dictionary[document.title]) document.title = dictionary[document.title];
  }

  async function applyLanguage() {
    document.documentElement.lang = language;
    configureLanguageSwitcher();
    preserveLanguageInLinks();

    if (language === "lt") return;

    try {
      const response = await fetch(dictionaryUrl);
      if (!response.ok) throw new Error(`Translation catalogue returned ${response.status}`);
      const catalogues = await response.json();
      const dictionary = catalogues[language] || {};
      translateTextNodes(dictionary);
      translateAttributes(dictionary);
      configureLanguageSwitcher();
    } catch (error) {
      console.error("5G TECH prototype translation failed", error);
    }
  }

  applyLanguage();
})();
