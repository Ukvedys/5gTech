# „5G TECH“ – pataisos po perdavimo audito

Data: 2026-09-07. Pataisyta vietinė „WordPress“ svetainė ir diegimo kodas. **Į GitHub ir kliento serverį šio darbo metu niekas neišsiųsta.**

## Rezultatas

Vietinės techninės pataisos įgyvendintos ir patikrintos. Projektą galima ruošti bandomajam perkėlimui į staging. Kaip galutinai priimtą kliento svetainę žymėti tik po tikro serverio, pašto ir atkūrimo patikrų.

Produkcijos pakeitimai atlikti „WordPress“ temoje, „5gtech-core“ ir valdomo turinio kopijoje. Statiniai `dizainas/maketai` failai nekeisti.

## Kas pataisyta

1. **Importo saugumas.** `sync-content.php` ir jo pagalbinis failas HTTP režimu grąžina 403 dar prieš „WordPress“ įkėlimą. Eksportuotojas taip pat skirtas tik CLI. Diegimo kopija ir scenarijai keliami už viešo katalogo; senas viešas `g5-deploy` numatytas perkelti į privačią atkuriamą kopiją.
2. **Kliento turinio apsauga.** Pašalinta `SYNC-ON` vėliavėlė. Įprasti kodo diegimai nebevykdo importo. Vienkartinis importas pasirenkamas atskirai ir nešalina kopijoje nenurodytų kliento įrašų. Sutampančius įrašus ir valdomus nustatymus jis vis dar perrašo – tai sąmoninga importo paskirtis.
3. **Perkėlimo ryšiai.** Kopija atnaujinta į schemą 2, pridėti šaltinio įrašų ID. Dviejų etapų importas persieja blokų vaizdus, partnerius, komandos operatorius, paslaugų ryšius, modulio nuorodas ir nustatymuose saugomus ID. Trūkstamos priklausomybės ir neišspręstos nuorodos sustabdo importą.
4. **Patikimesnis diegimas.** Importui būtini aktyvūs papildiniai ir tema. Kritinės klaidos grąžina nesėkmę; vykstantis diegimas nebeatšaukiamas dėl naujo paleidimo. Pridėta po diegimo atliekama aktyvios temos, papildinių ir pagrindinių HTTP adresų patikra. DB kopijų prieigos teisės apribotos.
5. **Turinio kopijos suderinimas.** Išsaugota naujesnė repozitorijos turinio ir medijos versija, ne aklai eksportuota senesnė vietinė DB. Prieš vietinį importą padaryta DB kopija. Vietinėje svetainėje pritaikyti visi 199 kopijos įrašai.
6. **EN / DE puslapių struktūra.** 12 pasenusių vieno bloko puslapių apvalkalų pakeisti dabartine „WordPress“ blokų struktūra, naudojant esamą vertimų sluoksnį. Tarp jų – kontaktai ir kandidatavimas: jų formos dabar iš tikrųjų atvaizduojamos.
7. **Formų kalba.** Kontaktų ir kandidatavimo formos perduoda pasirinktą kalbą; grįžimo adresas gaunamas iš „Polylang“. Sėkmei ir klaidoms naudojama ta pati pataisyta funkcija.
8. **Tiesioginiai kontaktai.** EN ir DE darbuotojų kortelių atranka nebepriklauso nuo išversto pareigų pavadinimo; rodomi visi trys numatyti kontaktai.
9. **Mobilus titulinis.** Skaidrių valdikliai perkelti į įprastą turinio tėkmę, todėl nebeužlipa ant CTA. Burgeris ir kalbos patikrinti tituliniame bei vidiniame puslapyje.
10. **Kalbų smulkmenos.** Ištaisyti nepublikuojamų profilių / projektų bei neaktyvių pozicijų grįžimo adresai, išlaikant kalbą. Lokalizuota mobiliojo meniu antraštė ir profilio nuorodos pavadinimas. EN / DE naujienose sutvarkytos `Uncategorized` kategorijos.
11. **Testai ir dokumentacija.** Kalbų HTTP testas pritaikytas dabartiniam „Polylang“. Pridėtas atskiroje DB vykdomas blokų redaktoriaus išsaugojimo testas. Perrašyta `DIEGIMAS.md`, kad instrukcija nebesiūlytų viešo ar automatinio turinio perrašymo.

## Patikros

