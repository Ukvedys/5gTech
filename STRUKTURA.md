# 5G TECH — naujos svetainės meniu ir turinio struktūra

**Data:** 2026-07-21
**Pagrindimo šaltiniai:** surinktas esamos svetainės turinys (`turinys/esama-svetaine/`) ir jūsų anketos atsakymai (`Tikslinis klientas.docx`). Prie kiekvieno sprendimo nurodyta **kodėl** taip siūlome.

---

## 1. Pagrindiniai struktūros principai

**Ką sakė anketa (svetainės tikslai):**

1. Lankytojas turi suprasti **visą paslaugų spektrą** — „nenorime, kad mus vertintų tik kaip mobilaus ryšio įrangos montuotojus".
2. Lankytojas turi pamatyti **patirtį** — atliktų darbų skaičius, operatoriai, geografija, įranga (B2B dalis).
3. Lankytojas turi lengvai **pateikti užklausą** dėl bendradarbiavimo.
4. Kandidatai turi lengvai **aplikuoti į darbo pozicijas**.
5. „Išlaikyti norėtume viską, kas yra. Pateikimas tik nepriimtinas."

**Iš to išplaukia struktūros logika:** svetainė aptarnauja **dvi skirtingas auditorijas** — B2B klientus (užsakovai, gen. rangovai, operatoriai) ir kandidatus į darbą. Dabar jos sumaišytos viename meniu (9 punktai vienoje eilėje: Paslaugos šalia DUK apie dienpinigius). Naujoje struktūroje nieko neišmetame, bet **sugrupuojame pagal auditoriją**: viršutinis meniu — klientams, „Karjera" šaka — kandidatams.

---

## 2. Siūlomas pagrindinis meniu

```
┌─ PAGRINDINIS  (/)
├─ PASLAUGOS  (/paslaugos/)
│   ├─ Mobiliojo ryšio tinklai      (/paslaugos/mobiliojo-rysio-tinklai/)
│   ├─ Vidinio ryšio tinklai        (/paslaugos/vidinio-rysio-tinklai/)
│   ├─ Fiksuoto ryšio tinklai       (/paslaugos/fiksuoto-rysio-tinklai/)
│   ├─ Elektros darbai              (/paslaugos/elektros-darbai/)
│   ├─ Saulės elektrinės            (/paslaugos/saules-elektrines/)
│   └─ Apsaugos sistemos            (/paslaugos/apsaugos-sistemos/)
├─ PATIRTIS  (/patirtis/)          ← NAUJAS puslapis
├─ APIE MUS  (/apie-mus/)
│   ├─ Rinkis mus                   (/rinkis-mus/)
│   └─ Naujienos                    (/naujienos/)   ← NAUJA skiltis
├─ KARJERA  (/karjera/)
│   ├─ Darbo pozicijos              (/karjera/#pozicijos → atskiri įrašai /karjera/pozicija/...)
│   ├─ 5GTECH Academy               (/akademija/)
│   ├─ Mokymai                      (/mokymai/)
│   └─ DUK kandidatams              (/duk/)
└─ KONTAKTAI  (/kontaktai/)        ← išskirtas kaip CTA mygtukas dešinėje
```

**Footer:** rekvizitai, greitosios nuorodos, sertifikatų ženkliukai, elgesio kodekso PDF, privatumo politika, slapukų nuostatos, kalbų perjungiklis.

**Kodėl 6 punktai vietoje dabartinių 9:**

