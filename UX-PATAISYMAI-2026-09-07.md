# 5G TECH — vizualiniai ir administravimo UX pataisymai

2026-09-07 · Vietinė WordPress svetainė: http://5gtech.test

## Rezultatas

Pataisytas patvirtintas sudėtinių blokų turinio praradimas išsaugant, klaidinantys seni administravimo valdikliai, teisių neatitinkančios redaktoriaus nuorodos, formų klaidų eiga ir keli mobiliojo išdėstymo trūkumai. Pakeitimai atlikti produkcinėje WordPress temoje bei `5gtech-core`, ne statiniuose maketuose. Blokų surinkti `build` failai atnaujinti.

Papildomai prisijungus „Chrome“, vizualinio redaktoriaus, teksto, nuotraukos ir skaidrės išsaugojimo patikra praėjo. Tai dar nėra besąlyginis patvirtinimas perduoti klientui: liko gyvai patikrinti abi kliento roles bei migracijos ir laiškų pristatymo eigą.

## Kas sutvarkyta

### Turinys ir administravimas

- 11 sudėtinių Gutenberg blokų dabar išsaugo vaikinius blokus per `InnerBlocks.Content`. Lapiniai dinaminiai blokai nekeisti aklai.
- Bendruose nustatymuose pašalinti dabartinio titulinio nebevaldantys antraštės, įžangos, sekcijų rodymo ir rikiavimo laukai. Vietoje jų yra instrukcija ir atskiros LT, EN bei DE titulinio redagavimo nuorodos, naudojančios Polylang įrašus.
- Senos `home_*` nustatymų reikšmės išsaugant kitus nustatymus neištrinamos. Esamas turinys ir seni modulių įrašai nešalinti.
- Atmintinė nebesiunčia kliento į seną „Turinio modulių“ mechanizmą. Paaiškinta, kaip atidaryti vertimą, nekeičiant originalaus įrašo kalbos. Pridėta tiesioginė nuoroda į karjeros puslapį.
- Personalo redaktoriui meniu nebeslepiami puslapiai, kuriuos jis jau turėjo teisę redaguoti. Papildomų administratoriaus teisių nesuteikta.
- Blokų nuorodos į darbuotojų, paslaugų, pozicijų ir kitus valdymo ekranus rodomos tik turint atitinkamas teises. Senų modulių meniu paslėptas abiem kliento redaktorių rolėms, bet paliktas administratoriui.
- Titulinio redaktoriaus rodikliai imami iš bendrų nustatymų, ne fiksuotų demonstracinių skaičių.
- Prisijungus peržiūrėti bendri nustatymai, atmintinė, darbuotojo ir darbo skelbimo laukai bei jų vertimų nuorodos. Tikri įrašai per šiuos ekranus neišsaugoti.

### Viešas puslapis ir formos

- Įjungus JavaScript, nepavykus siuntimui tekstas ir pasirinktas CV lieka esamoje formoje. Klaidos pranešimas parodomas ir sufokusuojamas; siuntimo metu apsaugoma nuo pakartotinio paspaudimo.
- Pasibaigusio saugos rakto atveju grąžinamas naujas raktas, kad galima būtų bandyti vėl nepildant visko iš naujo.
- Ryšio klaida nevadinama garantuotai neišsiųsta forma: pranešama, kad pristatymo patvirtinti nepavyko. Automatinio pakartotinio siuntimo nėra.
- Duomenys neįrašomi į naršyklės ilgalaikę saugyklą ar URL. Be JavaScript lieka įprastas POST nukreipimas į pranešimo vietą, tačiau įvestų laukų išlaikymas tokiu režimu negarantuojamas.
- Formų pranešimai išlaiko LT / EN / DE kalbą. Pataisytas el. pašto paminėjimas ankstesniuose EN / DE siuntimo klaidos vertimuose.
- Mobilūs titulinio rodiklių paaiškinimai padidinti nuo 8 iki 12 px; perkomponuota jų eilutė. Kalbų paspaudimo sritys padidintos iki bent 44 × 44 px, siaurame ekrane sutalpinti logotipas, kalbos ir meniu.
- „Kaip dirbame“ planšetėje perjungta į vieną skaitomą stulpelį. Pataisyti ilgo teksto iškritimai titulinio naujienose, auditorijų ir komandos kortelėse, „Apie mus“, darbo pozicijų eilutėse, informacinėse kortelėse ir rodiklių juostoje. Patikrinti ir ilgesni EN / DE vertimai.
- Paslaugų kortelėms grąžinta natūrali nuorodos semantika. Komandos kortelės nebežada asmeninio profilio, kai veda tik į bendrą komandą.
- Titulinio skaidres galima pristabdyti ir tęsti. Pauzė išlaikoma grįžus į skirtuką; sumažinto judėjimo nuostata lieka gerbiama.

