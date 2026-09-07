# 5G TECH – patikra prieš perdavimą klientui

> Tai pradinė būsena prieš pataisas. 2026-09-07 pataisų rezultatai ir likę priėmimo darbai aprašyti [PATAISYMAI-2026-09-07.md](PATAISYMAI-2026-09-07.md). Žemiau pateiktos failų eilutės atitinka audito metu tikrintą, ne dabartinį kodą.

Data: 2026-09-07. Tikrintas `main`, paskutinis įrašas `284ee9c` (2026-09-04).

## Verdiktas

**Kaip užbaigtos svetainės dar neperduoti ir į viešą kliento serverį dabartiniu diegimo scenarijumi nekelti.** Svetainės pagrindas veikia, tačiau yra saugumo, turinio perkėlimo ir kelių vartotojo sąsajos problemų. Pirmiausia jas pašalinti, tada atlikti bandomąjį perkėlimą į izoliuotą kliento serverio aplinką.

Tai vietinės WordPress svetainės ir repozitorijos auditas, ne kliento serverio saugumo sertifikavimas. Produkcinis kodas ir valdomas turinys šio audito metu neredaguoti; niekas nepublikuota. Patikrai paleistos vietinės Herd paslaugos. Laiškai klientui ar kandidatams nesiųsti.

## Kas patikrinta ir veikia

| Patikra | Rezultatas | Ribos |
|---|---|---|
| WordPress paleidimas | HTTP 200, aktyvi 5gtech tema, 5gtech-core ir Polylang | Vietinė aplinka, PHP 8.4.23, WordPress 7.0.2 |
| Viešų įrašų adresai | 94 iš 94 galutinių atsakymų HTTP 200 | 46 puslapiai, 18 paslaugų, 15 komandos profilių, 9 naujienos, po 3 darbo pozicijų ir projektų įrašus; skaičiuojami kalbų variantai |
| Antraštės | Kiekviename iš 94 atsakymų po vieną H1 | Tai struktūros, ne viso teksto kokybės patikra |
| Failų pasiekiamumas | 51 unikalus surinktas vietinis resursas pasiekiamas | HTML img/script/stylesheet/source/poster; ne visų CSS fonų ar viso medijos katalogo auditas |
| Kitos vidinės nuorodos | 18 papildomų adresų pasiekiami po nukreipimų | Neapima visų išorinių nuorodų ir inkarų |
| Neegzistuojantis puslapis | Grąžina HTTP 404 | `/audit-missing-20260907/` |
| PHP sintaksė | 85 temos, pagrindinio papildinio ir deploy PHP failai be sintaksės klaidų | Tai nėra visų priklausomybių ar PHP versijų suderinamumo garantija |
| Gutenberg blokų kompiliavimas | Sėkmingas, 147 sugeneruoti failai | Esamos vietinės priklausomybės; rezultatas rašytas į tmp, ne produkcinį build |
| Formų serverinė logika | 17 atvejų pateikė laukiamą rezultatą | Transportas pakeistas vietiniu imitatoriumi; realus SMTP netikrintas |
| Kalbų pasirinkimas | Tituliniame ir vidiniame puslapyje yra; paslaugų LT → EN → DE perjungimas išlaiko puslapį | Patikrinta naršyklėje, ne vien ieškant HTML teksto |
| Mobilus meniu | Tituliniame ir vidiniame puslapyje rodomas burgeris; meniu atsidaro, nuoroda atveria paslaugas | 390 px plotis; antraštėje atskiro Kontaktai mygtuko mobilėje nėra |
| Pagrindiniai maketai | Peržiūrėti titulinis, paslaugos, vadovams ir karjera | Darbalaukio bei 390/360 px mobilūs vaizdai; ne visų 94 adresų vizualinė matrica |
| Komandos vardai | Tituliniame rodomi realūs vardai, ne „Vardas Pavardė“ | Tikrintoje dabartinėje vietinėje versijoje |
| Diegimo medija | Visi snapshot attachments nurodyti originalūs failai yra deploy/uploads | Neatstoja importo į kitą duomenų bazę testo |

