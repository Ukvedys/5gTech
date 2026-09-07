# 5G TECH: vizualinė ir administravimo UX peržiūra

Data: 2026-09-07. Aplinka: vietinis WordPress, http://5gtech.test.

**Atnaujinimas po audito:** naudotojui leidus taisyti, pagrindinės žemiau aprašytos problemos pataisytos. Aktualūs pakeitimai, testų rezultatai ir dar nepatikrintos dalys aprašyti [pataisymų ataskaitoje](UX-PATAISYMAI-2026-09-07.md). Toliau palikta pirminė audito būsena, ne dabartinio kodo vertinimas.

## Išvada

Patikrintuose viešuose puslapiuose bendras išdėstymas tvarkingas: neaptikta horizontalaus iškritimo iš ekrano, mobilus meniu ir tikrinti kalbų perjungimai veikia. Tačiau svetainės dar nerekomenduoju perduoti klientui savarankiškai redaguoti. Patvirtintas sudėtinių blokų išsaugojimo defektas gali pašalinti jų vidinį turinį. Administravime taip pat palikta klaidinančių senų nustatymų ir nesuderinta navigacija su redaktorių teisėmis.

Tai auditas, ne pataisymų ataskaita. Produkciniai failai, WordPress turinys ir nustatymai šios peržiūros metu sąmoningai nekeisti. Testinės užklausos el. paštu nesiųstos.

## Patikros apimtis ir ribos

- Atliktos 39 DOM geometrijos patikros: 13 puslapių po 360, 768 ir 1440 px pločiuose. Tikrintas dokumento plotis ir pagrindinių teksto, lentelių, formų bei navigacijos elementų iškritimas iš ekrano. Visos šios patikros praėjo; tai nereiškia, kad kiekvienas puslapio pikselis ar visos būsenos patikrinti.
- Maršrutai: `/`, `/paslaugos/`, `/paslaugos/mobiliojo-rysio-tinklai/`, `/vadovams/`, `/projektu-vadovams/`, `/karjera/`, `/karjera/telekomunikaciju-specialistas/`, `/apie-mus/`, `/mokymai/`, `/akademija/`, `/kontaktai/`, `/kandidatuoti/`, `/duk/`.
- Papildomai vizualiai peržiūrėti titulinio ir vidinių puslapių fragmentai darbalaukio bei mobiliajame dydžiuose; patikrintas mobilus meniu tituliniame ir karjeros puslapyje. Paspaudus kalbą titulinis perėjo į EN, karjeros puslapis — į atitinkamą DE puslapį.
- Administravimas vertintas pagal produkcinį kodą ir izoliuotą išsaugojimo testą su įdiegto WordPress JavaScript bei sukompiliuotais projekto blokais. Administravimo naršyklės lange liko prisijungimo ekranas: reali redaktoriaus darbo eiga su kliento paskyra, mediateka, peržiūra ir išsaugojimu dar nepatikrinta.
- Tai nėra pilnas WCAG atitikties, saugumo, visų vertimų ar visų naršyklių auditas. Tikrame telefone, Safari ir kliento serveryje šios patikros neatliktos.

## 1. P1 — išsaugant sudėtinius blokus prarandami vidiniai elementai

**Patvirtinta izoliuotu testu, tikro puslapio neišsaugant.**

Tėviniai blokai redaktoriuje naudoja vidinius blokus, tačiau jų `save()` grąžina `null`. WordPress JavaScript serializatorius išsaugo tik užsidarantį tėvinio bloko komentarą, be vaikinių blokų.

| Tėvinis blokas | Testinis vidinis blokas | Prieš → po serializavimo ir pakartotinio nuskaitymo |
| --- | --- | --- |
| section | card-grid | 1 → 0 |
| card-grid | card | 1 → 0 |
| home-hero | hero-slide | 1 → 0 |
| steps | step | 1 → 0 |
| check-list | check-item | 1 → 0 |
| home-audiences | audience-item | 1 → 0 |
| home-sections | card | 1 → 0 |