## Patikrinta

| Patikra | Rezultatas |
| --- | --- |
| Tikras įdiegto WordPress JavaScript serializatorius ir sukompiliuoti blokai | 58 sėkmingi ciklai: 11 tėvinių tipų, gilesnis lizdavimas, visi 46 paskelbti puslapiai |
| Redaktoriaus teisės ir REST išsaugojimas atskiroje testinėje duomenų bazėje | 127 patikros praėjo |
| Formų klaidos, įvesties išlaikymas, dubliuotas siuntimas, fokusas, sėkmės būsena | 28 izoliuotos patikros praėjo; tikri laiškai nesiųsti |
| Viešų puslapių ir kalbų HTTP regresija | 1003 patikros, 0 klaidų; siuntimo testai tik su netinkamu raktu arba trūkstamais privalomais laukais |
| 21 puslapis × 320, 360, 768, 1440 px | 84 DOM pločio patikros, horizontalaus puslapio iškritimo neliko |
| PHP sintaksė, formų ir titulinio JavaScript sintaksė, Git tarpų patikra | Praėjo |

21 puslapio matrica apima titulinį, karjerą, „Apie mus“, vadovų ir projektų vadovų puslapius, kontaktus bei kandidatavimą visomis trimis kalbomis. Matmenų testas nėra visų puslapių pikselių ar visų galimų būsenų vizualinė patikra.

Papildomai vizualiai patikrinti titulinio bei vidinių puslapių fragmentai darbalaukio ir mobiliajame dydžiuose, antraštė, mobilus meniu bei kalbų paspaudimai. Karjeros LT → DE nuoroda atidarė atitinkamą vidinį puslapį. „Escape“ uždaro meniu. Pristabdytos skaidrės liko vietoje. Kontaktų `?forma=mail` būsenoje fokusas buvo `g5tech-form-status`, o pranešimas matomas ekrane.

### Tikras išsaugojimas administravime

Sukurta pažymėta „Apie mus“ kopija — juodraštis Nr. 503. Per WordPress kodo redaktorių pakeistas vienas tekstas, grįžta į vizualinį režimą ir paspausta „Išsaugoti juodraštį“. Pakartotinai patikrinus duomenų bazę, pakeistas tekstas išliko, visų 24 blokų struktūra išliko. Originalaus puslapio Nr. 83 turinio SHA-256 prieš ir po sutapo:

`92e7cd59bba878d4f4efa96b8042ce6e56444017a817f2353031097c1db5ec52`

Baigus bandymą tik juodraštis Nr. 503 perkeltas į WordPress šiukšlinę; jį galima atkurti. Publikuotas turinys ir bendri nustatymai per administravimo bandymus nekeisti. Snapshot neatnaujintas, nes valdomas produkcinis turinys nekeistas.

## Liko prieš galutinį perdavimą

