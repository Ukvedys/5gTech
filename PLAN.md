# 5G TECH — naujos svetainės kūrimo planas

**Data:** 2026-07-20
**Projektas:** nauja 5gtech.lt svetainė, kuriama nuo nulio (struktūra, dizainas, tekstai), valdoma WordPress.

---

## Pagrindiniai principai

1. **WordPress branduolio neredaguojame.** WordPress naudojame tik kaip turinio valdymo sistemą — visi mūsų sprendimai gyvena `wp-content/themes/` (mūsų tema) ir `wp-content/plugins/` (mūsų papildiniai). Taip WordPress versiją bet kada galėsime saugiai atnaujinti.
2. **Nauja svetainė kuriama iš pagrindų** — sena svetainė (5gtech.lt, veikia ant WordPress) naudojama tik kaip informacijos šaltinis: paslaugų sąrašui, faktams, tekstų atspirčiai.
3. **Dirbame lokaliai**, į serverį keliame tik pabaigoje. Lokali aplinka — **Local (WP Engine)** programa Mac'e.
4. **Dizainas pagal brandbook'ą** (2020-11-23): spalvos, logotipo taisyklės, šriftai.
5. **Trys kalbos: LT + EN + DE**, turinys visomis kalbomis 1:1.
6. Mūsų temos ir papildinių kodą versijuojame **git** — visada galime atstatyti ir matome, kas keitėsi.

---

## Etapas 1. Esamos svetainės informacijos surinkimas

Prieigos prie senos svetainės administravimo neturime, todėl visą viešai prieinamą turinį susirenkame iš puslapių ir susidedame į projekto aplanką kaip žaliavą naujiems tekstams.

**Darbai:**

1. Surinkti visų puslapių sąrašą (navigacija + sitemap): Pagrindinis, Paslaugos (6 kryptys), Apie mus, Rinkis mus, Kontaktai, Karjera, 5GTECH Academy, Mokymai, DUK ir kt.
2. Kiekvieno puslapio tekstą išsaugoti į `turinys/esama-svetaine/` (po vieną `.md` failą puslapiui).
3. Susidaryti turinio inventoriaus lentelę: puslapis → URL → sekcijos → kokie tekstai/nuotraukos yra → ką perrašysime, ką išmesime.
4. Išsisaugoti naudingas nuotraukas ir logotipus iš `wp-content/uploads` (sertifikatai, projektų nuotraukos, logotipai).
5. Užfiksuoti senų puslapių URL sąrašą — jo prireiks 301 nukreipimams, kad neprarastume Google pozicijų.

**Rezultatas:** pilnas senos svetainės turinio archyvas projekto aplanke.

---

## Etapas 2. Lokalios aplinkos paruošimas

**Darbai:**

1. Įdiegti **Local** programą (localwp.com) → sukurti svetainę `5gtech` (PHP 8.2+, MySQL 8, naujausias WordPress).
2. Projekto aplanke laikyti tik **mūsų kuriamus failus** (tema, papildinys, turinys, dokumentai) — Local turės savo WordPress kopiją, kurios neliečiame. Temos aplanką į Local svetainės `wp-content/themes/` prijungsime simboline nuoroda (symlink), kad kodas liktų projekto aplanke ir git'e.
3. Bazinė WordPress konfigūracija: lietuvių kalba, laiko juosta, permalink struktūra (`/pavadinimas/`), išjungti komentarai, panaikinti numatytieji įrašai.
4. `git init` temos ir papildinio aplankams.

**Siūloma projekto aplanko struktūra:**

```
5gTech/
├── wordpress/                  ← švarus WP archyvas (atsarginė kopija, neredaguojame)
├── tema/5gtech/                ← mūsų blokų tema (git)
├── papildinys/5gtech-core/     ← mūsų papildinys: įrašų tipai, formos (git)
├── turinys/
│   ├── esama-svetaine/         ← surinkta sena informacija
│   ├── nauji-tekstai/lt|en|de/ ← nauji tekstai trimis kalbomis
│   └── nuotraukos/
├── dizainas/                   ← brandbook, wireframe'ai, maketai
└── PLAN.md
```

**Rezultatas:** veikiantis lokalus WordPress su prijungta tuščia mūsų tema.

---

## Etapas 3. Naujos svetainės struktūra (informacinė architektūra)

Pagal „Tikslinis klientas" dokumentą svetainės tikslai: parodyti **visą paslaugų spektrą** (ne tik mobilaus ryšio montavimą), pademonstruoti **patirtį skaičiais** (6000+ bazinių stočių, operatoriai, geografija, įranga), gauti **B2B užklausas** ir **kandidatus į darbo pozicijas**.