Formų atvejai: netinkamas nonce, trūkstami laukai, netinkamas el. paštas, sutikimo nebuvimas, honeypot, sėkmė ir pašto transporto klaida abiem formoms; kandidatavimui papildomai trūkstamas CV, per didelis CV ir netinkamas plėtinys. Imituoto siuntimo metu CV laikina kopija po apdorojimo pašalinta. Tikras laiško pristatymas ir failo įkėlimas per naršyklę nebuvo šiais testais patvirtinti.

## Perdavimo stabdžiai

### P1-1. Sinchronizavimo scenarijus neturi apsaugos nuo paleidimo per HTTP

Šaltiniai: [sync-content.php:16](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:16), [deploy.yml:247](/Users/eduardas/Claude/Projects/5gTech/.github/workflows/deploy.yml:247), [sync-content.php:309](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:309).

Diegimas kopijuoja `deploy/` į viešo WordPress katalogo `wp-content/g5-deploy/`. `sync-content.php` pradžioje nėra CLI-only patikros, autentifikavimo ar analogiško vartų mechanizmo. Scenarijus pats įkelia WordPress, nustato administratoriaus kontekstą ir, esant SYNC-ON, perrašo turinį bei negrįžtamai šalina snapshot neatitinkančius įrašus. Įkeliamas ir pats snapshot JSON, kuriame eksportuojami ne tik vieši įrašai, bet ir juodraščiai / privatūs įrašai.

Tai patvirtinta kodo ir diegimo kelio peržiūra. **Pavojingas HTTP endpoint nebuvo vykdomas.** Nežinoma, ar konkrečiame kliento serveryje šį kelią blokuoja išorinė taisyklė, todėl negalima teigti, kad gyva svetainė jau išnaudojama ar pažeista.

Taisyti: neleisti vykdymo ne CLI režimu dar prieš WordPress įkėlimą; scenarijų ir turinio kopiją laikyti už viešo katalogo arba aiškiai uždrausti HTTP prieigą. Po pataisos izoliuotoje aplinkoje patvirtinti, kad HTTP užklausa negali nei gauti turinio kopijos, nei sukelti duomenų pakeitimų.

### P1-2. Perkeliant į kitą duomenų bazę nepersiejamos visos ID nuorodos

Šaltiniai: [sync-content.php:121](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:121), [sync-content.php:197](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:197), [equipment-logos/block.json](/Users/eduardas/Claude/Projects/5gTech/wordpress/wp-content/plugins/5gtech-core/blocks-src/equipment-logos/block.json), [team.php:1160](/Users/eduardas/Claude/Projects/5gTech/wordpress/wp-content/plugins/5gtech-core/includes/team.php:1160).

Importas persieja `imageId` / `image1Id` tipo atributus, bet ne bendras įrašų nuorodas. Snapshot yra tikri paveikiami duomenys:

- Mokymų LT / EN / DE blokai turi `partnerIds: [22,23,24,44,45,144]`.
- Aleksandro profilio kalbų variantuose `g5_team_operators` saugomi vietiniai operatorių įrašų ID, pvz., 31, 34, 35.

Metadata nukopijuojama pažodžiui. Naujame WordPress tie patys skaičiai nebūtinai reiškia tuos pačius partnerius: gali dingti sąrašai arba būti parodyti neteisingi ryšiai. Ši problema gali nesimatyti vietinėje kopijoje.

Taisyti: įrašams sukurti pilną senas ID → naujas ID atitikmenų žemėlapį arba eksportuoti stabilias tipas/slug nuorodas; po įrašų sukūrimo persieti blokų ir meta ryšius. Patvirtinti importu į izoliuotą bazę, kurioje ID sąmoningai skiriasi. Atlikti du importus ir patikrinti, kad neatsiranda dublikatų.