1. **Vizualinio redaktoriaus blokavimas nepasikartojo „Chrome“.** Prisijungus vartotojui, redaktoriaus turinio iframe užsikrovė ir jame galima redaguoti tiesiogiai. Ankstesnis tuščias langas pasireiškė programėlės naršyklėje, ne „Chrome“; tikslus programėlės techninis apribojimas nebuvo papildomai diagnozuotas. Prisijungimo ir bazinio vizualinio redaktoriaus patikros nebelaukiama.
2. **Abi kliento rolės gyvai.** Atskiroje testinėje bazėje teisės ir išsaugojimas tikrinti, tačiau realus vizualus teksto, nuotraukos, skaidrės ir vertimo redagavimas su abiem rolėmis lieka priėmimo bandymu. Hero ir CTA blokų užrakinimo atributai esamame turinyje yra; ankstesnė bendro pobūdžio pastaba nėra įrodymas, kad visa struktūra neužrakinta.
3. **CTA į konkretų vadovą.** Ankstesnio audito pasiūlymas bendrą „Susisiekti su vadovu“ kelią nukreipti į konkretų žmogų neįgyvendintas: atsakingą gavėją reikia suderinti, ne atspėti.
4. **Kliento serveris.** Migracija, tikras el. laiškų ir CV pristatymas, atsarginės kopijos atkūrimas bei tikro telefono / Safari patikra neatlikti. Svetainė nepublikuota, pakeitimai nepushinti, WordPress ir papildiniai neatnaujinti.
5. **Specialių puslapių kopijavimas į naują puslapį.** Sukurta testinė „Apie mus“ turinio kopija su nauju adresu naudoja bendrinį puslapio šabloną, o teminiai stiliai dalinai parenkami pagal originalaus puslapio adresą. Todėl toks kopijavimas nėra pilna dizaino kopijavimo funkcija: testinės kopijos viešoje peržiūroje matėsi bendrinė antraštė ir 60 px horizontalus perpildymas ties 1800 px. Tai nėra patvirtintas esamo „Apie mus“ puslapio redagavimo defektas. Jei klientui reikia kurti tokius naujus puslapius, būtina atskirai sutvarkyti šablonų ir stilių parinkimą pagal turinį; ši funkcija šiuo etapu nepakeista.

## Papildoma „Chrome“ patikra prisijungus vartotojui

- Naudota vartotojo administratoriaus sesija; jo teisės ir slaptažodis nekeisti.
- Testinė „Apie mus“ kopija Nr. 505: vizualiai pakeista istorijos antraštė ir vidinio sąrašo elemento antraštė. Paspaudus „Išsaugoti juodraštį“ ir iš naujo atidarius redaktorių, abu tekstai išliko, blokų skaičius liko 24.
- Tame pačiame juodraštyje atidaryta mediateka ir pasirinkta esama nuotrauka „5G TECH komandos darbai“ (failas Nr. 64). Po išsaugojimo duomenų bazėje patvirtinta `image1Id:64`; juodraščio peržiūroje nauja nuotrauka užsikrovė. Bibliotekos failai ir jų aprašymai nekeisti.
- Titulinio kopija Nr. 508: vizualiai pakeistas pirmos skaidrės pavadinimas. Po išsaugojimo ir pakartotinio atidarymo pakeitimas išliko, bendras blokų skaičius liko 22. Šešios skaidrės redaktoriuje pasiekiamos.
- Angliškas titulinis Nr. 286 atidarytas tik peržiūrai: rodomi angliški laukai, šeši angliški skaidrių pavadinimai ir `English` įrašo kalba. Originalas neišsaugotas.
- „Chrome“ viešai patikrinti titulinio darbalaukio ir mobilus (360 px) vaizdas, karjeros mobilus vaizdas, abiejų puslapių mobilus meniu ir „Escape“. Paspaudus DE karjeros puslapyje atsidarė `/de/karriere/`, `lang=de-DE`, dokumento plotis liko 360 px.
- Testiniai juodraščiai Nr. 505 ir 508 perkelti į WordPress šiukšlinę, iš kurios juos galima atkurti. Originalų Nr. 83 ir Nr. 5 turinio kontrolinės sumos prieš ir po bandymų sutapo. Titulinio SHA-256: `cbd651bb2561fc24878cca71592f328c88e0c4d313a3acfbb3be99b134ebbf25`.

Šiame papildomame etape produkcinio kodo pakeitimų nedaryta: patikrinti ankstesni pataisymai ir užfiksuotas kopijavimo apribojimas. `design:design-critique` gairės padėjo tikrinti tikrą redagavimo eigą, o ne spręsti apie veikimą vien iš redaktoriaus išvaizdos.

## Pakartojami testai

Iš projekto šaknies:

```sh
node tools/test-block-serialization.cjs
node tools/test-form-feedback.cjs
php tools/test-block-editor.php /absolute/path/to/disposable/test.sqlite
php tools/test-multilingual-site.php
git diff --check
```

Redaktoriaus PHP testui būtina atskira vienkartinė `test.sqlite` kopija. Testas atsisako naudoti įprastą projekto duomenų bazę.

`design:design-critique`, `design:accessibility-review` ir `write-human-web-copy` gairės padėjo prioritetą teikti turinio saugumui, suprantamiems veiksmams, skaitomumui bei neklaidinantiems klaidų pranešimams. Ši dalinė peržiūra nevadinama pilna WCAG atitikties patikra.