| Patikra | Rezultatas |
| --- | --- |
| Visų publikuotų puslapių / įrašų kalbinė HTTP regresija, formų klaidų POST, 404 ir kontaktų kortelės | **967 patikros, 0 klaidų**; 94 publikuotų įrašų adresai |
| Dabartinio blokų redaktoriaus serverinė integracija | **97 patikros**: registruoti blokai, abu redaktorių vaidmenys, REST išsaugojimas, LT / EN / DE, atvaizdavimas |
| Importas į atskirą SQLite DB su kitais ID | Du sėkmingi 199 įrašų importai; **439 ryšių ir išlikimo patikros**; nėra dublikatų, papildomas „kliento“ puslapis išliko |
| Kontaktų ir CV apdorojimas | **17 scenarijų** su imituotu transportu; laiškai nesiųsti, laikinos CV kopijos pašalinamos |
| Importo apsauga | HTTP 403; be `--import-content` „WordPress“ net neįkeliamas; neišspręsta ID nuoroda atmetama |
| PHP sintaksė | **97 projekto PHP failai** be sintaksės klaidų |
| Diegimo konfigūracija | YAML ir **15 įterptų shell scenarijų** sintaksė tinkama; tikras Actions paleidimas neatliktas |
| Eksporto bandymas į TMP | 199 įrašai, 19 nustatymų, 14 unikalių vaizdų failų; repozitorijos kopija šiuo bandymu neperrašyta |
| Naršyklė | Titulinis ir kontaktai darbalaukyje bei telefone; LT → EN → DE; burgerio atidarymas / uždarymas ir navigacija |
| 360 × 800 titulinis LT / EN / DE | Puslapio plotis 360 px, CTA ir skaidrių valdikliai nesikerta |
| Git skirtumų patikra | `git diff --check` be klaidų |

Blokų kompiliavimas buvo sėkmingai patikrintas pradinio audito metu (147 failai); šiame pataisų etape blokų JavaScript šaltiniai nekeisti.

Senieji administravimo ir modulių vertimų testai atskiroje DB praėjo (23 ir 13 patikrų). **Senas `test-wordpress-modules.php` nepraeina**: jis tikisi ankstesnio nustatymų / modulių kompozitoriaus. Jis nebuvo „padarytas žalias“ mažinant dabartinės svetainės funkcijas; dabartiniam blokų saugojimui pridėtas atskiras `test-block-editor.php`. Viso seno testų rinkinio nelaikyti žaliu.

## Kas liko prieš galutinį perdavimą

- Realus bandomasis importas į kliento MySQL / MariaDB aplinką; vietinė SQLite patikra jo neatstoja.
- Tikri kontaktų ir kandidatavimo laiškai, CV gavimas, SMTP konfigūracija ir podėlių įtaka formoms.
- DB **ir failų** atsarginės kopijos atkūrimo bandymas. Importas nėra transakcija ir automatinio rollback nėra.
- Redaktoriaus priėmimas kliento serveryje: ne tik serverinis REST testas, bet ir visas teksto, vaizdo bei vertimo redagavimas naršyklėje.
- HTTPS, indeksavimas, tikro domeno nukreipimai, canonical / hreflang / sitemap, Safari / iOS / Android.
- Kliento patvirtinti veiklos teiginiai, kontaktai, sertifikatai, atlygio informacija, vertimai ir vaizdų naudojimo teisės. Neįrodytų faktų naujais pažadais nepakeičiau.
- Našumo matavimas realiame mobiliajame ryšyje. Medijos suspaudimas šiame pataisų etape neatliktas.

Esamame kliento serveryje senas viešas importas **dar nepašalintas šiuo darbu**, nes nebuvo vykdomas diegimas. Lokali kodo pataisa pati savaime serverio neapsaugo.

## Kopijos ir pakartojimas

- Pradinė vietinės DB kopija: `tmp/handoff-fix/before-fix.sqlite`.
- Izoliuota bandymų DB: `tmp/handoff-fix/sandbox/test.sqlite`.
- Importo bandymo pagalbiniai scenarijai: `tmp/handoff-fix/import-sandbox.php`, `verify-sandbox.php`. Jie nėra diegimo dalis.
- Galutinio eksporto patikros rezultatas: `tmp/handoff-fix/after-export/deploy/content/snapshot.json`.
- Pašalinta tik nenaudojama Git sekama `deploy/content/SYNC-ON` vėliavėlė; ją galima rasti Git istorijoje. Kliento turinys nešalintas.
- Patikros sugeneruotos papildomos vaizdų miniatiūros perkeltos į `tmp/handoff-fix/generated-thumbnails`, o ne ištrintos.
- TMP duomenų bazių ir testų artefaktų į klientui perduodamą viešą katalogą nekelti.

Pakartojamos pagrindinės patikros:
```bash
php tools/test-multilingual-site.php
php tools/test-block-editor.php /absoliutus/kelias/į/atskirą/test.sqlite
```

Tolesnė eiga aprašyta [DIEGIMAS.md](DIEGIMAS.md).