Pavyzdys: `home-hero` su skaidre tampa `<!-- wp:g5tech/home-hero /-->`. Viešo atvaizdavimo funkcija, negavusi skaidrių, grąžina tuščią rezultatą. Taigi rizika nėra vien redaktoriaus peržiūros neatitikimas — po išsaugojimo gali dingti pradinis titulinio ekranas, kortelės ar sekcijų turinys.

Šaltiniai: `wordpress/wp-content/plugins/5gtech-core/blocks-src/home-hero/index.js:72`, `blocks-src/section/index.js:50`, `blocks-src/home-sections/index.js:14`; `includes/content-blocks.php:1170` ir `1198`.

Pakartojamas vietinis testas: `node tmp/ux-audit-2026-09-07/serialize.cjs`. Testas registruoja realius sukompiliuotus blokus; redaktoriaus vaizdo komponentai šiame izoliuotame teste nekviečiami. Testinis failas laikomas laikiname, Git ignoruojamame kataloge.

**Taisymas:** visiems blokams su vaikais išsaugoti vidinį turinį per `InnerBlocks.Content` arba atitinkamą `useInnerBlocksProps.save()` realizaciją; ne keisti visų dinaminių lapinių blokų `save()` aklai. Toks modelis pateiktas [oficialioje WordPress vidinių blokų dokumentacijoje](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/nested-blocks-inner-blocks/).

**Priėmimas:** patikrinti visus sudėtinius blokus, tada viso titulinio ir bent dviejų vidinių puslapių kopijose pakeisti tekstą, išsaugoti, iš naujo atidaryti redaktorių ir palyginti vaikinių blokų skaičių bei viešą vaizdą. Vien REST užklausa su iš anksto paruoštu turinio tekstu šio kliento pusės defekto neaptinka.

## 2. P1 — administravime liko du konkuruojantys turinio redagavimo keliai

**Patvirtinta pagal kodo duomenų srautą.**

„Bendruose duomenyse“ vis dar pateikiami titulinio antraštės, įžangos, sekcijų rodymo ir eiliškumo nustatymai. Ten taip pat rašoma, kad pradinis paveikslėlis keičiamas kaip puslapio pagrindinis paveikslėlis. Dabartinis blokinis titulinis antraštę ima iš bloko atributų, o nuotrauką — iš vidinio skaidrės bloko, ne iš šios instrukcijos nurodytos vietos.

**Poveikis klientui:** žmogus pakeičia reikšmes, išsaugo, o puslapis nepasikeičia. Jam tenka spėlioti, ar kaltas išsaugojimas, podėlis, ar kita redagavimo vieta.

Šaltiniai: `includes/settings.php:335` (titulinio nustatymai); `includes/content-blocks.php:1170` (dabartinis atvaizdavimas); `blocks-src/home-hero/index.js:17` (vaizdo šaltinis). Senų ekranų nukreipimai `includes/admin-redirects.php` bendrų nustatymų ekrano nepašalina.

**Taisymas:** palikti tik iš tikrųjų naudojamus globalius laukus; vietoje senų titulinio valdiklių pateikti aiškią nuorodą „Redaguoti titulinį puslapį“. Instrukciją apie nuotrauką suderinti su skaidrių valdymu. Administravimo atmintinėje taip pat peržiūrėti „Turinio modulių“ kelią, kad klientas nebūtų siunčiamas į seną puslapių surinkimo mechaniką.

## 3. P2 — redaktorių teisės ir jiems rodomos nuorodos nesuderintos

**Patvirtinta pagal kodo konfigūraciją; faktinį vaizdą dar patikrinti su abiem paskyromis.**

