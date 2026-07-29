# 5G TECH WordPress integracijos auditas

**Data:** 2026-07-29
**Apimtis:** tema `5gtech` (functions.php, theme.json, 26 šablonai, header/footer, CSS/JS), papildinys `5gtech-core` (visi 17 failų, 6 646 eil.), mu-papildinys `5gtech-local-mail.php`, `wp-config.php`. Palyginta su WORDPRESS-INTEGRACIJOS-PLANAS.md ir 2026-07-29 testų ataskaita.

---

## 1. Bendras vertinimas

Integracija padaryta tvarkingai ir drausmingai. **WordPress branduolys, numatytosios temos ir trečiųjų šalių papildiniai nepaliesti** — visas kodas gyvena tik `wp-content/themes/5gtech/` ir `wp-content/plugins/5gtech-core/` (plius vienas mu-papildinys vietiniam paštui). Saugumo higiena vientisai gera: **kritinių saugumo spragų nerasta**.

Didžiausia rizika ne saugumas, o **architektūrinė skola prieš daugiakalbystę**: visame kode nėra nė vieno vertimo mechanizmo, o lietuviški puslapių slug'ai įsiūti į temą, papildinį ir šablonų pavadinimus. Jei EN/DE bus diegiama vėliau nekeičiant požiūrio dabar, teks perrašinėti didelę dalį kodo. Antra pagal svarbą — **redaktorius gali negrįžtamai sugadinti puslapį** (ištrynęs bloką, kurio negalės įdėti atgal).