### P1-3. Dar neperduota turinio kontrolė klientui

Šaltiniai: [SYNC-ON](/Users/eduardas/Claude/Projects/5gTech/deploy/content/SYNC-ON), [sync-content.php:291](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:291), [polylang-shared.php:22](/Users/eduardas/Claude/Projects/5gTech/deploy/polylang-shared.php:22).

Repozitorijoje yra SYNC-ON. Kol jis įjungtas, kitas diegimas perrašys kliento WordPress pakeitimus, o įrašus, kurių nėra snapshot, gali pašalinti visam laikui. Tai dokumentuotas kūrimo režimas, bet ne saugus galutinis perdavimo režimas.

Be to, Polylang nustatymų įrašymas vykdomas **prieš** SYNC-ON patikrą. Vien pašalinus vėliavėlę kodas vis tiek gali perrašyti kalbų nustatymus.

Taisyti: po vienkartinio patvirtinto importo išjungti turinio perrašymą ir atskirti vienkartinį kalbų paruošimą nuo įprastų kodo atnaujinimų. Priėmimo testas: klientas pakeičia tekstą / sukuria puslapį, atliekamas kodo diegimas, turinys ir kalbų konfigūracija išlieka.

### P1-4. Tikrinama vietinė svetainė nėra tokia pati kaip numatyta importuoti versija

Šaltinis: [patikros duomenys](/Users/eduardas/Claude/Projects/5gTech/tmp/audit-2026-09-07/checks.json).

10 įrašų `post_content` skiriasi nuo `deploy/content/snapshot.json`: pagrindinis, patirtis, mokymai, home, startseite, training, schulungen, paslaugos, services, leistungen.

Pavyzdžiai: snapshot turi atnaujintus ISO / SSVA / VERT tekstus, o dalyje vietinių puslapių dar ankstesnės formuluotės. Mokymų salės paveikslo šaltinis snapshot yra `mokymu-sale.jpg`, vietinėje bazėje – `generated/training-technical-lab-v1.jpg`. Snapshot `generated` lauke likusi 2026-08-04 data, nors jo turinys keistas vėliau; vien šia data remtis negalima.

Tai nereiškia, kad vietinė bazė yra naujesnė ar teisingesnė. **Negalima aklai eksportuoti vietinės bazės ir perrašyti snapshot**, nes taip galima prarasti naujesnius pakeitimus.

Taisyti: suderinti, kuri versija yra patvirtinta, ir būtent tą pačią versiją patikrinti bandomai importavus. Dabartinis vietinis vizualinis auditas negali patvirtinti, kad po importo bus identiškas rezultatas.

## Funkciniai ir užbaigtumo trūkumai

### P2-1. Kontaktų ir kandidatavimo formos praranda kalbą

Šaltinis: [forms.php:72](/Users/eduardas/Claude/Projects/5gTech/wordpress/wp-content/plugins/5gtech-core/includes/forms.php:72).

HTTP testu atkurta: kontaktų forma su EN kalbos slapuku ir EN puslapio Referer, gavusi netinkamą nonce, atsako `Location: /kontaktai/?forma=security`. Kandidatavimo forma iš DE grąžina `/kandidatuoti/?forma=security`. Abiem atvejais tai lietuviški adresai. Bendra redirect funkcija naudojama ir kitoms klaidoms bei sėkmei.

Taisyti: perduoti ir patikrinti formos kalbą, grąžinimo adresą gauti iš Polylang puslapio vertimo. Patikrinti visų trijų kalbų sėkmę ir klaidas po realaus POST, ne vien atveriant puslapį su būsenos parametru.

### P2-2. Tituliniame telefone persidengia CTA ir skaidrių valdikliai

Šaltinis: [home.css:1220](/Users/eduardas/Claude/Projects/5gTech/wordpress/wp-content/themes/5gtech/assets/css/home/home.css:1220).