- Personalo redaktoriui suteikiamos puslapių redagavimo teisės, bet „Puslapiai“ pašalinami iš jo meniu. Atmintinėje siūlomos darbo pozicijos ir komanda, tačiau nėra tiesioginio kelio į patį karjeros puslapį.
- Bendras turinio redaktorius neturi komandos įrašų valdymo teisių, tačiau titulinio komandos bloke visada rodomos nuorodos „Pridėti darbuotoją“ ir „Visa komanda“.

Šaltiniai: `includes/admin.php:129`, `150`, `605`, `695`; `blocks-src/home-team/index.js:14`.

**Taisymas:** rodyti veiksmus pagal realias teises; personalui duoti aiškų „Karjeros puslapio“ kelią ir sąmoningai apibrėžti, kuriuos puslapius jis gali redaguoti. Jei klientui realiai dirbs vienas žmogus, numatyti vieną tinkamai apribotą kasdienio redagavimo rolę, o ne spręsti problemą administratoriaus teisėmis.

## 4. P2 — po formos klaidos lankytojas grįžta į puslapio pradžią ir turi pildyti iš naujo

**Patvirtinta kode ir naršyklėje atidarius klaidos būsenos URL, nieko nesiunčiant.**

Siuntimo apdorojimas nukreipia į puslapį su `?forma=...`. Įvestų reikšmių grąžinimas nenumatytas. Klaidos pranešimas atvaizduojamas po forma; nėra automatinio fokuso į pranešimą. 360 × 740 px peržiūroje kontaktų `?forma=mail` pranešimas buvo maždaug 1866 px žemiau ekrano viršaus, puslapis liko pradžioje, fokusas — `BODY`.

Šaltiniai: `includes/forms.php:72`, `298–341`, `351–454`; `includes/content-blocks.php:652–665` ir kandidatavimo formos atvaizdavimas tame pačiame faile.

**Taisymas:** po nesėkmės išlaikyti saugiai apdorotus įvestus duomenis, iškart parodyti ir sufokusuoti pranešimą, nurodyti konkrečią taisytiną vietą. CV failo naršyklė automatiškai neatkurs — apie būtinybę prisegti iš naujo reikia aiškiai pasakyti. Nepersistuoti jautrių formos duomenų URL ar neapibrėžtam laikui naršyklės saugykloje.

## 5. P2 — telefone dalis tekstų per maži, kalbos valdikliai ankšti

**Patvirtinta vizualiai ir pagal apskaičiuotus CSS dydžius.**

360 px pločio tituliniame faktų paaiškinimai ir „ISO / SSVA / VERT“ sumažinami iki 8 px. Kalbų nuorodų paspaudimo sritys yra maždaug 32 × 30 px. Mobilus meniu turi patogesnę 48 × 48 px paspaudimo sritį.

Šaltinis: `wordpress/wp-content/themes/5gtech/assets/css/home/home.css:1230`.