- Dabartiniame meniu 9 lygiaverčiai punktai — lankytojas turi pats atspėti, kad „Mokymai", „Academy" ir pusė DUK skirti kandidatams, o ne jam. Anketoje sprendimų priėmėjais įvardijote įmonių vadovus ir projektų vadovus — jie svetainėje praleis 30–60 sekundžių; per tą laiką turi rasti paslaugas, patirtį ir užklausos formą, nesiblaškydami po karjeros turinį.
- „Kontaktai" kaip išskirtas mygtukas (ne eilinis punktas) — nes anketoje užklausos pateikimas įvardytas kaip pagrindinis norimas veiksmas. Toks pat sprendimas naudojamas jūsų nurodytame pavyzdyje lineworksinc.com („Contact us" mygtukas dešinėje).
- Nieko neišmetame — visi dabartiniai puslapiai lieka (jūsų reikalavimas „išlaikyti viską"), keičiasi tik grupavimas ir pateikimas.

---

## 3. Puslapiai po vieną: kas juose bus ir kodėl

### 3.1. Pagrindinis `/`

**Sekcijos (eilės tvarka):**

1. **Hero** — aiškus vertės teiginys + 2 CTA („Mūsų paslaugos", „Susisiekti"). Siūlome akcentuoti „techninis partneris nuo telekomunikacijų iki energetikos" vietoje dabartinio „Inžinerinių tinklų ir ryšio stočių projektavimas, statyba ir priežiūra".
   *Kodėl:* anketoje pozicionavimą apibrėžėte kaip „techninis partneris su galimybe suteikti pilno spektro paslaugas" — dabartinė hero antraštė kalba tik apie ryšio stotis, t. y. daro būtent tai, ko nenorite (rėmuoja jus kaip vien montuotojus).
2. **Skaičių juosta** — 6000+ bazinių stočių, 4+ veiklos šalys, nuo 2020 m., 17 atvirų pozicijų (dinamiškai).
   *Kodėl:* anketoje „pamatyti bendrovės patirtį — atliktų darbų skaičius" įvardyta kaip antras svetainės tikslas; dabar šie skaičiai paslėpti „Rinkis mus" puslapio apačioje. Skaičiai per pirmus 5 sekundžių ekranus — standartinis B2B pasitikėjimo elementas.
3. **Bėganti logotipų juosta** — operatoriai (Telia, Tele2, Vodafone, Deutsche Telekom, Telefónica/O2, Orange, Telenor, 1&1, N4M, Hi3G, 450 connect...) ir įrangos gamintojai (Ericsson, Nokia, Huawei, Delta, Eltek...).
   *Kodėl:* tai jūsų tiesioginis prašymas — lineworksinc.com pavyzdyje jums patiko būtent „bėganti eilutė įrangos gamintojų, operatorių". Esamoje svetainėje šie vardai tik DUK tekste — naujoje jie tampa vizualiu pasitikėjimo įrodymu.
4. **Paslaugų tinklelis (6 kortelės)** — su nuorodomis į atskirus paslaugų puslapius.
   *Kodėl:* visas spektras matomas iš karto pagrindiniame — sprendžia „ne vien montuotojai" problemą.
5. **Kabliukų blokas** (puslapio viduryje) — trys faktų kortelės, kurių kiekviena veda į savo persona puslapį, bet **niekur neklausiame, kas lankytojas yra** — atranką padaro pats faktas:

   | Kabliukas (kortelės antraštė) | Potekstė | Nuoroda |
   |---|---|---|
   | „6000+ bazinių stočių. Atsakomybė, patvirtinta ISO ir draudimu." | rizika, patikimumas | „Kodėl mumis pasitiki operatoriai →" /vadovams/ |
   | „Ericsson, Nokia, Huawei — su dokumentacija pagal jūsų standartus." | techninė virtuvė | „Kaip vykdome projektus →" /projektu-vadovams/ |
   | „2 500–3 600 € į rankas Europos projektuose." | karjera | „Atviros pozicijos →" /karjera/ |

   *Kodėl taip, o ne „pasirinkite, kas esate":* lankytojo neverčiame deklaruoti savo vaidmens — jį prisitraukia jam aktualiausias faktas (ledų baro principas: vaikui nerašome „vaikams" — jis pats pamato ledus ir eina). Vadovas užkibs už atsakomybės ir skaičių, inžinierius — už įrangos ir dokumentacijos, kandidatas — už atlyginimo. Kabliukai paimti iš jūsų anketos: kiekvienai personai — jos svarbiausias kriterijus. Detaliau — 3.12 skyriuje.