Atkurta 360 × 800 naršyklės lange. `.hero-actions` vertikalios ribos yra maždaug 543–673 px, o `.hero-rotator` – 637–684 px. Skaidrių tekstas ir valdikliai užlipa ant „Peržiūrėti paslaugas“. 390 × 844 vaizde atstumas taip pat pernelyg mažas. Horizontalus puslapio plotis 360 px teste išlieka teisingas – tai vertikalaus išdėstymo, ne bendro pločio klaida.

Taisyti: mobilėje valdiklius išdėstyti normalioje turinio tėkmėje arba rezervuoti jiems atskirą vietą; tikrinti LT, EN, DE ir mažą ekrano aukštį. Vien burgerio pataisos neužtenka.

### P2-3. Dabartinis automatinis kalbų testas netinka priėmimui

Šaltinis: [test-multilingual-site.php](/Users/eduardas/Claude/Projects/5gTech/tools/test-multilingual-site.php), [vykdymo žurnalas](/Users/eduardas/Claude/Projects/5gTech/tmp/audit-2026-09-07/legacy-multilingual.log).

Rezultatas: **273 nepraėję patikrinimai iš 1116**. Tai nėra 273 patvirtinti svetainės gedimai. Testas tikisi ankstesnio `g5_lang` mechanizmo, senų perjungiklio klasių, keturių hreflang nuorodų ir dalį teisėtų kalbos perjungimo nuorodų klaidingai vertina kaip nutekėjimą į kitą kalbą. Dabartinė Polylang išvestis turi trijų kalbų hreflang, ir realus perjungimas veikia.

Taisyti: testą pritaikyti tikriems Polylang URL, vertimų ryšiams ir dabartinei navigacijai; įtraukti formų POST bei migracijos ID patikras. Į CI pridėti trumpą funkcionalumo testą po diegimo. Dabartinis vietinis HTTP surašymas yra diagnostinis, ne pilnas šio testo pakaitalas.

### P2-4. Diegimas gali baigtis „sėkmingai“, nors dalis turinio nepritaikyta

Šaltiniai: [sync-content.php:113](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:113), [sync-content.php:163](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:163), [sync-content.php:192](/Users/eduardas/Claude/Projects/5gTech/deploy/sync-content.php:192).

Trūkstamos nuotraukos tik išveda įspėjimą, neegzistuojantys įrašų tipai praleidžiami, WordPress įrašo klaida išvedama ir tęsiama. Pabaigoje tokios dalinės klaidos nebūtinai grąžina nenulinį proceso kodą. Todėl žalias diegimas nėra pilno importo įrodymas.

Scenarijus automatiškai aktyvuoja Polylang, tačiau naujam serveriui reikia aiškiai numatyti ir 5gtech-core aktyvavimą bei 5gtech temos pasirinkimą prieš importą. Vien failų įkėlimas to negarantuoja.

Taisyti: išankstinės priklausomybių patikros, klaidų skaitiklis su nesėkmingu rezultato kodu ir importuotų įrašų / ryšių / medijos sutikrinimas. Į diegimo instrukciją įtraukti švaraus WordPress paruošimą. Apsvarstyti nenutraukiamą diegimą: dabartinis `cancel-in-progress: true` gali nutraukti vykstantį failų kopijavimą; nėra patikrinto automatinio grįžimo į ankstesnę versiją.

### P3. Galutinis tekstų ir medijos sutvarkymas