**Taisymas:** mobiliajame faktus perkomponuoti, o ne smulkinti iki 8 px; rinktis apie 12–14 px paaiškinimus ir didesnes kalbų paspaudimo sritis. 44 × 44 px čia yra patogumo rekomendacija, ne automatinė WCAG 2.1 AA pažeidimo išvada: [WCAG 2.1 kriterijus 2.5.5 yra AAA](https://www.w3.org/WAI/WCAG21/Understanding/target-size.html).

## 6. P2 — paslaugų kortelės praranda nuorodos semantiką

**Patvirtinta naršyklės prieinamumo medyje ir kode.**

Titulinio paslaugų kortelės yra nuorodos su `href`, tačiau joms suteiktas `role="listitem"`. Prieinamumo medyje jos pateikiamos kaip konteineriai, ne nuorodos. Tai apsunkina paslaugų radimą naudojant pagalbines technologijas.

Šaltinis: `includes/content-blocks.php:1324`; analogiškas senas atvaizdavimas yra `includes/homepage.php:310`.

**Taisymas:** sąrašo elemento vaidmenį suteikti apvalkalui, o pačiai nuorodai palikti jos natūralią semantiką. Patikrinti klaviatūrą, fokusą ir nuorodų sąrašą ekrano skaitytuve.

## Papildomi patogumo pagerinimai

1. **Komandos kortelės žada profilį, kai jo nėra.** Keturios titulinio nuorodos pažymėtos „Peržiūrėti profilį“, bet veda į bendrą `/apie-mus/#komanda` sekciją. Aleksandro profilis turi atskirą adresą. Kai asmeninis profilis išjungtas, rinktis neklaidinančią bendros komandos nuorodą arba nerodyti kortelės kaip profilio nuorodos. Šaltinis: `includes/homepage.php:98–101`.
2. **„Susisiekti su vadovu“ veda į bendrą kontaktų puslapį.** Tai ne neveikianti nuoroda, bet papildomas paieškos žingsnis. Siūlau vesti prie konkretaus kontakto arba į formą su aiškiu gavėju.
3. **Redaktoriaus peržiūroje rodikliai įrašyti tiesiogiai.** Titulinio pradžios peržiūroje `6000+` ir `6` yra fiksuoti, nors viešas puslapis ima šiuos duomenis iš nustatymų. Pasikeitus realiems rodikliams klientas matys skirtingus skaičius. Šaltinis: `blocks-src/home-hero/index.js:57–58`. Naudoti tą patį duomenų šaltinį.
4. **Automatinės skaidrės neturi aiškaus „Pristabdyti“ veiksmo.** Yra ankstesnės / kitos skaidrės valdymas ir atsižvelgiama į sumažinto judėjimo nuostatą, bet savarankiškai sustabdyti rotaciją paprastam lankytojui nėra aiškaus mygtuko. Siūlau jį pridėti, išlaikant pasirinktą automatinio demonstravimo koncepciją. Šaltinis: temos `assets/js/home.js:117`.
5. **Struktūros apsaugą dar patikrinti kliento paskyroje.** `canLockBlocks=false` tik uždraudžia keisti užrakinimą; pats savaime jis neužrakina jau neužrakintų sekcijų. Klientui palikti aiškų teksto ir vaizdų redagavimą, o bendro tinklelio bei sudėtingų apvalkalų judinimą riboti ten, kur jis nereikalingas. Šaltinis: `includes/editor-curation.php`.

## Siūloma darbų eilė ir perdavimo kriterijai

1. Sutaisyti sudėtinių blokų išsaugojimą; iki tol neraginti kliento išsaugoti produkcinių puslapių. Turėti patikrintą turinio atsarginę kopiją.
2. Pašalinti neveikiančius senus valdiklius, suvienodinti instrukcijas ir tikrą turinio redagavimo vietą.
3. Prisijungus patikrinti abi redaktorių roles: pakeisti tekstą ir nuotrauką, tvarkyti skaidrę, paslaugą, darbuotoją, darbo poziciją ir vertimą. Šiems bandymams naudoti atskiras turinio kopijas, ne esamus kliento puslapius.
4. Sutvarkyti formų klaidų kelią, mobilius smulkius užrašus ir nuorodų semantiką.
5. Pakartoti vizualinę patikrą darbalaukyje ir telefone po realaus išsaugojimo; patikrinti titulinį, vidinį puslapį, meniu ir LT / EN / DE perjungimą.
6. Tik tada užbaigti trumpą kliento atmintinę su realiais, veikiančiais keliais. Migracijos į kliento serverį, laiškų pristatymo ir atsarginių kopijų atkūrimo bandymai lieka atskiras perdavimo etapas.

Vertinant naudotos `design:design-critique`, `design:accessibility-review` ir `write-human-web-copy` gairės: jos padėjo atskirti vizualinį skonį nuo funkcinių klaidų, peržiūrėti veiksmų aiškumą ir nevadinti dalinės patikros pilnu prieinamumo auditu. Svetainės dizainas ar tekstai pagal šias gaires šį kartą nekeisti.