| Sritis | Būklė |
|---|---|
| WP branduolio nekeitimas (projekto taisyklė) | ✅ Laikomasi |
| Saugumas (nonce, teisės, sanitizacija, escape) | ✅ Gerai, kelios vidutinės pastabos |
| CPT / rolių / nustatymų architektūra | ✅ Gerai |
| Daugiakalbystė (LT+EN+DE planas) | 🔴 Neparuošta — blokuojanti skola |
| Redaktoriaus apsauga nuo puslapio sugadinimo | 🔴 Nėra (nei templateLock, nei inserter) |
| GDPR (šriftai, formos) | 🟠 Google Fonts iš išorės; formos tvarkingos |
| Blokų registravimas pagal planą (block.json) | 🟠 Nukrypimas nuo plano |
| Smulkus tvarkingumas (slug'ai, URL, dublikatai) | 🟠 Daug smulkių trapumų |

---

## 2. Kas padaryta gerai

Saugumo pusėje visi `save_post_*` ir `admin_post_*` apdorotojai turi nonce patikrą, autosave apsaugą, `current_user_can()` ir kiekvieno lauko sanitizaciją; išvedimas nuosekliai escape'inamas (`esc_html`/`esc_url`/`esc_attr`); tiesioginių SQL užklausų nėra; visur `wp_safe_redirect`; `wp-config.php` turi `DISALLOW_FILE_EDIT` ir aiškiai pažymėtas kaip vietinis. Kandidatavimo CV niekur nesaugomas svetainėje — kopijuojamas į laikiną katalogą, prisegamas prie laiško ir ištrinamas (atitinka reikalavimą).

Architektūros pusėje turinio tipai, taksonomijos, rolės ir nustatymai yra papildinyje, ne temoje — išjungus temą turinys lieka (plano 15.2 kriterijus). Rolių/teisių sistema padaryta teisingai: atskiri capability tipai kiekvienam CPT su `map_meta_cap`, versijuotas rolių sinchronizavimas, `option_page_capability_*` filtras. Tema — korektiška FSE struktūra (theme.json v3 su schema, teisingi šablonų pavadinimai, template parts), CSS versijuojamas per `filemtime`, JS kraunamas `defer`. SEO sluoksnis apgalvotas: apsauga nuo konflikto su Yoast/RankMath/SEOPress/AIOSEO, paslėpti įrašai išimami iš sitemap, 404 turi `noindex`, seni URL turi 301. Mu-papildinys vietiniam paštui — saugus ir teisingai apribotas `wp_get_environment_type() === 'local'`.

---

## 3. Radiniai pagal svarbą

### 3.1 AUKŠTA SVARBA — spręsti prieš kuriant daugiau turinio

**A1. Daugiakalbystė neįmanoma be perdirbimo.** Visame papildinyje ir temoje nėra nė vieno `__()`/`_e()` kvietimo su text domain (vienintelė išimtis — `service-blocks.php:62` ir pora `wp_die()` žinučių). Nėra `load_plugin_textdomain()`. Visi admin ir viešų puslapių tekstai — LT literalai PHP kode. Polylang/gettext neturės ko versti. Kol tekstų dar ne šimtai — pigiausia apvynioti dabar.

**A2. Lietuviški slug'ai įsiūti į kodą trijuose sluoksniuose.** (1) Temos `functions.php:108,129` CSS kraunamas pagal `is_page('apie-mus')`, `is_page(array('karjera','naujienos','kontaktai',...))`; (2) 14 šablonų pavadinti pagal slug'ą (`page-akademija.html`, `page-kontaktai.html`...); (3) papildinyje kietai užkoduoti `home_url('/kontaktai/')`, `/karjera/#positions`, `/kandidatuoti/`, `/privatumo-politika/`, `/naujienos/`, o `team.php:763` ir admin nukreipimai tikrina `post_name === 'apie-mus'`. Pervadinus puslapį ar sukūrus EN/DE vertimą kitu slug'u — CSS nebeužsikraus, šablonas nebepritaikomas, nuorodos ves į LT. Sprendimas: puslapius identifikuoti ne pagal slug, o pagal turinį (`has_block('g5tech/...')` — taip jau daroma `admin.php:473`) arba per priskirtą šabloną/meta, o nuorodas generuoti iš nustatymų ar `get_permalink()`.

**A3. Redaktorius gali negrįžtamai ištrinti puslapio bloką.** Visi 27 blokai registruoti su `inserter => false`, o raktiniai puslapiai (titulinis, kontaktai, karjera...) yra tiesiog `wp:post-content` su vienu `g5tech/*` bloku turinyje. `templateLock` nenaudojamas niekur. Redaktorius, atidaręs puslapį, gali bloką ištrinti — ir per UI nebegalės įdėti atgal. Tai tiesiogiai kertasi su plano 3.3 („turinio režimas / užrakinimas") ir 15.3–15.4 priėmimo kriterijais. Sprendimas: `templateLock` šablonuose arba `use_block_editor_for_post` išjungimas tokiems puslapiams (kaip jau padaryta Mokymams), arba blokus dėti tiesiai į šabloną, ne į turinį.

**A4. Google Fonts iš `fonts.googleapis.com` — GDPR rizika ES auditorijai.** `functions.php:17,79` Poppins kraunamas iš Google kiekviename puslapyje, ir jis realiai naudojamas: `site.css:62` kūno šriftų stekas yra `"Poppins", "DM Sans", Arial`. Lankytojo IP perduodamas Google be sutikimo (LG München precedentas). DM Sans jau yra lokaliai (`assets/fonts/*.woff2`) — reikia arba Poppins atsisiųsti lokaliai, arba jo atsisakyti.

**A5. Šriftų sistema išsibarsčiusi (susiję su A4).** Trys nesuderinti pavadinimai: `site.css` deklaruoja „DM Sans", `internal/shared.css` — atskirą dublikatą „DM Sans Local", o `home.css:19` ir `team/tokens.css:38` pirmu numeriu nurodo **„Gilroy", kurio @font-face niekur nėra** (licencijos klausimas atviras — žr. projekto atmintį), tad realiai krinta į Poppins. `theme.json` deklaruoja tik „DM Sans" be `fontFace`. Reikia vieno sprendimo: kuris šriftas pagrindinis, deklaruoti jį `theme.json` `fontFace` (lokalūs woff2) ir suvienodinti stekus.

**A6. Vienakalbis turinio modelis nustatymuose.** Titulinio, „Apie mus", Mokymų tekstai gyvena `g5tech_settings`, `g5tech_about_content`, `g5tech_training_page_content` opcijose be kalbos dimensijos. Polylang opcijų masyvų neverčia — reikės `pll_register_string` arba opcijų per kalbą. Į tą patį stalčių: meniu ir poraštė yra statiniai LT `wp:navigation-link` `header.html`/`footer.html` failuose — EN/DE meniu mechanizmo nėra.

**A7. Teisiniai ir „brošiūriniai" tekstai užkoduoti PHP.** Visa privatumo ir slapukų politika (`legal.php:56–83`), Akademijos puslapis, karjeros privalumai/atrankos žingsniai, titulinio sekcijų tekstai — PHP kode. Redaktorius jų pakeisti negali, teisinio teksto pataisa reikalauja deploy'aus, vertimas neįmanomas. Mokymų puslapiui jau padarytas opcijomis valdomas modelis (plano 16 pastaba) — tą pačią schemą verta pritaikyti bent privatumo politikai ir Akademijai.

### 3.2 VIDUTINĖ SVARBA

**V1. Formos + puslapių talpykla (cache).** `wp_nonce_field` generuojamas anoniminiams lankytojams (`forms.php:181,241`); įjungus bet kokį puslapių cache produkcijoje, nonce pasens ir visos užklausos kris su „security" klaida. Prieš paleidimą: išimti formų puslapius iš cache arba atsisakyti nonce anoniminėms formoms ir remtis kitomis priemonėmis.

**V2. Apsauga nuo šlamšto — tik honeypot.** Nei laiko spąstų, nei rate limiting, nei CAPTCHA; `admin-post.php` galima skriptuoti ir užversti `info@5gtech.lt`. Planas (10.2) numato formų papildinį su apsauga — jei liekama prie savų formų, bent pridėti laiko spąstus ir paprastą IP ribojimą per transient.

**V3. CV failo tvarkymas.** `forms.php:381` naudoja `copy()` be `is_uploaded_file()` patikros (turėtų būti `is_uploaded_file()` + `move_uploaded_file()` arba bent patikra); jei `wp_mail()` nulūžta (SMTP timeout), laikina CV kopija lieka `get_temp_dir()` — trynimą verta perkelti į `try/finally` logiką ar `shutdown` hook'ą. `Reply-To` antraštėje vardas dedamas neapdorotas (`forms.php:315,426`) — lietuviškos raidės/kableliai gali sugadinti antraštę kai kuriems MTA (injekcijos nėra, nes `sanitize_text_field` pašalina naujas eilutes).

**V4. `contact_page_url` klaida.** Nustatymas sanitizuojamas `esc_url_raw` (priima absoliutų URL), bet visur naudojamas kaip `home_url( $contact_url )` (`projects.php:491`, `service-blocks.php:95,296`, `homepage.php:170`, `site-blocks.php:159`). Adminui įvedus `https://...` gausis `http://5gtech.testhttps://...`. Sanitizuoti kaip kelią arba nebevynioti į `home_url()`.

**V5. Rolių meniu slėpimas — kosmetinis.** `remove_menu_page()` slepia meniu, bet neatima teisių: `g5_content_editor` su `edit_posts` tiesioginiu URL pasiekia `tools.php` ir `edit-comments.php` (`admin.php:569–604`). Taip pat teisės tikrinamos pagal rolės slug'ą (`in_array($role, $user->roles)`), o ne per capability — nestandartinis ir trapus kelias. Papildomai: `$submenu['edit.php'][5][0]` indeksas nėra garantuotas tarp WP versijų.

**V6. `g5_team`/`g5_job` meta neregistruota per `register_post_meta()`.** Sanitizacija vyksta tik meta box save kelyje; WP-CLI, importo įrankiai ar Polylang meta kopijavimas ją apeis. Kituose failuose (services, projects, partners, faqs) meta registruojama — suvienodinti.

**V7. Paslėptų įrašų filtravimas paieškoje.** `the_posts` filtras (`seo.php:344–370`) išima paslėptus projektus/pozicijas/narius jau PO puslapiavimo skaičiavimo — paieškos puslapiai gali būti „trumpi" su fantomine paginacija. Teisingas kelias — `pre_get_posts` + meta_query. Taip pat neaktyvių pozicijų/paslėptų profilių `template_redirect` neturi išimties administratoriui — neįmanoma peržiūrėti prieš publikuojant (`jobs.php:646`, `team.php:1181`).

**V8. SEO smulkmenos.** `og:locale` ir JSON-LD `inLanguage` kietai `lt_LT` (`seo.php:140,210,219`); visi paprasti puslapiai gauna vieną identišką atsarginį meta aprašymą (`seo.php:69`) — masinis dublikatas; archyvo canonical perima query string'us (`?utm_...` patenka į canonical, `seo.php:162`); pasibaigusios pozicijos grąžina 302 vietoj 410/404 — Google jas laiko aktyviomis.

**V9. `index.html` šablono užklausa neveiks archyvams.** Užklausa ne `"inherit":true`, todėl kategorijų/datų archyvai ir tinklaraščio puslapiavimas visada rodys tuos pačius 10 naujausių įrašų. Naujienų blokas (`news.php`) turi 12 įrašų lubas be puslapiavimo — 13-a naujiena taps nepasiekiama.

### 3.3 ŽEMA SVARBA / PASTABOS

Blokai registruoti be `block.json` (planas §2 reikalavo `block.json`) ir be redaktoriaus peržiūros — redaktoriuje matomi kaip pilki placeholder'iai; `'autoRegister' => true` nėra WP supports raktas (niekur neveikia). `menu_position` 22 naudojamas trims CPT (settings/projects/faqs) — tvarka nedeterministinė. Kietas produkcinis URL `https://5gtech.lt/.../5GTech_Kodeksas...pdf` poraštėje ir `team.php:334` — neveiks staging'e. `g5_job_expires` nevaliduojamas kaip `Y-m-d` (ranka įvedus `2026.01.01` aktyvumo patikra klys). `g5_faq_service` meta neregistruota ir nevaliduojamas post tipas. `admin.php:272` kviečia kitų failų funkcijas be `function_exists()` apsaugos. `home.js` — be IIFE (globalūs `const`), `data-placeholder-link` — negyvas kodas. Nuotraukų alt tekstai: naujienose alt perrašomas įrašo pavadinimu, titulinio hero alt aprašo numatytąją nuotrauką net ją pakeitus. `contact-page` kontaktų kortelės parenkamos pagal LT pareigų fragmentus (`'direktor'`, `'vadov'`) — pakeitus formuluotę kortelė dings. Korektūra: `projects.php:489` „Atsiūskite" → „Atsiųskite". Poraštėje statinis „© 2026". Plano taksonomijų sąrašo (g5_country, g5_sector, g5_faq_topic, g5_job_location...) realizuotos tik 2 projektų taksonomijos, kita — meta laukai: veikia, bet jei reikės filtravimo pagal šalis/technologijas kitur — verta žinoti apie nukrypimą.

---

## 4. Atitiktis integracijos planui

| Plano reikalavimas | Būklė |
|---|---|
| §1 Tema + papildinys atskirai, branduolys nepaliestas | ✅ |
| §2 Blokai per `block.json` | ❌ runtime registracija be block.json |
| §3.3 `templateLock` / turinio režimas | ❌ nenaudojama (išskyrus admin formų puslapius) |
| §4 Turinio tipai (8) | ✅ visi yra (naujienos = `post`, kaip planuota) |
| §4 Taksonomijos (6) | ⚠️ tik 2 (projektų šalys/technologijos), likusios — meta |
| §6 Globalūs nustatymai vienoje vietoje | ✅ `g5tech_settings` (bet yra dubliuotų fallback'ų kode) |
| §10 Formų papildinys / SMTP | ⚠️ savos formos + vietinis interceptorius; SMTP dar nėra |
| §10 Daugiakalbystė LT/EN/DE | 🔴 neparuošta (žr. A1–A2, A6) |
| §11 Rolės (3) | ✅ padaryta teisingai (su V5 pastaba) |
| §12 Sauga ir kodo kokybė | ✅ iš esmės laikomasi; PHPCS/eslint proceso nesimato |
| §16 Valdomi viso puslapio moduliai | ⚠️ įgyvendinta Mokymams; Akademija/legal liko kode |

---

## 5. Rekomenduojama veiksmų seka

1. **Dabar, prieš kuriant likusį turinį:** apsispręsti dėl daugiakalbystės mechanikos ir apvynioti visas eilutes `__()/_e()` su `5gtech-core`/`5gtech` text domain + `load_plugin_textdomain()` (A1). Kuo vėliau — tuo brangiau.
2. **Kartu:** atsieti logiką nuo LT slug'ų — puslapio atpažinimas per `has_block()`/šabloną/meta, nuorodos per nustatymus arba `get_permalink()` (A2, V4).
3. **Redaktoriaus apsauga:** užrakinti raktinius puslapius (templateLock arba Mokymų tipo admin formos) (A3).
4. **Šriftai:** išimti Google Fonts, apsispręsti Gilroy klausimą, suvienodinti į vieną lokalų šeimos pavadinimą per `theme.json` `fontFace` (A4–A5).
5. **Prieš paleidimą:** formų cache/nonce sprendimas, CV tvarkymo pataisos, rate limiting, SMTP (V1–V3); SEO smulkmenos (V8); rolių teisių sugriežtinimas (V5); `register_post_meta` team/jobs (V6).
6. **Nuolat:** įsivesti PHPCS su WordPress Coding Standards — didžioji dalis smulkmenų būtų pagauta automatiškai.

---

*Auditas atliktas statine kodo peržiūra (be veikiančios aplinkos). Eilučių numeriai — 2026-07-29 failų versijų. Saugumo išvados galioja peržiūrėtai apimčiai; trečiųjų šalių papildinių (sqlite-database-integration) kodas nevertintas.*
