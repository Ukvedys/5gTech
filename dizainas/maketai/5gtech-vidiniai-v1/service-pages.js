(() => {
  const services = {
    mobile: {
      category: "Telekomunikacijos",
      title: "Mobiliojo ryšio tinklai",
      summary: "Įrengiame, modernizuojame ir prižiūrime mobiliojo ryšio infrastruktūrą pagal operatorių techninius bei darbų saugos standartus.",
      image: "../5gtech-komanda-v1/team-work-01.jpg",
      work: [
        "2G, 3G, 4G-LTE ir 5G ryšio įrangos montavimas bei modernizavimas",
        "Antenų, radijo modulių ir metalinių konstrukcijų montavimas",
        "Bangolaidžių, fiderių ir kabelių instaliavimas",
        "„SiteMaster“ matavimai ir bangolaidžių kokybės testavimas",
        "Radiorelinių linijų įrengimas, konfigūravimas ir testavimas",
        "Duomenų perdavimo sistemų diegimas",
        "Objekto ir įrangos techninis įvertinimas („Site Survey“)",
        "Dokumentacijos parengimas ir perdavimas užsakovui"
      ],
      equipment: ["Ericsson", "Nokia", "Huawei", "ZTE", "Eltek", "Delta"]
    },
    indoor: {
      category: "Telekomunikacijos",
      title: "Vidinio ryšio tinklai",
      summary: "Projektuojame ir įrengiame patikimus mobiliojo ir belaidžio ryšio sprendimus dideliuose bei techniškai sudėtinguose pastatuose.",
      image: "../5gtech-komanda-v1/team-work-02.jpg",
      work: [
        "Vidaus mobiliojo ryšio tinklų projektavimas",
        "Vidinių antenų ir signalo stiprinimo sistemų montavimas",
        "Wi-Fi infrastruktūros diegimas pastatuose",
        "Ryšio sprendimai prekybos centruose, ligoninėse ir viešbučiuose",
        "Sprendimai tuneliuose, požeminėse aikštelėse ir kituose objektuose",
        "Sistemos matavimai, optimizavimas ir testavimas",
        "Techninės dokumentacijos rengimas",
        "Įrengto sprendimo perdavimas užsakovui"
      ],
      equipment: ["Ericsson", "Nokia", "Huawei", "CommScope", "ZTE", "SiteMaster"]
    },
    fixed: {
      category: "Telekomunikacijos",
      title: "Fiksuoto ryšio tinklai",
      summary: "Įrengiame šviesolaidinio ir varinio ryšio infrastruktūrą nuo magistralinių linijų iki galutinio vartotojo prijungimo.",
      image: "../5gtech-komanda-v1/team-work-01.jpg",
      work: [
        "Magistralinių ir skirstomųjų ryšio linijų projektavimas",
        "Šviesolaidinių kabelių montavimas",
        "Varinių kabelių montavimas ir priežiūra",
        "Optinių kabelių virinimas ir movų montavimas",
        "Ryšio linijų testavimas ir gedimų diagnostika",
        "Vartotojų paslaugų diegimas ir prijungimas",
        "ODF spintų įrengimas centralėse",
        "Dokumentacijos parengimas ir tinklo perdavimas užsakovui"
      ],
      equipment: ["Optiniai tinklai", "ODF", "OTDR", "Šviesolaidis", "Variniai kabeliai", "Testavimo įranga"]
    },
    electrical: {
      category: "Energetika",
      title: "Elektros darbai",
      summary: "Projektuojame, įrengiame ir prižiūrime elektros sistemas naujuose bei rekonstruojamuose objektuose.",
      image: "../5gtech-komanda-v1/team-work-02.jpg",
      work: [
        "Vidaus elektros instaliacijos montavimas",
        "Vidaus ir lauko apšvietimo sistemų įrengimas",
        "Elektros tinklų ir įvadų projektavimas",
        "Elektros įrangos montavimas ir prijungimas",
        "Elektrinio šildymo sistemų įrengimas",
        "Profilaktinė elektros sistemų priežiūra",
        "Varžų matavimai ir bandymai",
        "Dokumentacijos parengimas tinklams perduoti ESO"
      ],
      equipment: ["Sonel", "Eltek", "Delta", "FIAMM", "Enersys", "Matavimo įranga"]
    },
    security: {
      category: "Inžinerinės sistemos",
      title: "Apsaugos ir stebėjimo sistemos",
      summary: "Projektuojame, montuojame ir integruojame vaizdo stebėjimo, signalizacijos bei patekimo kontrolės sprendimus.",
      image: "../5gtech-komanda-v1/team-work-01.jpg",
      work: [
        "IP, HD-SDI ir analoginių kamerų sistemų diegimas",
        "Apsaugos ir gaisro signalizacijos montavimas",
        "Perimetro apsaugos sprendimai",
        "Patekimo kontrolės sistemos",
        "Telefonspynių ir vaizdo telefonspynių įrengimas",
        "Automobilių numerių atpažinimo sistemos",
        "Stebėjimo postų ir vaizdo archyvavimo sprendimai",
        "Dokumentacijos parengimas eksploatacijai"
      ],
      equipment: ["IP CCTV", "Patekimo kontrolė", "Gaisro signalizacija", "Perimetro apsauga", "LPR", "Vaizdo archyvai"]
    },
    solar: {
      category: "Atsinaujinanti energetika",
      title: "Saulės elektrinės",
      summary: "Projektuojame, montuojame ir prižiūrime saulės elektrines verslo, infrastruktūros ir gyvenamuosiuose objektuose.",
      image: "../5gtech-komanda-v1/team-work-02.jpg",
      work: [
        "Techninis sprendimo įvertinimas ir projektavimas",
        "Saulės modulių montavimas",
        "Inverterių montavimas ir prijungimas",
        "Konstrukcijų įrengimas ant stogų",
        "Antžeminių konstrukcijų įrengimas",
        "Elektrinės testavimas ir paleidimas",
        "Techninė priežiūra ir gedimų diagnostika",
        "Dokumentacijos parengimas atsakingoms institucijoms"
      ],
      equipment: ["Saulės moduliai", "Inverteriai", "Konstrukcijos", "Apsaugos įranga", "Matavimo įranga", "Stebėsena"]
    }
  };

  const key = document.body.dataset.service;
  const service = services[key];
  const main = document.querySelector("#content");
  if (!service || !main) return;

  const workItems = service.work.map((item, index) => `
    <div class="service-row">
      <span>${String(index + 1).padStart(2, "0")}</span>
      <p>${item}</p>
    </div>`).join("");

  const equipmentItems = service.equipment.map((item) => `<li>${item}</li>`).join("");

  main.innerHTML = `
    <section class="service-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="service-title">
      <div class="g5-container g5-grid service-hero__grid">
        <div class="service-hero__copy">
          <nav class="breadcrumbs" aria-label="Puslapio kelias">
            <a href="../5gtech-titulinis-v1/index.html">Pagrindinis</a><span>/</span>
            <a href="paslaugos.html">Paslaugos</a><span>/</span><span>${service.title}</span>
          </nav>
          <div class="g5-eyebrow">${service.category}</div>
          <h1 class="g5-display-xl" id="service-title">${service.title}</h1>
          <p class="g5-body-lg">${service.summary}</p>
          <a class="g5-button g5-button--primary" href="kontaktai.html">Aptarkime projektą <span class="g5-button__icon" aria-hidden="true">→</span></a>
        </div>
        <figure class="service-hero__media">
          <img src="${service.image}" alt="5G TECH specialistai infrastruktūros objekte">
          <figcaption>Demo image</figcaption>
        </figure>
        <div class="service-proof" aria-label="5G TECH patirties rodikliai">
          <div><strong>6000+</strong><span>įgyvendintų bazinių stočių</span></div>
          <div><strong>6</strong><span>Europos šalys</span></div>
          <div><strong>ISO</strong><span>9001 · 14001 · 45001</span></div>
        </div>
      </div>
    </section>

    <section class="g5-section g5-grid-lines" aria-labelledby="scope-title">
      <div class="g5-container section-head">
        <div class="g5-eyebrow">Ką atliekame</div>
        <div class="section-head__copy">
          <h2 class="g5-display-md" id="scope-title">Atliekami darbai.</h2>
          <p class="g5-body">Galime prisijungti prie atskiro projekto etapo arba įgyvendinti visą sutartą darbų apimtį.</p>
        </div>
      </div>
      <div class="g5-container service-list">${workItems}</div>
    </section>

    <section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="process-title">
      <div class="g5-container section-head section-head--dark">
        <div class="g5-eyebrow">Kaip dirbame</div>
        <div class="section-head__copy">
          <h2 class="g5-display-md" id="process-title">Penki projekto etapai.</h2>
        </div>
      </div>
      <ol class="g5-container steps">
        <li><span>01</span><strong>Įsigiliname</strong><p>Suprantame užduotį, sąlygas ir atsakomybes.</p></li>
        <li><span>02</span><strong>Suplanuojame</strong><p>Suderiname sprendimus, terminus ir komandą.</p></li>
        <li><span>03</span><strong>Įgyvendiname</strong><p>Atliekame numatytus montavimo darbus.</p></li>
        <li><span>04</span><strong>Patikriname</strong><p>Testuojame įrangą ir šaliname neatitikimus.</p></li>
        <li><span>05</span><strong>Perduodame</strong><p>Pateikiame dokumentaciją ir užbaigtą sprendimą.</p></li>
      </ol>
    </section>

    <section class="g5-section g5-grid-lines" aria-labelledby="equipment-title">
      <div class="g5-container section-head">
        <div class="g5-eyebrow">Techninė patirtis</div>
        <div class="section-head__copy">
          <h2 class="g5-display-md" id="equipment-title">Įranga ir sistemos.</h2>
        </div>
      </div>
      <ul class="g5-container equipment-list">${equipmentItems}</ul>
    </section>

    <section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="faq-title">
      <div class="g5-container section-head">
        <div class="g5-eyebrow">Dažniausi klausimai</div>
        <div class="section-head__copy"><h2 class="g5-display-md" id="faq-title">Projekto vykdymas.</h2></div>
      </div>
      <div class="g5-container faq-list">
        <details><summary>Ar galite atlikti tik dalį projekto darbų?</summary><p>Taip. Darbų apimtį deriname pagal techninę užduotį ir užsakovo poreikį.</p></details>
        <details><summary>Ar parengiate projekto dokumentaciją?</summary><p>Taip. Dokumentacijos apimtį ir formatą suderiname prieš darbų pradžią.</p></details>
        <details><summary>Kur vykdote projektus?</summary><p>Dirbame Lietuvoje ir kitose Europos šalyse, priklausomai nuo projekto apimties.</p></details>
      </div>
    </section>

    <section class="g5-section cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="cta-title">
      <div class="g5-container cta-grid">
        <div><div class="g5-eyebrow">Kitas žingsnis</div><h2 class="g5-display-lg" id="cta-title">Aptarkime jūsų techninę užduotį.</h2><p class="g5-body">Atsiųskite turimą informaciją – įvertinsime darbų apimtį ir pasiūlysime tolesnius veiksmus.</p></div>
        <a class="g5-button g5-button--primary" href="kontaktai.html">Susisiekti <span class="g5-button__icon" aria-hidden="true">→</span></a>
      </div>
    </section>`;
})();