6. **Patirties žemėlapis / teaser** — kur dirbame (LT, SE, NO, DE + plėtra), nuoroda į /patirtis/.
7. **Sertifikatai** — ISO 9001, 14001, 45001, SSVA, VERT (kai gausite).
   *Kodėl:* anketoje sertifikatai įvardyti tarp svarbiausių kriterijų renkantis partnerį ir kaip atsakymas į dažniausią kliento abejonę (patirtis, kompetencija). Dabar jie yra tik paveiksliukas šone.
8. **Naujienų teaser** (3 naujausios) — *kodėl:* anketoje patvirtinote, kad naujienas kelsite reguliariai; teaser'is pagrindiniame rodo, kad įmonė gyva.
9. **CTA juosta** — „Aptarkime jūsų projektą" → forma.

### 3.2. Paslaugos `/paslaugos/` + 6 atskiri puslapiai

**Kas keičiasi:** dabar visos 6 paslaugos yra viename ilgame puslapyje su inkarais (#mob-tinklai, #elektra...). Naujoje svetainėje — apžvalginis puslapis + atskiras puslapis kiekvienai paslaugai. Tekstų pagrindas jau surinktas (02-paslaugos.md) — juos perrašysime, papildysime.

**Kodėl skaidome:**

- Anketoje sakėte, kad paslaugų suskirstymas aktualus ir norite būti matomi kaip pilno spektro partneris — atskiri puslapiai leidžia kiekvienai krypčiai (saulės elektrinės, silpnos srovės...) gyventi savarankiškai ir būti randamai Google atskirai („saulės elektrinių montavimas rangovams" niekada neatves į puslapį, kurio pavadinimas — „ryšio stotys").
- Kiekviename paslaugos puslapyje: aprašymas, paslaugų sąrašas (jau turime), susijusi patirtis/įranga, DUK klientams apie tą paslaugą, CTA forma. Struktūra „nuo planavimo iki priežiūros" — nes anketoje minėjote, kad klientams svarbi galimybė įgyvendinti projektą pilnu ciklu.

### 3.3. Patirtis `/patirtis/` — NAUJAS

**Sekcijos:** skaičiai (6000+ stočių 2G–5G), geografijos žemėlapis, operatorių logotipai pagal šalis, įrangos gamintojų sąrašas, tipinių projektų aprašymai (be konfidencialių detalių), sertifikatai.

**Kodėl šis puslapis būtinas:**

- Tai tiesiogiai anketos tikslas №2: „Pamatyti bendrovės patirtį – atliktų darbų skaičius, operatoriai, geografija kur esame dirbę, įrangą su kuria dirbę esame (B2B dalis)."
- Esamoje svetainėje šios informacijos **puslapio nėra iš viso** — faktai išbarstyti po „Rinkis mus" apačią ir DUK atsakymus. Tuo tarpu jūsų klientų dažniausia abejonė (anketos žodžiais) — „patirtis, techninė kompetencija". Puslapis, kurį projektų vadovas gali nusiųsti savo vadovui kaip argumentą — stipriausias pardavimo įrankis svetainėje.
- Techniškai: projektai bus atskiras turinio tipas (CPT `projektas`), tad ateityje galėsite pildyti naujus objektus be programuotojo.

### 3.4. Apie mus `/apie-mus/`

**Lieka:** įmonės aprašymas, komanda su kontaktais, vertybės, strategija 2025–2028, misija/vizija (visas turinys jau surinktas 03-apie-mus.md).
**Kodėl beveik nekeičiame:** turinys geras ir šviežias (strategija 2025–2028), atitinka anketos akcentus (atsakomybė, patikimumas). Keičiasi tik pateikimas — vertybės ir strategija dabar ilgi tekstiniai blokai, naujoje versijoje taps vizualiai skenuojamomis kortelėmis.

### 3.5. Rinkis mus `/rinkis-mus/` (po „Apie mus" šaka)

**Lieka atskiru puslapiu** su vertybėmis, partnerystės filosofija, elgesio kodeksu ir faktais.
**Kodėl:** anketoje į klausimą „kuo skiriasi jūsų darbo metodas" atsakėte nuoroda būtent į šį puslapį — vadinasi, jį laikote savo diferenciacijos pagrindu. Be to, reikalavote išlaikyti visus puslapius. Meniu jį perkeliame po „Apie mus", nes abu atsako į tą patį lankytojo klausimą („kas jie tokie?") ir 9 punktų meniu tampame 6.

### 3.6. Naujienos `/naujienos/` — NAUJA

**Kodėl:** anketoje atsakėte „Taip" į klausimą apie reguliarų naujienų/projektų kėlimą, bet esamoje svetainėje naujienų skilties **nėra** — nėra kur jų dėti. Naudosime standartinius WordPress įrašus (jokių papildomų įskiepių nereikia). Naujienos taip pat maitins pagrindinio puslapio teaser'į ir duos šviežio turinio Google indeksavimui — svarbu EN/DE rinkoms.

### 3.7. Karjera `/karjera/` + pozicijų įrašai

**Struktūra:** įvadas („Atskleisk savo potencialą") → atvirų pozicijų sąrašas (filtrai: Lietuva / Europa / Biuras) → atrankos procesas (5 žingsniai) → Grow2Go modelis → sauga ir aprūpinimas (Singing Rock, Skylotec, Petzl) → aplikavimo forma.

**Kas keičiasi ir kodėl:**

- **Kiekviena pozicija — atskiras įrašas** (CPT `darbo_pozicija`), ne 17 pastraipų viename puslapyje kaip dabar. Priežastys: (a) anketos tikslas №4 — „aplikuoti į darbo pozicijas" — kandidatas turi galėti pasidalinti konkrečios pozicijos nuoroda; (b) personalo koordinatorė galės pati įjungti/išjungti pozicijas per WP administravimą; (c) atskiri pozicijų puslapiai gali būti indeksuojami Google for Jobs.
- Karjeros forma (visi 15 laukų jau užfiksuoti 06-karjeros-forma.md) integruojama į pozicijos puslapį + lieka bendra forma kandidatų bazei — dabartinė logika „palik CV ateičiai" gera, ją išlaikome.
- **Privalomas GDPR sutikimo laukas** formoje — dabar formos renka CV be privatumo politikos (jos svetainėje apskritai nėra — 404).

### 3.8. 5GTECH Academy `/akademija/` (po „Karjera" šaka)

**Lieka** visa 5 etapų programa, sąlygos (2 900 € į rankas, apgyvendinimas, AAP) — turinys jau surinktas (07-5gtech-academy.md).
**Kodėl po Karjera:** Academy yra įdarbinimo kanalas — jos auditorija ta pati kaip Karjeros. URL trumpiname iš `/5gtech-telecom-academy/` į `/akademija/` (301 nukreipimas iš seno adreso; senas URL — 26 simbolių anglų-lietuvių mišinys).

### 3.9. Mokymai `/mokymai/` (po „Karjera" šaka)

**Lieka:** mokymų salės aprašymas, temos, įranga (3Z-RFVision, SiteMaster, Sonel), video.
**Ką REIKĖS sukurti naujo:** esamame puslapyje nėra nei kursų sąrašo, nei trukmių, nei kainų, nei registracijos būdo (užfiksuota 08-mokymai.md). Jei mokymai teikiami ir išorės klientams — tai atskira paslauga ir jai reikės pilno turinio; jei tik saviems darbuotojams — paliekame kaip įmonės infrastruktūros pristatymą po Karjera. **Klausimas jums.**

### 3.10. DUK `/duk/` (po „Karjera" šaka) + DUK klientams

**Kas keičiasi:** surinkus visą DUK (37 klausimai, 09-duk.md) matyti, kad ~30 iš 37 klausimų skirti **kandidatams** (dienpinigiai, apgyvendinimas, komandiruotės, darbo rūbai), o ne klientams. Todėl:

- `/duk/` lieka kandidatų DUK ir meniu keliauja po „Karjera" (jo tikroji auditorija).
- Techniniai klausimai (kas yra bazinė stotis, 2G–5G, sauga, gamintojai, operatoriai) — akordeonais atitinkamuose paslaugų puslapiuose.
- **Naujas B2B DUK blokas** (kurti reikės naujo turinio): garantijos, terminai, dokumentacija, subrangos modelis, draudimai, kaip vyksta projektų perdavimas.
  *Kodėl:* anketoje nurodėte, kad klientui prieš sprendimą kyla abejonės dėl patirties ir kompetencijos, o svarbiausi kriterijai — atsakomybė ir greita reakcija; klientinis DUK į tai atsako, dabar tokio turinio nėra visai.

### 3.11. Kontaktai `/kontaktai/` (CTA mygtukas meniu)

**Lieka:** rekvizitai, komandos kontaktai, forma „Parašykite mums" (10-kontaktai.md). **Pridedame:** žemėlapį (Meistrų g. 8A), GDPR sutikimą, temos pasirinkimą formoje („Užklausa dėl bendradarbiavimo" / „Karjera" / „Kita") — kad užklausos iškart keliautų teisingam žmogui (anketoje: sprendimus priima vadovas ir projektų vadovai — jų laikas brangus).

### 3.12. Persona puslapiai (landingai) — NAUJA

Du trumpi puslapiai, kurių kiekvienas sudėlioja argumentus pagal konkretų sprendimo priėmėją. Tai ne turinio kopijos, o **argumentų maršrutai**: trumpas, personai pritaikytas tekstas + nuorodos į Patirtį, Paslaugas, sertifikatus + personai pritaikytas CTA.

**Kodėl iš viso:** anketoje į klausimą „kas priima sprendimą dėl jūsų paslaugų" atsakėte — **įmonės vadovas, projektų vadovas, projektų vykdytojai**. Šie žmonės į tą pačią svetainę žiūri skirtingomis akimis: vadovas vertina riziką ir atsakomybę, projektų vadovas — techninę kompetenciją ir reakcijos greitį. Vienas bendras tekstas visiems reiškia, kad nė vienas negauna tiksliai savo argumentų. Persona puslapiai taip pat idealūs kaip nukreipimo taškai LinkedIn/el. pašto kampanijoms į konkrečias pareigybes.

**Įėjimo principas — faktas, ne etiketė:** į persona puslapius lankytojas patenka ne pasirinkęs „kas aš esu", o užkibęs už jam aktualiausio fakto (žr. pagrindinio puslapio kabliukų bloką, 3.1 §5). Pačiuose puslapiuose antraštės taip pat formuluojamos per vertę, ne per etiketę — ne „Informacija CEO", o pvz. „Partneris, dėl kurio nereikia aiškintis užsakovui" (/vadovams/) ir „Techninė virtuvė: įranga, procesai, dokumentacija" (/projektu-vadovams/). Paantraštėje galima patikslinti auditoriją („Informacija įmonių vadovams") — bet tai patvirtinimas jau užkibusiam, ne filtras prie durų.

#### /vadovams/ — „Įmonės vadovui"

Argumentų eilė (pagal anketos kriterijus: patikimumas, atsakomybė, sertifikatai):

1. **Rizikos valdymas** — ISO 9001/14001/45001, SSVA atestatas, VERT (kai bus), draudimai, saugos politika („nulinė tolerancija").
2. **Įrodyta patirtis** — 6000+ stočių, 4 šalys, operatorių sąrašas (Telia, Vodafone, DT...): partneris, kuris nesugadins jūsų santykio su užsakovu.
3. **Vienas partneris — pilnas spektras** — nuo telekomunikacijų iki saulės energetikos ir elektros darbų: mažiau rangovų, mažiau sąsajų rizikos.
4. **Atsakomybė ir terminai** — anketoje kliento pagrindinė problema: „montavimo terminai, kokybė".
5. CTA: tiesioginis vadovybės kontaktas + „Susitarkime dėl pokalbio".

#### /projektu-vadovams/ — „Projektų vadovui ir techniniam personalui"

Argumentų eilė (pagal anketos kriterijus: techninė kompetencija, patirtis, greita reakcija):

1. **Įranga ir gamintojai** — Ericsson, Nokia, Huawei; maitinimo/infrastruktūros įranga (Delta, Eltek, FIAMM, Enersys, AirSite, Dantherm, ZTE).
2. **Procesai ir dokumentacija** — Site Surveys, SiteMaster testavimas, MW linijų konfigūravimas, dokumentacija pagal užsakovo standartus (paslaugų tekstai tą jau mini — čia iškeliame į pirmą planą).
3. **Komandos kvalifikacija** — aukštalipių, elektrosaugos, pirmosios pagalbos sertifikatai; saugos įranga Singing Rock / Skylotec / Petzl.
4. **Greita reakcija ir lankstumas** — mobili komanda, darbas keliose šalyse, 6–8 sav. rotacijos modelis.
5. CTA: projektų vadovo tiesioginis kontaktas + užklausa su galimybe prisegti techninę užduotį.

#### Kandidatams → /karjera/

Trečiai personai atskiro landingo nekuriame — **Karjeros puslapis jau yra jos landingas** (pozicijos, atlyginimai, Grow2Go, sąlygos). Pagrindinio puslapio persona bloke kortelė „Ieškau darbo" veda tiesiai ten.

**Kur personos pasiekiamos:** pagrindinio puslapio viduryje (kabliukų blokas), footer'yje ir kampanijų nuorodose. Į pagrindinį meniu jų nededame — meniu liktų 6 punktų, o personos yra maršrutai, ne skiltys.

**Apimties kontrolė:** +2 nauji puslapiai × 3 kalbos = 6 vertimo vienetai. Puslapiai trumpi (4–5 argumentų blokai + CTA), turinys nedubliuojamas — tik formuluotės personai ir nuorodos į esamus puslapius.

### 3.13. Nauji privalomi puslapiai (footer)

| Puslapis | Kodėl |
|---|---|
| Privatumo politika `/privatumo-politika/` | Dabar — 404, nors formos renka CV ir asmens duomenis. GDPR reikalavimas, ne pasirinkimas. |
| Slapukų politika + juosta | Svetainė naudos analitiką; ES reikalavimas. |
| 404 puslapis | Su nuorodomis į paslaugas ir kontaktus — dabar standartinis. |

---

## 4. Turinio tipai (kas bus valdoma per WordPress administravimą)

| Turinio tipas | Kam | Kodėl atskiras tipas |
|---|---|---|
| Puslapiai (standart.) | Pagrindinis, Apie mus, Rinkis mus, Kontaktai... | Statiški, keičiami retai |
| Įrašai (standart.) | Naujienos | Anketoje patvirtintas reguliarus pildymas |
| `paslauga` | 6 paslaugų puslapiai | Vienoda struktūra, galima pridėti 7-ą paslaugą be programuotojo |
| `projektas` | Patirties įrašai | B2B tikslas №2; pildysite patys po kiekvieno objekto |
| `darbo_pozicija` | 17+ pozicijų | Personalo koordinatorė valdo pati; pozicijos nuolat kinta |
| `duk` | DUK įrašai (kategorijos: kandidatams / klientams) | Vienas šaltinis, rodomas skirtinguose puslapiuose |

Visi tipai registruojami mūsų `5gtech-core` papildinyje (ne temoje) — pakeitus dizainą turinys išlieka. WordPress branduolys nelieciamas.

---

## 5. Kalbos ir URL

- LT (pagrindinė): `5gtech.lt/paslaugos/...`
- EN: `5gtech.lt/en/services/...`
- DE: `5gtech.lt/de/leistungen/...`

Turinys 1:1 visomis kalbomis (jūsų anketos atsakymas). Meniu, formos, DUK — verčiama viskas. Naujienos: jei naujiena aktuali tik LT rinkai, Polylang leidžia palikti ją tik LT — lankstumas ateičiai.

**Kodėl EN ir DE būtinos pagal anketą:** tikslinės rinkos — gen. rangovai Vokietijoje, Švedijoje, Norvegijoje, Danijoje, Suomijoje, Belgijoje, Olandijoje. EN dengia Skandinaviją ir Beneliuksą, DE — Vokietiją (didžiausia įvardyta rinka, ten dirba ir jūsų saulės elektrinių kryptis).

---

## 6. 301 nukreipimų žemėlapis (SEO išsaugojimui)

| Senas URL | Naujas URL |
|---|---|
| /paslaugos/#mob-tinklai | /paslaugos/mobiliojo-rysio-tinklai/ |
| /paslaugos/#tinklai | /paslaugos/vidinio-rysio-tinklai/ |
| /paslaugos/#fiksuoti-tinklai | /paslaugos/fiksuoto-rysio-tinklai/ |
| /paslaugos/#elektra | /paslaugos/elektros-darbai/ |
| /paslaugos/#elektrines | /paslaugos/saules-elektrines/ |
| /paslaugos/#apsaugos | /paslaugos/apsaugos-sistemos/ |
| /5gtech-telecom-academy/ | /akademija/ |
| /karjeros-forma/ | /karjera/ (forma puslapyje) |
| Kiti (/, /paslaugos/, /apie-mus/, /rinkis-mus/, /karjera/, /mokymai/, /duk/, /kontaktai/) | Nesikeičia — 301 nereikia |

Pastaba: #inkarai techniškai nereikalauja 301 (Google indeksuoja tik /paslaugos/), bet vidinis peradresavimas išsaugos senų nuorodų patogumą.

---

## 7. Naujo turinio poreikis (ką reikės sukurti, ko sena svetainė neturi)

1. **Patirties puslapio turinys** — projektų aprašymai, skaičiai pagal šalis (žaliava: 6000+ stočių, operatorių sąrašas — jau turime; reikės jūsų patvirtinimo ir 3–5 tipinių projektų aprašymų).
2. **B2B DUK** (~10 klausimų klientams).
3. **Hero ir pozicionavimo tekstai** — „techninis partneris" kryptimi.
4. **Mokymų kursų detalės** — jei mokymai bus siūlomi išorei.
5. **Privatumo ir slapukų politikos** tekstai.
6. **Persona puslapių tekstai** — /vadovams/ ir /projektu-vadovams/ (trumpi, 4–5 argumentų blokai pagal 3.12 skyrių) + kabliukų bloko kortelės pagrindiniame (faktas → nuoroda, be „pasirinkite, kas esate").
7. **EN ir DE vertimai** — viso aukščiau išvardyto.
8. **Nuotraukos:** turime „KAIPGERAI_5GTECH" fotosesiją (mokymų salė + keli darbų kadrai) — jos dengia Karjerą/Mokymus/Apie mus. Trūksta: objektų (bokštai, stotys), saulės elektrinių ir biuro/komandos nuotraukų Paslaugų ir Patirties puslapiams.

---

## 8. Kitas žingsnis

Patvirtinus šią struktūrą: 2 etapas (Local aplinka jūsų Mac'e) → temos skeletas su šia meniu struktūra ir demo turiniu → tada naujo turinio rašymas pagal 7 skyriaus sąrašą.

**Laukiantys jūsų sprendimai:**

1. Ar „Mokymai" siūlomi išorės klientams, ar tik vidinei komandai? (lemia puslapio vietą ir turinio apimtį)
2. Ar tvirtinate „Rinkis mus" ir „Naujienos" perkėlimą po „Apie mus", o Academy/Mokymai/DUK — po „Karjera"?
3. Ar galite patvirtinti 3–5 projektus/objektus, kuriuos galima viešai aprašyti Patirties puslapyje (šalis, operatorius, darbų tipas — be konfidencialių detalių)?