- EN ir DE naujienų kategorijose, taip pat titulinių naujienų kortelėse rodoma `Uncategorized`.
- DE mobilus meniu turi lietuvišką matomą antraštę `MENIU`; kai kurių naujai pridėtų komandos nuorodų prieinamumo pavadinimai lieka `Peržiūrėti: ...`.
- Vadovams puslapyje yra absoliučių pažadų („visa atsakomybė“, grafikas, „kurio laikomės“), kuriuos klientas turi patvirtinti kaip savo komercinę poziciją. Tai nėra šiame audite patvirtinti įmonės veiklos faktai.
- Prieš publikavimą patvirtinti žmonių duomenis, 6000+ projektų / stočių teiginį, sertifikatus, atlygio informaciją ir vaizdų naudojimo teises. Automatinis testas faktų tikrumo neįrodo.
- Titulinio pagrindinis PNG yra apie 1,78 MiB, salės vaizdo įrašas – 7,72 MiB. Tai našumo optimizavimo kandidatai; realaus mobiliojo ryšio LCP / INP matavimas neatliktas.

## Kas dar turi būti patvirtinta kliento serveryje

1. Izoliuotas bandomasis importas su kitais įrašų ID, visų kalbų / partnerių / komandos / medijos ryšių sutikrinimas ir pakartotinio importo testas.
2. Tikroji PHP, MySQL/MariaDB ir WordPress konfigūracija; tema bei papildiniai aktyvūs; produkcinėje aplinkoje nepaliktas vietinio pašto imitatorius ar `local` režimas.
3. Kontaktų ir CV formų siuntimas su kliento patvirtintais testiniais gavėjais: realus gavimas, Reply-To, priedas, nesėkmės atvejis. SMTP / SPF / DKIM sukonfigūruoti pagal pasirinktą pašto paslaugą.
4. Prisijungimas kliento redaktoriaus teisėmis: teksto, nuotraukos, paslaugos, darbuotojo, darbo pozicijos bei vertimo pakeitimas, išsaugojimas, peržiūra ir atstatymas. Dabartinės DB neredagavau vien tam, kad imituočiau šį priėmimą.
5. Atsarginė bazės IR failų kopija bei realus atstatymo bandymas. Esamas workflow turi bazės kopijavimą prieš importą, bet tai dar ne patvirtintas visos svetainės atkūrimas.
6. Tikras domenas, HTTPS, cache, formų nonce su cache, canonical/hreflang/sitemap, senų 5gtech.lt adresų nukreipimai. Staging neturi būti indeksuojamas, live turi būti indeksuojamas pagal kliento sprendimą.
7. Chrome ir Safari / iOS bei Android patikra; mobilus meniu, formos, kalbos, pagrindinis CTA ir mažas ekrano aukštis. Šiame audite naudota viena naršyklės aplinka.
8. Kliento patvirtinti privatumo / slapukų tekstai ir atitiktis faktiškai naudojamoms išorinėms paslaugoms. Teisinis atitikties auditas nebuvo atliekamas.

## Siūloma užbaigimo tvarka

1. Uždaryti viešo sinchronizavimo riziką.
2. Sutaisyti perkėlimo ryšius ir patikimą klaidų fiksavimą.
3. Suderinti vietinį ir eksportuojamą turinį, pasirinkti patvirtintą versiją.
4. Sutaisyti formų kalbą, mobilų CTA persidengimą ir likusius vertimo užpildus.
5. Atlikti bandomąjį perkėlimą, redagavimo bei pašto priėmimo testus.
6. Po kliento patvirtinimo atlikti vienkartinį live importą, išjungti turinio perrašymą, patikrinti veikimą ir perduoti prieigas / naudojimo instrukciją.

**Darbo rezultatas dabar: veikianti svetainė, kuriai reikia aiškiai apibrėžto užbaigimo etapo, o ne kūrimo iš naujo.**

Diagnostikos failai: [HTTP ir turinio duomenys](/Users/eduardas/Claude/Projects/5gTech/tmp/audit-2026-09-07/checks.json), [tik skaitymui skirtas tikrintuvas](/Users/eduardas/Claude/Projects/5gTech/tmp/audit-2026-09-07/check.php), [formų testų scenarijus](/Users/eduardas/Claude/Projects/5gTech/tmp/audit-2026-09-07/forms.php). Jie nėra svetainės diegimo dalis.
