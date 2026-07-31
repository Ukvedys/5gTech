# 5G TECH WordPress valdymo standartas

**Versija:** 2 (2026-07-30). Pakeičia tos pačios dienos 1 versiją.
**Kodėl 2 versija:** pirmoji versija aprašė, kaip suvienodinti esamą savos gamybos administraciją. Patikrinus ją pagal dabartines WordPress praktikas (WP 7.0 „Armstrong", išleista 2026-05-20) paaiškėjo, kad dalis to, ką statome patys, WordPress'e jau yra — ir būtent tose vietose yra visi rasti kritiniai bug'ai. Tikslinis modelis perrašytas į WP-natyvų.
**Audituota:** `5gtech-core` 0.12.0, tema `5gtech`, gyva duomenų bazė.
**Patvirtinti sprendimai:** kryptingas suderinimas su WP standartu (ne pilnas perrašymas) + perėjimas į Polylang.

Eilučių numeriai atitinka 2026-07-30 failų versijas.

---

## 1. Verdiktas

### 1.1 Kas daroma teisingai

Pamatas sveikia ir jo keisti nereikia: sava tema ir savas papildinys, WordPress branduolys nepaliestas, turinio tipai papildinyje, `theme.json` dizaino tokenai, rolės per `capability_type` + `map_meta_cap`, Settings API bendriems duomenims, tvarkinga saugumo higiena (nonce, teisės, sanitizacija, escape'inimas). Tai atitinka oficialias rekomendacijas ir yra geras startas.

### 1.2 Keturi nukrypimai

| Ką padarėme patys | Ką WordPress tam turi | Kodėl tai svarbu |
|---|---|---|
| Blokų redaktorius apeitas — puslapio redagavimas nukreipiamas į savus ekranus | `templateLock: "contentOnly"` + blokų užrakinimas + šablonai. WP 7.0 pridėjo šablonų **content-only režimą**, Spotlight ir Isolated redagavimo režimus | Tai tiksliai tas uždavinys, kurį sprendžiame. Jūsų pačių integracijos planas (§3.3, §17) šiuos dokumentus cituoja, bet kode jie nepanaudoti |
| Puslapių turinys opcijų masyvuose (`g5tech_about_content`, `g5tech_career_page_content`, `g5tech_training_page_content`, `g5tech_structured_content`) | Post meta per `register_post_meta()` su `show_in_rest` ir `revisions_enabled`; Block Bindings API laukams susieti su blokais | Opcija yra globali: nėra revizijų, peržiūros, autosave, REST — ir **nėra kalbos dimensijos**. Iš to seka daugiakalbystės skausmas |
| Blokai be `block.json`, su `inserter: false`, po vieną visam puslapiui | WP 7.0 **oficialiai leidžia registruoti blokus vien PHP** ir pats sugeneruoja inspektoriaus valdiklius | PHP registracija nebėra nukrypimas — bet 7.0 tai padarė tam, kad blokai gautų tikrą redaktoriaus sąsają. Dabartiniai blokai redaktoriuje nematomi ir, kartą ištrynus, neįdedami atgal |
| Sava daugiakalbystė: regex'ai ant išvesties buferio, raktas = lietuviškas tekstas | Polylang (numatytas jūsų plane §10.3) arba WPML | Mažiausiai standartinė vieta projekte. Iš jos tiesiogiai seka atsirišantys vertimai, globali raktų erdvė, `<script>` gadinimo rizika, URL dublikatai, neverčiamas JSON-LD |

### 1.3 Ko nedarome

Nerašome visko iš naujo. Svetainė veikia, funkciniai testai praeina, vertimų katalogai jau paruošti. Keičiame tik ten, kur platforma grąžina konkrečią naudą: revizijas, peržiūrą, saugų redagavimą ir tvarkingą daugiakalbystę.

---

## 2. Tikslinė architektūra

### 2.1 Pamatinės taisyklės

> **1. Jei WordPress tai jau turi — naudojame WordPress.**
> **2. Vienas turinys — viena vieta.**
> **3. Vienas veiksmas — vienas pavadinimas.**

Pirmoji taisyklė yra svarbiausia ir nauja. Prieš kuriant naują administracijos ekraną, lauką ar mechanizmą — patikrink, ar to nėra branduolyje. Kiekvienas savas sluoksnis yra kodas, kurį reikės tikrinti po kiekvieno WordPress atnaujinimo.

### 2.2 Kur gyvena turinys — keturi sluoksniai

Kiekvienas svetainės tekstas priklauso lygiai vienam sluoksniui.

**1 sluoksnis — Katalogas.** Objektai, turintys savo gyvenimą: paslaugos, projektai, komandos nariai, darbo skelbimai, DUK, partneriai, naujienos.
*Mechanizmas:* CPT + `register_post_meta()` su `show_in_rest => true`, `sanitize_callback`, `auth_callback` ir `revisions_enabled => true` (post meta revizijos yra branduolyje nuo WP 6.4).
*Ką taisome:* `g5_team`, `g5_job` ir `g5_module` meta laukai šiandien visai neregistruoti — rašomi tiesiai `update_post_meta`. Registruojame visus.

**2 sluoksnis — Bendri duomenys.** Faktai, kartojami keliuose puslapiuose: rekvizitai, kontaktai, skaičiai, sertifikatai, šalys, proceso etapai.
*Mechanizmas:* Settings API, opcija `g5tech_settings`. **Šis sluoksnis lieka kaip yra** — tai teisėta opcijų paskirtis.
*Sąlyga:* opcija privalo likti `autoload => false` ir neaugti į turinio saugyklą.

**3 sluoksnis — Puslapio turinys.** Tekstai, priklausantys vienam puslapiui: hero, sekcijų antraštės, kortelės, sąrašai, CTA.
*Mechanizmas:* blokai `post_content`, užrakinti `templateLock: "contentOnly"`; struktūriniai laukai — post meta, susieti su blokais per **Block Bindings API** (WP 6.5, praplėsta 6.6 ir 7.0).
*Ką taisome:* visą turinį iš `g5tech_about_content`, `g5tech_career_page_content`, `g5tech_training_page_content` ir `g5tech_structured_content` perkeliame į atitinkamo puslapio meta arba blokus.

**4 sluoksnis — Pakartotinai naudojama sekcija.** Sekcija, realiai rodoma daugiau nei viename puslapyje.
*Mechanizmas:* **sinchronizuotas šablonas** (synced pattern, `wp_block`) su pattern overrides ten, kur reikia puslapiui specifinių nukrypimų. Tai natyvus atitikmuo dabartinei „Turinio modulių" bibliotekai — su ta pačia logika „pakeitei vienoje vietoje, atsinaujino visur", tik su WP redaktoriumi, revizijomis ir be savo priskyrimų opcijos.
*Katalogo valdomos sekcijos* (paslaugų tinklelis, komanda, naujienos, partneriai) lieka dinaminiais blokais — bet su `block.json` ir matomi inserteryje.

**Ko nelieka niekur:** viešo teksto PHP failuose. Šiandien kietai užkoduota privatumo ir slapukų politika, Akademijos hero ir CTA, Vadovų ir Projektų vadovų hero, DUK grupių pavadinimai, Kontaktų formos etiketės, darbo skelbimų grupių pavadinimai (keturiose vietose) ir titulinio `intro` sekcija.

### 2.3 Redagavimo patirtis: `contentOnly`, ne savi ekranai

Puslapis redaguojamas **įprastame WordPress blokų redaktoriuje**. Dizaino apsauga užtikrinama ne tuo, kad redaktorius paslepiamas, o tuo, kad blokai užrakinami.

Praktiškai:

- Puslapio šablonas arba pradinis blokų rinkinys registruojamas su `templateLock: "contentOnly"`. Redaktorius mato tik teksto ir medijos laukus; konteineriai, kolonos ir tarpai iš sąrašo dingsta; naujų blokų įterpti negalima; vaikiniai blokai automatiškai užrakinami nuo perkėlimo ir trynimo.
- Atrakinimo mygtukas („Modify") atimamas ne administratoriams per `block_editor_settings_all` filtrą (`canLockBlocks`).
- Leistinų blokų sąrašas apribojamas `allowed_block_types_all` filtru: 5G TECH blokai plius pastraipa, antraštė, sąrašas, nuoroda, paveikslėlis, lentelė.
- Kiekvienas blokas gauna `block.json`, pavadinimą, ikoną ir peržiūrą redaktoriuje. `inserter: false` lieka tik tiems blokams, kurie iš tikrųjų neturi būti dedami ranka.
- Sekcijų matomumas — WP 7.0 blokų matomumo valdikliai, ne savos varnelės opcijoje.

Ką tai duoda iš karto ir be papildomo darbo: revizijas su galimybe atstatyti, tikrą peržiūrą prieš publikavimą, autosave, juodraščio ir laukiančio patvirtinimo būsenas, mobilų redagavimą ir suderinamumą su Polylang.

**Svarbi techninė pastaba:** WP 7.0 įgalino privalomą redaktoriaus iframe'ą. Temos stiliai privalo būti prijungti per `add_editor_style()` arba `theme.json` — kitaip redaktoriuje jie nebus matomi. Tema jau naudoja `add_editor_style()`, bet po perėjimo tai reikia patikrinti.

### 2.4 Kalbos: Polylang

Savas vertimų sluoksnis pakeičiamas Polylang — kaip ir numatė pradinis integracijos planas.

Kaip veikia: kiekviena kalba yra **tikri WordPress įrašai** su savo slug'ais (`/apie-mus/`, `/en/about-us/`, `/de/ueber-uns/`). Kiekvienas turi savo revizijas, peržiūrą ir būseną. Sąsaja tarp kalbų — Polylang lentelėje, o ne teksto sutapime.

Kodėl tai išsprendžia esmines problemas:

- **Vertimas nebeatsiriša nuo teksto.** Dabar raktas yra pats lietuviškas tekstas, todėl pataisius LT formuluotę vertimas lieka pririštas prie senosios (radiniai K4 ir K8). Su Polylang tokio ryšio nebėra iš principo.
- **Dingsta URL dublikatai.** Dabar `/en/paslaugos/` ir `/en/services/` abu grąžina 200 be canonical (V12). Polylang duoda po vieną adresą kalbai ir natyvų `hreflang`.
- **Verčiasi viskas**, įskaitant JSON-LD, meta aprašymus ir formų pranešimus (V14, V15) — nes tai ne teksto pakeitimas išvestyje, o atskiras įrašas.
- **Dingsta `<script>` gadinimo rizika** (V11), nes nebelieka regex'ų ant išvesties buferio.

Esamas darbas nedingsta: `en.php` ir `de.php` katalogai (~136 KB paruoštų vertimų) panaudojami kaip pradinis turinys — Polylang Pro nuo 3.3 versijos turi XLIFF eksportą ir importą turinio vertimams, o sąsajos eilutėms — eilučių vertimų importą/eksportą.

Kas lieka savo rankose: kalbų perjungiklis (jau parašytas, tik niekur neįdėtas — V13) gali būti pakeistas Polylang perjungikliu arba paliktas kaip savas blokas, pridėjus jį į temos antraštę ir įjungus `inserter`.

**Perėjimo sąlyga:** Polylang reikalauja, kad kiekviena kalba būtų atskiras įrašas. Todėl 2 etapas (turinys iš opcijų į post meta ir blokus) turi būti atliktas **prieš** Polylang diegimą — opcijų vertimų Polylang neverčia.

### 2.5 Kas pagrįstai lieka savo ekranuose

Ne viskas turi tapti bloku. Savi administracijos ekranai yra teisėti ten, kur turinys nėra puslapis:

- **„5G TECH nustatymai"** — bendri duomenys. Settings API, kaip dabar.
- **Modulių arba šablonų biblioteka** — jei po perėjimo į sinchronizuotus šablonus liks poreikis matyti „kur kuri sekcija naudojama", tam gali likti paprastas sąrašas.
- **Darbo skelbimų, komandos, partnerių sąrašai** — tai CPT sąrašai, natyvūs.

Ko nelieka: savų ekranų, kurie **pakeičia** puslapio redagavimą. Visi `load-post.php` nukreipimai (`modules.php:1148`, `team.php:809`, `jobs.php:150`, `admin.php:560`) panaikinami.

### 2.6 Vienodi valdikliai

| Uždavinys | Vienintelis leistinas sprendimas |
|---|---|
| Sekcijų tvarka | Blokų tvarka redaktoriuje (tempimas arba List View). Jokių skaitinių svorių, CSS `order`, `section_order` opcijų ar savo tempimo implementacijų |
| Sekcijos matomumas | WP 7.0 blokų matomumo valdikliai arba bloko pašalinimas. Tuščias turinys sekcijos neslepia |
| Įrašo matomumas | Įrašo būsena (juodraštis / paskelbta) ten, kur įmanoma; viena varnelė ten, kur reikia papildomos sąlygos, numatytai **įjungta** |
| Struktūriniai laukai | `register_post_meta()` + Block Bindings. Laukų sąsajai — Secure Custom Fields (WordPress.org palaikomas ACF atskilimas nuo 2024-10) arba natyvūs meta laukai |
| Kartotiniai elementai | InnerBlocks arba SCF repeater. Savų `g5tech_render_repeater` implementacijų nebelieka |
| Nuotrauka | Bloko medijos valdiklis arba „Specialusis paveikslėlis". Dvi savos implementacijos panaikinamos |
| Išsaugojimas | WordPress „Atnaujinti". Savų mygtukų nelieka ten, kur redaguojamas puslapis |
| Teisių trūkumas | `wp_die()` su aiškia žinute. Jokių tuščių ekranų |

### 2.7 Žodynas

| Veiksmas | Vienintelis pavadinimas |
|---|---|
| Atidaryti puslapio turinį | **Redaguoti puslapį** |
| Atidaryti sekcijos duomenų šaltinį | **Redaguoti: <šaltinis>** |
| Atidaryti bendrus duomenis | **5G TECH nustatymai** |
| Darbo skelbimų sąrašas | **Darbo skelbimai** |
| Naujas darbo skelbimas | **Kurti naują skelbimą** |
| Atidaryti viešą puslapį | **Peržiūrėti** (WordPress standartas) |

Iš puslapių sąrašo eilutės lieka **viena** nuoroda. Šiandien „Apie mus" eilutėje jų keturios ir jos veda į du skirtingus ekranus.

### 2.8 Teisės

Taisyklė: **jei vartotojas mato nuorodą ar meniu punktą, jis privalo galėti tą ekraną atidaryti.** Šiandien taip nėra (A4).

- Meniu punkto teisė = ekrano atvaizdavimo teisė.
- Nukreipimas negali nuvesti ten, kur vartotojas neturi teisės.
- Meniu slėpimas nėra apsauga — kartu atimama ir teisė.
- Turinio redaktorius: puslapiai, paslaugos, projektai, naujienos, DUK, partneriai, bendri duomenys.
- Personalo redaktorius: komanda, darbo skelbimai, Karjeros ir Apie mus puslapiai, kandidatų DUK.

---

## 3. Puslapių žemėlapis

| Puslapis | Turinys DABAR | Tikslinė vieta |
|---|---|---|
| **Pagrindinis** | `g5tech_settings` + `g5tech_structured_content` + kietas kodas (`intro`, hero mygtukai, 5 skaidrės). Hero nuotrauka nepasiekiama | Blokai `post_content` (contentOnly); skaičiai ir sertifikatai lieka 2 sluoksnyje; hero nuotrauka — įprastas medijos valdiklis |
| **Paslaugos / Projektai** | Antraštės kietame kode, kortelės iš CPT | Dinaminis blokas su `block.json`; antraštės — bloko atributai |
| **Patirtis** | Visos antraštės kietame kode | Blokai `post_content`; duomenys iš 2 sluoksnio |
| **Apie mus** | `g5tech_about_content` + 4 kartotiniai; tvarkos opcija niekada nerašoma | Blokai `post_content` + post meta per Block Bindings; tvarka — blokų tvarka |
| **Karjera** | `g5tech_career_page_content` + 3 kartotiniai; grupių pavadinimai kietame kode 4 vietose | Blokai + meta; grupių pavadinimai → 2 sluoksnis, viena vieta |
| **Academy / Vadovams / Projektų vadovams** | `g5tech_structured_content`; hero ir CTA kietame kode; Vadovų CTA — asmens vardas kode | Blokai + meta; visas tekstas → 3 sluoksnis |
| **Mokymai** | `g5tech_training_page_content`; temos dubliuojasi su moduliu; `section_order` be UI | Blokai + meta; dublikatas panaikinamas |
| **DUK kandidatams** | 4 grupių pavadinimai ir įvadai kietame kode | Grupės → 2 sluoksnis; klausimai lieka `g5_faq` |
| **Kontaktai** | Hero ir formos etiketės kietame kode; kortelės parenkamos pagal pareigų teksto fragmentus („direktor", „vadov") | Blokai + meta; kortelės — aiškus laukas komandos nario įraše |
| **Naujienos** | Hero kietame kode | Blokai + meta |
| **Privatumo politika / Slapukai / Kandidatuoti** | 100 % kietas kodas, redaguoti neįmanoma | Blokai `post_content` (contentOnly). Teisinis tekstas privalo keistis be programuotojo |

---

## 4. Rasti bug'ai

Radiniai patikrinti dviem nepriklausomomis peržiūromis, kritiniai ir aukšti — dar kartą rankiniu būdu. Dalis pirminių įtarimų po patikros atmesta ir čia neįtraukta.

Žyma **→ išnyksta** reiškia, kad radinys dingsta savaime atlikus atitinkamą migracijos etapą ir jo atskirai taisyti nereikia.

### 4.1 Kritiniai — praranda arba sugadina duomenis

**K1. Modulio išsaugojimas nustumia jį į kiekvieno puslapio galą.** `modules.php:235-238` — modulis pašalinamas ir pridedamas gale. Pataisius rašybos klaidą modulyje, esančiame antru penkiuose puslapiuose, jis tampa paskutinis visuose. DB tai jau rodo: Mokymų tvarka `[195,196,145]`, nors migracija sukūrė `[145,195,196]`. **→ išnyksta 4 etape**

**K2. Dinaminio modulio išsaugojimas ištrina visus jo EN/DE vertimus.** `i18n-admin.php:359, 380-387` — vertimai perrašomi iš POST besąlygiškai; jei tekstų nuskaityti nepavyko (juodraštis), išsaugoma tuščia. Paskelbus anksčiau išverstą juodraštį, vertimai dingsta. Paaiškina, kodėl iš 38 dinaminių modulių vertimus turi tik 2. **→ išnyksta 3 etape**

**K3. Tuščias išsaugojimas sunaikina viso ekrano vertimus.** `i18n-admin.php:624-628, 654` — neveikiant JavaScript krovinys ateina tuščias, o kontekstas perrašomas. Vienas išsaugojimas ištrina visus to ekrano EN ir DE tekstus. **→ išnyksta 3 etape**

**K4. LT teksto pataisymas pririša seną vertimą prie naujo teksto.** `i18n-admin.php:404, 413-415` — turinys saugomas 10-u prioritetu, vertimai 30-u, jau su nauju LT tekstu, bet senu vertimu. **→ išnyksta 3 etape**

**K5. Kartotinio elemento pridėjimas EN režimu įrašo anglišką tekstą į lietuvišką šaltinį.** `admin-content-i18n.js:126-127, 234-236`. Dokumentacija teigia, kad EN/DE režimu šie valdikliai paslepiami — kode to nėra. **→ išnyksta 3 etape**

**K6. Modulio grąžinimas iš šiukšlinės nebegrąžina jo į puslapius.** `modules.php:1083-1084` — yra `trashed_post` ir `before_delete_post`, nėra `untrashed_post`. **→ išnyksta 4 etape**

**K7. Trūkstamas laukas ištrina visus modulio priskyrimus.** `modules.php:973-974` — nesant `g5_module_pages` traktuojama kaip „jokių puslapių", o ne „nekeisti". **→ išnyksta 4 etape**

**K8. Vertimai neatnaujinami pakeitus LT šaltinį.** Perskaičiuojama tik išsaugant patį modulį; pakeitus paslaugos ar komandos nario tekstą vertimas lieka pririštas prie senos formuluotės. **→ išnyksta 3 etape**

Kadangi K2–K5 ir K8 išnyksta perėjus į Polylang, o K1, K6, K7 — perėjus į sinchronizuotus šablonus, **0 etapo pataisos yra laikinos**. Jas vis tiek darome: iki 3 ir 4 etapų pabaigos praeis savaitės, o turinys prarandamas jau dabar.

### 4.2 Aukšti — funkcija egzistuoja, bet nepasiekiama

**A1. Titulinio hero nuotrauka nepasiekiama.** `settings.php:338` nurodo ją keisti redaguojant puslapį „Pagrindinis", o `modules.php:1171` kiekvieną to puslapio redagavimą nukreipia kitur — įskaitant administratorių. `homepage.php:164` tos nuotraukos vis tiek ieško. **→ išnyksta 1 etape**

**A2. „Apie mus" sekcijų tvarka neveikia.** `team.php:270-282` opciją skaito, `team.php:1110` ja rikiuoja, bet **niekas jos niekada nerašo**. Ekranas žada „Viršuje keiskite sekcijų tvarką". **→ išnyksta 2 etape**

**A3. Mokymų sekcijų tvarka be valdiklio.** `admin.php:348` tvarka užkoduota kietai. **→ išnyksta 2 etape**

**A4. Rolių aklavietės.** Turinio redaktorius, pasirinkęs „Karjera", nukreipiamas į ekraną su `edit_g5_jobs` reikalavimu ir gauna `wp_die`; personalo redaktorius — su „Mokymai". Kelio atgal nėra. „Puslapių sąrašai" meniu rodomas su `edit_g5_modules`, o atsidaro tik su `manage_g5tech_settings`. **Taisome 0 etape** (rolių aklavietė blokuoja darbą jau dabar).

**A5. „Darbo skelbimų valdymas" metadėžė niekada nematoma.** `jobs.php:127-134` ją registruoja, `jobs.php:161-166` ir `modules.php:1171` tą patį redaktorių nukreipia. **→ išnyksta 1 etape**

**A6. Personalo redaktorius gali ištrinti modulį iš puslapių, kurių valdyti negali.** `modules.php:1058-1067` tikrina teisę tik tam puslapiui, iš kurio paspausta, tada šalina iš visų. **Taisome 0 etape**

**A7. „Kur naudojamas" metadėžė apeina puslapių teises.** `modules.php:924-927` rodo visus 13 puslapių be filtro. **Taisome 0 etape**

**A8. Trys iš keturių redagavimo nukreipimų negyvi.** `modules.php:1171` suveikia pirmas, todėl `team.php:809`, `jobs.php:150` ir `admin.php:560` nepasiekiami — o `jobs.php` nukreipia į kitą vietą nei laimintysis. **→ išnyksta 1 etape**

### 4.3 Vidutiniai

**V1.** `modules.php:284-286` — sąraše nebuvę moduliai iššoka į puslapio priekį. **→ 4 etape**
**V2.** `module-layouts.php:560` — 39 modulių sukūrimas ant `admin_init` be teisių patikros; autoriumi tampa bet kuris pirmas užėjęs vartotojas. **Taisome 0 etape**
**V3.** Priskyrimų pakeitimai — „nuskaityk–pakeisk–įrašyk" ant vienos opcijos be užrakto. **→ 4 etape**
**V4.** `modules.php:1232-1234` — kiekvienai sąrašo eilutei 13 kartų perskaičiuojamas visas žemėlapis. **→ 4 etape**
**V5.** `repeaters.php:40-42` — dvigubas `wp_unslash` naikina pasvirąjį brūkšnį; „Ericsson \ Nokia" virsta „Ericsson  Nokia". **Taisome 0 etape**
**V6.** `i18n-admin.php:375` — vertimas, sutampantis su originalu, atmetamas; tikrinių vardų palikti negalima. **→ 3 etape**
**V7.** `i18n-admin.php:421-423` — daugiaeilis turinys poruojamas pagal eilutės numerį. **→ 3 etape**
**V8.** `i18n-admin.php:103` prieš `i18n.php:586` — administracija suspaudžia tarpus, priekinė dalis ne. **→ 3 etape**
**V9.** Vertimų raktų erdvė globali: vienas lietuviškas sakinys visoje svetainėje gali turėti tik vieną vertimą. **→ 3 etape**
**V10.** `i18n-admin.php:352, 372-376` — bet kas, galintis redaguoti bent vieną modulį, gali perrašyti bet kurį EN/DE tekstą svetainėje. Ne skriptų injekcija, bet turinio klastojimas. **→ 3 etape**
**V11.** `i18n.php:581, 605` — vertimas regex'ais be `<script>` ir `<style>` išimties. **→ 3 etape**
**V12.** `i18n.php:222, 415` — kiekvienas lietuviškas adresas gyvas ir po `/en/`, ir po `/de/`, be canonical. **→ 3 etape**
**V13.** Kalbų perjungiklis registruotas (`i18n.php:715`), bet niekur neįdėtas, o `inserter => false` neleidžia įdėti ranka. **→ 3 etape**
**V14.** `i18n.php:520` + `forms.php:72-79` — kalbos nukreipimas numeta užklausos parametrus; EN/DE lankytojai niekada nemato formos sėkmės pranešimo, dingsta UTM. **→ 3 etape**
**V15.** JSON-LD generuojamas vienu bloku ir niekada neverčiamas. **→ 3 etape**
**V16.** `i18n-admin.php:320` — kiekvienas išsaugojimas įrašo katalogo vertimą į DB, o DB turi pirmenybę; vėlesni `languages/*.php` pataisymai negalioja. **→ 3 etape**
**V17.** `i18n-admin.php:105` — vien iš skaičių ar simbolių sudarytos eilutės („6000+") neverčiamos. **→ 3 etape**
**V18.** Modulių tvarkos negalima keisti telefone: HTML5 tempimas nuo lietimo nesuveikia. **→ 1 etape** (blokų redaktorius veikia mobiliajame)

### 4.4 Žemi

`modules.php:1089` — kopijavimas netikrina puslapio teisių. `module-layouts.php:427-430` — nepatikrinta `getElementById()` grąža gali sukelti lemtingą klaidą viešame puslapyje. `module-layouts.php:403` — nesanitizuota meta reikšmė XPath užklausoje. `structured-content.php:357` — nesant lauko atstatoma numatytoji, o ne dabartinė reikšmė. `repeaters.php:112` — be `_id` išsaugotos eilutės kaskart gauna naują tapatybę. `modules.php:472-485` — ta pati užklausa vykdoma du kartus. `team.php:536` — `$view` parametras nenaudojamas. `i18n.php:176` — dokumentuotas `g5tech_i18n_routes` filtras iškviečiamas per anksti, prie jo prisikabinti neįmanoma. `i18n-admin.php:562-564` — ištrintų eilučių vertimai įrašomi atgal ir lieka amžinai.

---

## 5. Migracijos planas

Eiliškumo logika: pirma sustabdome duomenų praradimą, tada grąžiname WordPress galimybes (revizijos, peržiūra), tada tvarkome turinį, ir tik po to daugiakalbystę — nes Polylang reikalauja, kad turinys jau būtų įrašuose, ne opcijose.

Kiekvienas etapas baigiamas patikra visuose keturiuose pločiuose (1440 / 1050 / 720 / 390 px) ir gali būti priimamas atskirai.

### 0 etapas — sustabdyti duomenų praradimą — **skubu, apimtis S**

Taisome K1–K8, A4, A6, A7, V2, V5. Tai izoliuotos pataisos esamoje architektūroje, be pertvarkos. Dalis jų vėliau taps nereikalingos — tai priimtina kaina, nes turinys prarandamas jau dabar.

*Rezultatas:* nė vienas įprastas redaktoriaus veiksmas nebepraranda turinio; rolės nebeįstringa.

### 1 etapas — grąžinti WordPress redaktorių — **apimtis M**

Panaikinami visi `load-post.php` nukreipimai. Kiekvienas blokas gauna `block.json`, pavadinimą, ikoną ir peržiūrą redaktoriuje. Puslapių šablonai gauna `templateLock: "contentOnly"`. Įjungiamas `allowed_block_types_all` filtras ir atimamas atrakinimo mygtukas ne administratoriams. Patikrinama, kad temos stiliai matomi redaktoriaus iframe'e.

*Rezultatas:* puslapiai redaguojami natyviai, su revizijomis, peržiūra ir autosave; dizaino sugadinti neįmanoma. Išnyksta A1, A5, A8, V18.

### 2 etapas — turinys iš opcijų į įrašus — **apimtis L**

`g5tech_about_content`, `g5tech_career_page_content`, `g5tech_training_page_content` ir `g5tech_structured_content` turinys perkeliamas į atitinkamų puslapių `post_content` blokus ir post meta. Visi meta laukai registruojami per `register_post_meta()` su `show_in_rest`, `sanitize_callback`, `auth_callback` ir `revisions_enabled`. Struktūriniai laukai susiejami su blokais per Block Bindings. `g5tech_settings` lieka.

Migracija atliekama vienkartiniu WP-CLI skriptu, ne rankomis.

*Rezultatas:* kiekvienas puslapis turi savo turinį, revizijas ir peržiūrą. Išnyksta A2, A3. Atsiranda būtina Polylang sąlyga.

### 3 etapas — Polylang — **apimtis L**

Diegiamas Polylang, sukonfigūruojamos LT/EN/DE kalbos ir URL struktūra. Esami `en.php` / `de.php` katalogai importuojami kaip pradiniai vertimai (Polylang Pro XLIFF importas turiniui, eilučių importas sąsajai). Savas i18n sluoksnis (`i18n.php`, `i18n-admin.php`, vertimų JS) pašalinamas. Sutvarkomi 301 nukreipimai ir `hreflang`.

*Rezultatas:* išnyksta K2, K3, K4, K5, K8 ir V6–V17 — trylika radinių vienu etapu.

### 4 etapas — moduliai į sinchronizuotus šablonus — **apimtis M**

Pakartotinai naudojamos teksto sekcijos perkeliamos į sinchronizuotus šablonus. Katalogo valdomos sekcijos lieka dinaminiais blokais. `g5_module` CPT ir `g5tech_module_placements` opcija panaikinami.

*Rezultatas:* išnyksta K1, K6, K7, V1, V3, V4.

### 5 etapas — turinys iš kodo — **apimtis M**

Perkeliami 2.2 išvardyti kietai užkoduoti tekstai. Pradedama nuo teisinių puslapių — teisinis tekstas privalo keistis be deploy'aus. Toliau: DUK grupės, darbo skelbimų grupės, Akademijos ir persona puslapių hero/CTA, Kontaktų formos etiketės, Kontaktų kortelių parinkimas (vietoj pareigų teksto spėjimo — aiškus laukas).

*Rezultatas:* programuotojo nereikia jokiam teksto pakeitimui.

### 6 etapas — likę valdikliai, teisės, SEO — **apimtis S**

Savi kartotiniai elementai keičiami InnerBlocks arba SCF; dvi nuotraukų implementacijos sujungiamos; suvienodinami pavadinimai pagal 2.7; uždaromi 4.4 radiniai; patikrinamas sitemap, canonical ir struktūriniai duomenys visoms kalboms.

---

## 6. Priėmimo kriterijai

1. Puslapiai redaguojami įprastame WordPress redaktoriuje; savų ekranų, pakeičiančių puslapio redagavimą, nebėra.
2. Redaktorius negali pakeisti struktūros, spalvų, šriftų ar tarpų, bet gali keisti visą tekstą ir mediją.
3. Kiekvienas puslapis turi veikiančias revizijas, peržiūrą prieš publikavimą ir autosave.
4. Kiekvienas svetainės tekstas priklauso vienam iš keturių sluoksnių; PHP failuose viešo teksto nelieka.
5. Visi meta laukai registruoti per `register_post_meta()` su sanitizacija ir teisių patikra.
6. LT, EN ir DE yra atskiri įrašai su savais adresais, revizijomis ir `hreflang`.
7. Pataisius lietuvišką tekstą, vertimas nenutrūksta.
8. Nė vienas redaktoriaus veiksmas nepraranda turinio; visi 4.1 radiniai uždaryti.
9. Jokia matoma nuoroda neveda į „neturite teisės".
10. Svetainė lieka veikianti po WordPress atnaujinimo staging aplinkoje — ir tam patikrinti reikia mažiau savo kodo nei anksčiau.
11. Turinio ir Personalo redaktoriai savarankiškai atlieka po penkias tipines užduotis be pagalbos.

---

## 7. Kontrolinis sąrašas naujam puslapiui

1. **Ar WordPress tai jau turi?** Prieš rašydamas naują ekraną, lauką ar mechanizmą — patikrink branduolyje.
2. **Ar puslapiui reikia naujų tekstų?** Jie eina į `post_content` blokus arba post meta. Ne į PHP, ne į opciją.
3. **Ar sekcija realiai kartosis kitame puslapyje?** Tik tada ji tampa sinchronizuotu šablonu.
4. **Ar puslapis rodo katalogo įrašus?** Tada jis juos tik atrenka; tekstų nekopijuoja.
5. **Ar naudoji faktą iš 2 sluoksnio?** Imk iš Nustatymų, neperrašinėk.
6. **Ar šablonas užrakintas `contentOnly`?**
7. **Ar kiekvienas naujas blokas turi `block.json`, pavadinimą, ikoną ir peržiūrą?**
8. **Ar meta laukai registruoti su `show_in_rest`, `sanitize_callback`, `auth_callback`, `revisions_enabled`?**
9. **Ar puslapis turi LT, EN ir DE versijas Polylang'e?**
10. **Ar teisė meniu punktui sutampa su teise ekranui?**
11. **Ar puslapis įtrauktas į 301 žemėlapį ir sitemap?**

---

## Priedas A: metodika

Auditas atliktas statine kodo analize be veikiančios aplinkos. Peržiūrėta: `5gtech-core` 0.12.0 (23 failai, ~330 KB PHP), temos `functions.php`, administracijos JavaScript ir gyva SQLite duomenų bazė. Radiniai gauti dviem nepriklausomomis peržiūromis; kritiniai ir aukšti patikrinti trečią kartą rankiniu būdu. Pirmos peržiūros metu dalis išvadų padaryta iš pasenusios failų kopijos ir po patikrinimo atmesta — čia jų nėra.

Tikslinė architektūra suderinta su oficialia WordPress dokumentacija ir WP 7.0 („Armstrong", 2026-05-20) galimybėmis.

## Priedas B: šaltiniai

- [Curating the Editor Experience — Block Editor Handbook](https://developer.wordpress.org/block-editor/how-to-guides/curating-the-editor-experience/)
- [Block Locking API — `templateLock`, `contentOnly`](https://developer.wordpress.org/block-editor/how-to-guides/curating-the-editor-experience/block-locking/)
- [Content-only editing and other locking updates — Make WordPress Core](https://make.wordpress.org/core/2022/10/11/content-locking-features-and-updates/)
- [Framework for storing revisions of Post Meta in 6.4 — Make WordPress Core](https://make.wordpress.org/core/2023/10/24/framework-for-storing-revisions-of-post-meta-in-6-4/)
- [Managing Post Metadata — Plugin Handbook](https://developer.wordpress.org/plugins/metadata/managing-post-metadata/)
- [Block Bindings API — 10up Block Editor Best Practices](https://gutenberg.10up.com/reference/Patterns/block-bindings-api/)
- [WordPress 7.0 — WP Engine apžvalga](https://wpengine.com/blog/wordpress-7-0-release/)
- [WordPress 7.0 Features: The Ultimate Guide for Developers](https://instawp.com/wordpress-7-0/)
- [Keeping your WordPress options table in check — 10up](https://10up.com/blog/2017/wp-options-table/)
- [Secure Custom Fields — WordPress.org](https://wordpress.org/plugins/secure-custom-fields/)
- [SCF vs ACF: kodėl WordPress atskyrė ACF](https://www.creolestudios.com/scf-vs-acf-why-wordpress-forked-advanced-custom-fields/)
- [Polylang: XLIFF eksportas ir importas](https://polylang.pro/documentation/support/guides/how-to-export-and-import-content-translations-with-xliff-in-polylang-pro/)
- [Polylang: eilučių vertimų importas ir eksportas](https://polylang.pro/documentation/support/guides/import-and-export-strings-translations/)

## Priedas C: susiję projekto dokumentai

`WORDPRESS-INTEGRACIJOS-PLANAS.md` (bendras planas — jo §3.3, §10.3 ir §17 numatė būtent tai, ką dabar grąžiname), `WORDPRESS-MODULIU-VALDYMAS.md` ir `DAUGIAKALBES-SVETAINES-VALDYMAS.md` (aprašo esamą sprendimą; po 3 ir 4 etapų juos reikės perrašyti), `WORDPRESS-INTEGRACIJOS-AUDITAS-2026-07-29.md` (ankstesnis auditas), `WORDPRESS-DARBU-PAKETAI.md`, `WORDPRESS-ADMINISTRAVIMO-ATMINTINE.md` (perrašytina po 1 etapo).