**Siūloma puslapių struktūra (išlaikome viską, kas yra dabar, keičiame tik pateikimą):**

| Puslapis | Paskirtis |
|---|---|
| Pagrindinis | Įspūdis per 5 sek.: kas mes, skaičiai, paslaugos, klientų/įrangos logotipų juosta, CTA |
| Paslaugos (+ 6 vidiniai) | Mobiliojo ryšio infrastruktūra, fiksuoto ryšio tinklai, vidaus ryšio tinklai, elektros darbai, apsaugos sistemos, saulės elektrinės |
| Patirtis / Projektai | Skaičiai, žemėlapis (LT, SE, NO, DE...), operatoriai (Telia, Tele2, Vodafone, DT...), įrangos gamintojai (Ericsson, Nokia, Huawei...) |
| Apie mus | Istorija, komanda, vertybės, sertifikatai (ISO 9001/14001/45001, SSVA, VERT) |
| Rinkis mus | Vertybės, darbo metodas, elgesio kodeksas |
| Karjera | Darbo pozicijos (atskiri įrašai) + aplikavimo forma |
| 5GTECH Academy / Mokymai | Mokymų aprašymai |
| Naujienos | Naujienos, projektai, atnaujinimai (planuojama kelti reguliariai) |
| DUK | Klausimai–atsakymai |
| Kontaktai | Rekvizitai, žemėlapis, užklausos forma |

**Turinio tipai (Custom Post Types — registruojami mūsų papildinyje, ne temoje, kad turinys nepriklausytų nuo temos):**

- `paslauga` (paslaugos), `darbo_pozicija` (karjera), `projektas` (patirtis), naujienoms naudojame standartinius WP įrašus, `duk` (klausimai).

**Daugiakalbystė:** Polylang papildinys (nemokamas, tvarkingas). URL struktūra: `5gtech.lt/...` (LT), `/en/...`, `/de/...`, hreflang žymos automatiškai.

**Rezultatas:** patvirtinta svetainės medžio schema ir turinio tipų sąrašas.

---

## Etapas 4. Dizainas

**Pagrindas — brandbook'as:**

- Spalvos: tamsiai mėlyna `#063354` (pagrindinė), gradientas `#F17929 → #EC0062` (akcentams), raudona `#F12F29`, balta.
- Šriftai: **Gilroy ExtraBold** antraštėms (dėmesio: komercinis šriftas — reikės patikrinti/įsigyti web licenciją; jei ne — brandbook'e numatyta alternatyva **Poppins**, nemokamas Google Fonts), Poppins/Roboto tekstui.
- Logotipo naudojimo taisyklės (apsauginė erdvė, fonai) — iš brandbook'o.

**Įkvėpimas:** lineworksinc.com stilius — bėganti logotipų juosta (operatoriai, įrangos gamintojai), stambūs skaičiai, aiškus inžinerinis įspūdis: technologiška, patikima, tarptautinė.

**Darbai:**

1. Wireframe'ai (juodraštinis išdėstymas) pagrindiniam, paslaugų ir karjeros puslapiams.
2. Dizaino sistemos apibrėžimas `theme.json` faile: spalvų paletė, šriftų dydžiai, tarpai.
3. Nuotraukų klausimas: kokybiškų savų nuotraukų nėra — planuoti fotosesiją objektuose ir/arba parinkti laikinas stock nuotraukas paleidimui.

**Rezultatas:** patvirtinti pagrindinių puslapių maketai ir dizaino sistema.

---

## Etapas 5. Blokų temos ir papildinio kūrimas

**Tema `5gtech` (block theme):**

- `theme.json` — visa dizaino sistema (spalvos, šriftai, tarpai) vienoje vietoje.
- Šablonai (`templates/`): pagrindinis, puslapis, paslauga, projektas, darbo pozicija, naujiena, archyvai, 404.
- Dalys (`parts/`): antraštė su meniu ir kalbų perjungikliu, poraštė.
- Šablonų ruošiniai (`patterns/`): hero sekcija, paslaugų kortelės, skaičių juosta (6000+ stočių), logotipų karuselė, sertifikatų sekcija, CTA juosta, DUK akordeonas, kontaktų forma.
- Viskas redaguojama Gutenberg redaktoriumi — turinį vėliau galėsite keisti patys be programuotojo.

**Papildinys `5gtech-core`:**

- Įrašų tipų registracija (paslaugos, projektai, pozicijos, DUK).
- Smulki verslo logika (pvz., skaičių/statistikos laukai).

**Kiti papildiniai (tik patikimi, iš oficialaus katalogo):**

- Polylang — daugiakalbystė;
- Contact Form 7 arba WPForms Lite — užklausų ir aplikavimo formos (+ failo prisegimas CV);
- Yoast SEO arba Rank Math — SEO, sitemap, hreflang;
- WebP/vaizdų optimizavimas (pvz., Performance Lab);
- UpdraftPlus — atsarginės kopijos (serveryje).

**Rezultatas:** veikianti svetainė lokaliai su visais šablonais ir demo turiniu.

---

## Etapas 6. Turinio kūrimas ir sukėlimas

1. Nauji LT tekstai visiems puslapiams — remiantis surinkta senos svetainės medžiaga, „Tikslinis klientas" atsakymais ir pozicionavimu: *techninis partneris su pilno spektro galimybėmis* (ne vien mobilaus ryšio montuotojai).
2. Akcentai tekstuose: techninė kompetencija, patirtis (6000+ stočių), geografija, operatorių ir gamintojų sąrašai, sertifikatai, greita reakcija, atsakomybė.
3. Vertimai EN ir DE (1:1 su LT).
4. Nuotraukų paruošimas: dydžių optimizavimas, WebP formatas, aprašymai (alt tekstai) visomis kalbomis.
5. Meniu, poraštės, formų ir 404 puslapio turinys visomis kalbomis.

**Rezultatas:** pilnai užpildyta svetainė trimis kalbomis lokalioje aplinkoje.

---

## Etapas 7. Testavimas ir kokybės kontrolė

- Responsyvumas: telefonas / planšetė / kompiuteris.
- Greitis: Lighthouse/PageSpeed (tikslas — 90+ mobile).
- Formos: užklausų ir CV formų veikimas, laiškų pristatymas.
- Daugiakalbystė: kalbų perjungimas, hreflang, ar niekur neliko ne tos kalbos teksto.
- SEO: meta pavadinimai/aprašymai, antraščių hierarchija, sitemap.
- Naršyklės: Chrome, Safari, Firefox, Edge.
- Saugumas: administratoriaus slaptažodžiai, prisijungimo apsauga, naujausios versijos.

---

## Etapas 8. Perkėlimas į serverį ir paleidimas

1. Hostingo parinkimas/patikra: PHP 8.2+, MySQL/MariaDB, SSL sertifikatas, SSH/SFTP prieiga.
2. Svetainės perkėlimas iš Local į serverį (Duplicator arba rankinis DB + failų perkėlimas).
3. **301 nukreipimų žemėlapis** iš senų URL į naujus (pagal 1 etapo sąrašą) — SEO pozicijoms išsaugoti.
4. DNS perjungimas, SSL, el. pašto siuntimo patikra (SPF/DKIM, kad formų laiškai nekristų į spam).
5. Google Search Console + Analytics prijungimas, naujo sitemap pateikimas.
6. Atsarginių kopijų grafikas serveryje.

---

## Etapas 9. Priežiūra po paleidimo

- Reguliarūs WordPress, temos ir papildinių atnaujinimai (saugu, nes branduolio nemodifikavome).
- Naujienų ir projektų kėlimas — per WP administravimą, be programuotojo.
- Periodinė greičio ir saugumo patikra.

---

## Eiliškumas ir apytikslė trukmė

| Etapas | Trukmė |
|---|---|
| 1. Senos svetainės turinio surinkimas | 1–2 d. |
| 2. Lokali aplinka | 0,5 d. |
| 3. Struktūra | 1–2 d. |
| 4. Dizainas | 3–5 d. |
| 5. Tema + papildinys | 5–10 d. |
| 6. Turinys (3 kalbos) | 5–7 d. |
| 7. Testavimas | 2–3 d. |
| 8. Paleidimas | 1–2 d. |

## Atviri klausimai

1. **Gilroy šrifto web licencija** — pirkti ar naudoti Poppins?
2. **Hostingas** — kur talpinsime (dabartinis tiekėjas ar naujas)?
3. **Nuotraukos** — fotosesija objektuose ar stock paleidimui?
4. **Vertimai EN/DE** — versime patys / vertėjas / AI vertimas su peržiūra?
5. **Senos svetainės prieiga** — jei vėliau atsiras, galėsime pasiimti originalias nuotraukas iš `uploads` geresne kokybe.
