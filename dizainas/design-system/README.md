# 5G TECH dizaino sistema

**Versija:** 1.4
**Data:** 2026-07-30
**Būsena:** patvirtinta kryptis, parengta vidinių puslapių dizainui ir WordPress realizacijai
**Pagrindinis šaltinis:** `dizainas/maketai/5gtech-titulinis-v1/`

Ši sistema aprašo taisykles, pagal kurias turi būti kuriamas visas naujas 5G TECH svetainės dizainas. Ji nėra vien spalvų ir šriftų sąrašas. Sistema apima tinklelio logiką, tipografijos hierarchiją, komponentus, judesį, vaizdų naudojimą, adaptyvumą ir turinio pateikimo principus.

## Failai

- `tokens.css` – kanoniniai spalvų, tipografijos, tarpų, tinklelio, formų ir animacijų kintamieji.
- `components.css` – baziniai pakartotinai naudojami komponentai.
- `index.html` – vizualus sistemos katalogas ir komponentų demonstracija.
- `README.md` – dizaino sprendimų, naudojimo ir priežiūros taisyklės.
- `../maketai/5gtech-komanda-v1/index.html` – pilno „Apie mus“ puslapio su komandos kompetencijomis šablonas.
- `../maketai/5gtech-komanda-v1/profilis.html` – atskiro komandos nario profilio šablonas.
- `../maketai/5gtech-paslauga-v1/index.html` – vienas universalus visų paslaugų puslapio šablonas.
- `../maketai/5gtech-vidiniai-v1/` – sujungtas vidinių puslapių ir bendros navigacijos prototipų rinkinys.

1.3 versijoje pridėti 404, paieškos formos ir paieškos rezultatų kortelės komponentai. Jie naudoja tą patį 6 kolonų tinklelį, tamsų hero ir linijomis atskiriamas šviesias korteles.

1.4 versijoje kalbų pasirinkimas perkeltas prie logotipo ir paverstas kompaktišku išskleidžiamu komponentu. Ramybės būsenoje rodoma tik aktyvi kalba, o kitos kalbos atveriamos užvedus, fokusuojant arba paspaudus.

## 1. Sistemos auditas

### Santrauka

**Audituotas maketas:** 5G TECH titulinis puslapis
**Komponentų grupės:** 16
**Pradinio maketo sistemos brandos įvertinimas:** 78/100

Maketas jau turėjo stiprią vizualinę kryptį: nuoseklų šešių kolonų tinklelį, aiškią tamsiai mėlyną spalvinę architektūrą, disciplinuotą tipografiją ir santūrų judesį. Didžiausia rizika buvo ne pats dizainas, o tai, kad daug reikšmių buvo įrašytos atskirai kiekviename bloke. Kuriant vidinius puslapius jos būtų pradėjusios skirtis.

### Rasti neatitikimai ir sprendimai

| Sritis | Audito išvada | Sistemos sprendimas |
|---|---|---|
| Spalvos | Naudotos kelios labai panašios pilkai mėlynos teksto spalvos | Paliktos dvi semantinės reikšmės: `text-muted` ir `text-subtle` |
| Tipografija | Daug artimų `clamp()` dydžių kiekvienai sekcijai | Sukurta šešių antraščių ir keturių tekstų dydžių skalė |
| Tarpai | Naudota daug vienkartinių 26, 30, 34, 38, 45, 55 px reikšmių | Sukurta 4 px pagrindu veikianti tarpų skalė ir semantiniai sekcijų tarpai |
| Komponentai | Mygtukai ir kortelės egzistavo tik titulinio puslapio kontekste | Aprašyti variantai, būsenos ir adaptyvus elgesys |
| Formos | Tituliniame puslapyje formų nebuvo | Pridėta į sistemą pagal tą pačią kampuotą, linijinę estetiką |
| Judesys | Animacijų trukmės buvo tinkamos, bet neįvardytos kaip tokenai | Sukurti `fast`, `standard`, `reveal`, `media` ir `hero` judesio tokenai |
| Prieinamumas | Buvo `reduced motion` palaikymas, bet ne visų komponentų būsenos | Aprašytos klaviatūros, fokuso, kontrasto ir sumažinto judesio taisyklės |
| Tinklelio tęstinumas | Keli vidinių puslapių blokai buvo savarankiškai padalyti į 4 arba 5 dalis | Pilno pločio komponentai perkelti į 6 kolonų sistemą ir jungiami tik ties pagrindinėmis tinklelio linijomis |

### Prioritetai prieš produkciją

1. Patvirtinti ir įsigyti **Gilroy web licenciją** arba visur naudoti Poppins.
2. Perkelti `tokens.css` reikšmes į WordPress temos `theme.json` ir CSS kintamuosius.
3. Pakeisti prototipo `DEMO IMAGE` iliustracijas patvirtintais vaizdais.
4. Patvirtinti sertifikatų, skaičių, operatorių ir gamintojų informaciją.
5. Sukurti mobilią navigaciją ir realias formų klaidų / sėkmės būsenas.

## 2. Vizualinė kryptis

### Pagrindinė idėja

**Inžinerinis tikslumas, patikimumas ir aiški atsakomybė.**

Dizainas neturi atrodyti nei futuristiškas, nei dekoratyvus. Technologinis įspūdis kuriamas struktūra: tinkleliu, disciplinuota tipografija, plonomis linijomis, realia darbo aplinka ir konkrečiais įrodymais.

### Penki principai

1. **Tinklelis įrėmina turinį, bet neina per tekstą ar pagrindines vizualines zonas.**
2. **Didelė tipografija naudojama tik pagrindinei minčiai.**
3. **Akcentinė spalva žymi veiksmą, progresą arba įrodymą.**
4. **Kortelės atskiriamos linijomis ir erdve, ne šešėliais.**
5. **Judesys atskleidžia informaciją, bet nėra dekoracija.**

### Ko nenaudojame

- bangos motyvo;
- stiklinių ar neoninių efektų;
- didelių užapvalintų kortelių;
- stiprių šešėlių;
- 3D technologinių dekoracijų;
- atsitiktinių gradientų;
- perteklinių animacijų;
- bendrinių „stock“ vaizdų be realaus infrastruktūros konteksto.

## 3. Spalvos

### Pagrindinės

| Tokenas | Reikšmė | Naudojimas |
|---|---:|---|
| `navy-950` | `#031F35` | poraštė, giliausias fonas, hero užtamsinimas |
| `navy-900` | `#063354` | pagrindinis prekės ženklo fonas, CTA sekcijos |
| `navy-800` | `#0A2940` | pagrindinis tekstas šviesiame fone |
| `dark-neutral` | `#11191E` | ilgos techninės / proceso sekcijos |
| `paper` | `#F4F6F7` | alternatyvus šviesus sekcijos fonas |
| `white` | `#FFFFFF` | pagrindinis paviršius ir tekstas tamsiame fone |

### Akcentai

| Tokenas | Reikšmė | Naudojimas |
|---|---:|---|
| `orange` | `#F17929` | gradiento pradžia, pirmas akcentas |
| `red` | `#F12F29` | progresas ir kritinis proceso akcentas |
| `pink` | `#EC0062` | pagrindinis UI akcentas, numeriai, fokusas |
| `berry` | `#BF0763` | gradiento pabaiga |

**Taisyklė:** viename komponente naudoti vieną akcentinę spalvą arba patvirtintą brandinį gradientą. Gradientas nėra fonas dideliems plotams – jis skirtas mažiems CTA, progreso ir prekės ženklo akcentams.

### Tekstas ir linijos

- Pagrindinis tekstas: `navy-800`.
- Antrinis tekstas: `text-muted`.
- Pagalbinis tekstas: `text-subtle`.
- Tekstas tamsiame fone: balta, antriniam tekstui – 66 % baltos.
- Šviesių sekcijų linijos: 16 % `navy-900`.
- Tinklelio linijos: 5,5 % `navy-900` arba 7 % baltos.

## 4. Tipografija

### Šriftai

- **Antraštės:** Gilroy Medium / Semibold. Kol nėra web licencijos – Poppins 500/600.
- **Tekstas ir UI:** Poppins 400/500/600/700.
- **Fallback:** DM Sans, Arial, sans-serif.

### Hierarchija

| Stilius | Dydis | Svoris | Eilučių aukštis | Naudojimas |
|---|---|---:|---:|---|
| Display XL | 44–72 px | 500 | 1.02 | puslapio hero ir baigiamasis CTA |
| Display LG | 40–64 px | 500 | svarbiausia sekcijos mintis |
| Display MD | 36–58 px | 500 | vidinio puslapio sekcijos |
| Heading LG | 28–42 px | 400/500 | proceso žingsniai |
| Heading MD | 23–34 px | 500 | paslaugos ir auditorijų eilutės |
| Heading SM | 20–26 px | 500 | naujienų kortelės |
| Body LG | 19 px | 400 | redakcinis įvadas |
| Body | 17 px | 400 | pagrindinis tekstas |
| Body SM | 15 px | 400 | kortelės aprašymas |
| Label | 11–12 px | 700 | viršantraštės, metaduomenys, numeriai |

### Taisyklės

- Antraščių raidžių tarpai neigiami: nuo `-0.035em` iki `-0.05em`.
- Didžiosios raidės naudojamos tik trumpoms žymoms.
- Teksto eilutė neturi būti ilgesnė nei 65–75 ženklai.
- Vienoje sekcijoje turi būti vienas dominuojantis tipografinis akcentas.
- Nedidinti teksto vien tam, kad užpildytų laisvą plotą – laisva erdvė yra sistemos dalis.

## 5. Tinklelis ir maketavimas

### Desktop

- Maksimalus turinio plotis: **1440 px**.
- Išorinės paraštės: **40 px**.
- Kolonos: **6 vienodo pločio kolonos**.
- Tarp kolonų nėra atskiro gutter – ribas žymi linijos.
- Vidinis komponento atitraukimas: **20 % vienos kolonos pločio**.
- Sistemoje yra septynios atskaitos linijos – šešių kolonų pradžios ir pabaigos ribos. Vizualiai jos rodomos selektyviai pagal konkrečią kompoziciją.
- Pilno pločio komponento vidinės ribos turi sutapti su viena iš septynių pagrindinių linijų.
- Hero sekcijoje gali būti rodomos visos šešios kolonos. Turinio sekcijose pagal nutylėjimą rodomos tik išorinės linijos ir konkrečios kompozicijos ribos.
- Redakcinėje antraštėje rodomos 1–2 ir 5–6 kolonų ribos; CTA bloke – riba tarp 4 ir 5 kolonų.
- Linija neturi eiti per antraštę, pastraipą, nuotrauką, formos lauką ar kortelės turinį. Komponento viduje ją pakeičia to komponento kraštinė.
- Keturi elementai vienoje juostoje dėliojami santykiu **2–1–1–2**, o penki – **2–1–1–1–1**. Jei turiniui reikia daugiau erdvės, keturi elementai dėliojami **2 × 2** tinkleliu.
- `repeat(4)` leidžiamas tik keturių pagrindinių kolonų pločio vidiniame konteineryje, kai kiekviena dalis tiksliai atitinka vieną pagrindinę koloną.
- Smulkūs vidiniai UI elementai, pavyzdžiui, formos laukų poros ar kortelės metaduomenys, gali turėti vietinį tinklelį, jei jis nekuria per visą sekciją matomos konkuruojančios vertikalios ribos.

### Tablet

- Lūžio taškas: **1050 px**.
- Išorinės paraštės: **22 px**.
- Pagrindinis šešių dalių principas išlieka, tačiau kortelės gali persigrupuoti į 2 stulpelius.

### Mobile

- Lūžio taškas: **720 px**.
- Išorinės paraštės: **16 px**.
- Turinys išdėstomas viena kolona.
- Tinklelis gali likti kaip subtili foninė tekstūra, bet neturi priversti teksto susispausti.
- Minimali interaktyvaus elemento zona: **44 × 44 px**.

### Kompozicijų šablonai

| Šablonas | Kolonų naudojimas |
|---|---|
| Vidinio puslapio hero | tekstas 1–4, vizualas arba tuščia erdvė 5–6 |
| Redakcinė sekcija | žyma 1, turinys 2–5 |
| Patirties sekcija | faktai 1–2, žemėlapis / vizualas 3–6 |
| Paslaugų tinklelis | po 2 kolonas vienai paslaugai |
| Dviejų dalių procesas | 1–3 ir 4–6 |
| Pilno pločio medija | 2–6 arba 1–6 pagal turinio svarbą |

## 6. Tarpai

Sistema remiasi 4 px žingsniu. Dažniausiai naudojamos reikšmės:

`4, 8, 12, 16, 20, 24, 28, 32, 40, 48, 56, 64, 72, 80, 96, 120, 144 px`

### Semantiniai tarpai

| Paskirtis | Desktop | Mobile |
|---|---:|---:|
| Sekcijos viršus / apačia | 120–150 px | 80 px |
| Antraštė → tekstas | 20–28 px | 16–20 px |
| Tekstas → CTA | 28–40 px | 24–32 px |
| Sekcijos antraštė → turinio tinklelis | 56–72 px | 40–48 px |
| Kortelės vidus | 28–32 px | 16–24 px |
| Elementų grupė | 12–20 px | 12–16 px |

## 7. Forma, linijos ir gylis

- Kortelės kampai: **0 px**.
- Mygtukai: pilnai apvalinti – `999px`.
- Apvalūs elementai naudojami tik ikonoms, rodyklėms ir statusams.
- Skiriamosios linijos: 1 px.
- Šešėliai nenaudojami turinio kortelėms.
- Gylis kuriamas fonų kontrastu, persidengiančiu hero užtamsinimu ir fiksuotos navigacijos `backdrop-filter`.

## 8. Komponentai

### Navigacija

**Variantai:** skaidri ant hero, tamsi slenkant, mobili su meniu mygtuku.

- Desktop aukštis: 86 px.
- Mobile aukštis: 72 px.
- „Kontaktai“ – baltas pill CTA.
- Aktyvus meniu punktas pažymimas rausva 1 px linija.
- Logotipas visada veda į pagrindinį puslapį.

### Kalbų pasirinkimas

**Vieta:** iš karto šalia logotipo.
**Ramybės būsena:** matomas tik aktyvios kalbos kodas ir krypties ženklas.
**Atverta būsena:** vertikaliame meniu rodomos tik kitos galimos kalbos.

- Desktop meniu atveriamas užvedus, fokusuojant arba paspaudus.
- Liečiamuose ekranuose meniu atveriamas paspaudus.
- `Escape` uždaro meniu ir grąžina fokusą į aktyvios kalbos valdiklį.
- Paspaudimas už komponento ribų uždaro meniu.
- Fokuso būsena žymima rausvu kontūru.
- Kalbos pasirinkimas įsimenamas, todėl pakartotinai junginėti kalbos nereikia.
- Pirmo apsilankymo kalba parenkama pagal naršyklės `Accept-Language`; neatpažintai kalbai naudojama LT.
- Komponentas nekeičia puslapio turinio struktūros ir išlaiko to paties puslapio atitikmenį kita kalba.

### Viršantraštė

Trumpa kategorijos arba sekcijos žyma su 9 × 9 px rausvu kvadratu. Naudoti vieną kartą sekcijos pradžioje. Nenaudoti ilgesniam nei dviejų eilučių tekstui.

### Mygtukai

| Variantas | Naudojimas |
|---|---|
| Primary light | pagrindinis CTA tamsiame fone |
| Dark | pagrindinis CTA šviesiame fone |
| Outline light | antrinis veiksmas tamsiame fone |
| Text link | trečinio lygio navigacija arba „Plačiau“ |

**Būsenos:** default, hover `-2 px`, focus su rožiniu kontūru, active be poslinkio, disabled 42 % opacity, loading su nekintančiu pločiu.

### Skaičių blokas

Didelis skaičius ir maža didžiosiomis raidėmis rašoma reikšmė. Skaičiai turi būti tikri, patvirtinti ir pateikti su aiškiu kontekstu.

### Paslaugos kortelė

- Desktop: 3 × 2.
- Tablet: 2 stulpeliai.
- Mobile: 1 stulpelis.
- Kortelę sudaro numeris, pavadinimas, 1–2 sakiniai ir rodyklė.
- Kortelės neturi atskiro fono ar šešėlio.

### Paslaugos puslapis

Visoms paslaugoms naudojamas vienas fiksuotas šablonas. Administratorius nekeičia maketo ir nekuria individualių blokų.

Redaguojami laukai:

1. paslaugos pavadinimas ir kategorijos žyma;
2. trumpas 2–3 sakinių aprašymas;
3. vienas pagrindinis vaizdas;
4. atliekamų darbų sąrašas;
5. pasirenkami įrangos gamintojai;
6. neprivalomi DUK.

Bendri patirties rodikliai, darbo procesas, kontaktinis CTA, navigacija ir poraštė įdedami automatiškai. Jei DUK neįrašyti, visa sekcija nerodoma.

### Standarto / sertifikato blokas

Keturi vienodi blokai tamsiame fone. Ikona, standarto kodas ir vienos eilutės paaiškinimas. Ikonos turi būti vienodo 2 px linijos storio.

### Proceso žingsnis

Numeruota žyma, aiški veiksmažodžio antraštė, trumpas paaiškinimas ir kvadratinis vizualas. Produkcijoje visi proceso vaizdai turi būti vienos rūšies: arba fotografijos, arba vienos stilistikos iliustracijos.

### Auditorijos eilutė

Visa eilutė yra nuoroda. Ji turi būti formuluojama per lankytojui aktualų rezultatą, ne per klausimą „kas jūs?“. Hover būsenoje visas fonas tampa tamsiai mėlynas.

### Naujienos kortelė

Metaduomenys, pavadinimas, trumpa ištrauka. Pirmą kortelę galima išskirti tamsiai mėlynu fonu. Produkcijoje visa kortelė turi būti nuoroda.

### Įrangos patirties juosta

**Problema:** gamintojų vardai turi veikti kaip praktinės patirties įrodymas, tačiau negali sudaryti dabartinių klientų ar oficialių partnerių įspūdžio.

**Variantai:**

| Variantas | Naudojimas |
|---|---|
| `logo` | kai gauti oficialūs, vienodos kokybės logotipų failai ir patvirtintas jų naudojimas |
| `text` | prototipui arba kai logotipų naudojimas nepatvirtintas |
| `static` | sumažinto judesio režimui ir siauriems ekranams |

**Būsenos:** default, hover / focus metu sustabdyta, reduced motion be automatinio slinkimo.

- Juostos tekstas turi aiškiai įvardyti, kad kalbama apie montuotą, integruotą arba prižiūrėtą įrangą.
- Gamintojų negalima vadinti „partneriais“ ar „klientais“ be atskiro patvirtinimo.
- Pasikartojanti techninė slenkančios juostos kopija paslepiama nuo ekrano skaitytuvų.

### Komandos kompetencijos kortelė

**Problema:** standartinė portretų galerija neparodo, kodėl konkretus žmogus sustiprina kliento projektą.

**Variantai:**

| Variantas | Naudojimas |
|---|---|
| `compact` | 3–4 žmonių anonsas tituliniame puslapyje |
| `standard` | pilnas „Apie mus / Komanda“ tinklelis |
| `featured` | pagrindinis vadovas arba techninis ekspertas |

Kortelę sudaro 4:5 portretas, pareigos, vardas, viena patirties santrauka ir ne daugiau kaip trys patvirtinti rodikliai. Visa kortelė yra nuoroda į pilną profilį.

**Būsenos:** default, hover, keyboard focus, active. Papildoma informacija negali būti pasiekiama tik užvedus pelę.

### Komandos rodiklis

Vienodas skaičiaus ir paaiškinimo komponentas naudojamas žmogaus kortelėje, pilname profilyje ir bendroje komandos statistikoje.

- Rodiklis visada turi turėti skaičiavimo apibrėžimą.
- „Objektai“, „projektai“ ir „bazinės stotys“ nėra sinonimai.
- Bendra statistika negali dubliuoti tų pačių objektų sudedant atskirų žmonių patirtį.
- Pasibaigusios kvalifikacijos neskaičiuojamos kaip aktyvios, nebent aiškiai pažymima kitaip.

### Komandos profilis

Pilnas profilis pateikiamas atskirame puslapyje arba prieinamame išskleidžiamame lange. Rekomenduojamas atskiras URL, nes jį galima nusiųsti klientui ir naudoti komerciniame pasiūlyme.

Turinio grupės:

1. vardas, pareigos ir kontaktai;
2. trumpas patirties aprašymas ir darbo srityje pradžios metai;
3. šalys bei operatorių projektų patirtis;
4. pagrindinės kompetencijos ir atsakomybės.

Sertifikatai ar kvalifikacijos pridedami tik tada, kai jie svarbūs konkretaus žmogaus profiliui. Nenaudojamos kvalifikacijų „patvirtinimo“ būsenos ar administracinės pastabos – lankytojui rodoma tik galutinė profesinė informacija.

Kontaktai, portretas ir asmeninė profesinė informacija viešinami tik gavus žmogaus sutikimą.

### Formos

- Laukai kampuoti, 1 px linija, 58 px aukštis.
- Label visada matomas; placeholder nėra label pakaitalas.
- Focus būsena – rožinė linija ir subtilus focus ring.
- Klaida rodoma tekstu ir spalva; negalima remtis vien spalva.
- Privalomi laukai pažymimi tekstu arba `*` su paaiškinimu.
- Sėkmingas pateikimas nekeičia viso puslapio konteksto – rodomas aiškus patvirtinimo blokas.

## 9. Vaizdai ir iliustracijos

### Fotografija

- Rodyti tikrą infrastruktūrą, specialistus, saugos įrangą ir mastą.
- Hero vaizde palikti tamsesnę arba vizualiai ramią zoną tekstui.
- Darbuotojų apranga turi atitikti 5G TECH identitetą.
- Vengti pozavimo biure, abstrakčių serverių ir „futuristinio 5G“ klišių.
- Fotografijoms taikyti santūrų sodrumą ir kontrastą; neperdažyti jų brandinėmis spalvomis.

### Techninės iliustracijos

- 1.5–2 px linijos.
- Tamsiai mėlyna arba balta bazinė linija.
- Rausvas akcentas tik svarbiam taškui, progresui ar patvirtinimui.
- Galima naudoti subtilų techninį kvadratų tinklelį.
- Viename bloke nemaišyti iliustracijos su fotografija.

### Žemėlapiai

- Geografija turi būti informacinė, ne dekoratyvi.
- Pažymėtos šalys privalo sutapti su patvirtinta veiklos informacija.
- Taškai ir linijos naudoja brandinius akcentus.
- Pagrindiniame puslapyje – santrauka; pilna versija gyvena `/patirtis/`.

## 10. Judesys

### Principas

Judesys turi parodyti hierarchiją arba progresą. Jei animacija nepadeda suprasti turinio, jos nereikia.

| Tokenas | Trukmė | Naudojimas |
|---|---:|---|
| Fast | 200 ms | rodyklės, underline, hover |
| Standard | 250 ms | kortelės fonas, mygtukai |
| Progress | 450 ms | proceso indikatorius |
| Reveal | 780 ms | tekstų ir kortelių pasirodymas |
| Media | 1050 ms | vaizdo atskleidimas |
| Hero | 1350 ms | pirmojo ekrano fotografija |

**Easing:** `cubic-bezier(.2, .7, .2, 1)`.

- Reveal poslinkis: iki 20 px.
- Stagger: 60–100 ms, ne daugiau 240 ms visai grupei.
- Elementas animuojamas vieną kartą.
- Navigacijos paspaudimai neturi animuotai slinkti tarp titulinio puslapio sekcijų.
- Privaloma palaikyti `prefers-reduced-motion`.

## 11. Prieinamumas

- Teksto kontrastas turi atitikti WCAG 2.1 AA.
- Fokusas visada matomas, nepašalinamas be alternatyvos.
- Visos interaktyvios kortelės pasiekiamos klaviatūra.
- Minimalus paspaudimo plotas – 44 × 44 px.
- Dekoratyvinės tinklelio linijos ir iliustracijos turi būti paslėptos nuo ekrano skaitytuvų.
- Paveikslėlių `alt` aprašo informacinę reikšmę, ne vaizdo stilių.
- Formos klaida susiejama su lauku per `aria-describedby`.
- Slinkimo ir progreso informacija negali būti prieinama tik animacija.

## 12. Turinys ir mikrocopy

### Tonas

Tikslus, ramus, kompetentingas ir įrodymais pagrįstas.

### Veiksmažodžiai

Naudoti: **įsigiliname, suplanuojame, įgyvendiname, patikriname, perduodame, projektuojame, montuojame, testuojame, dokumentuojame**.

### Vengti

- „esame geriausi“;
- „lyderiaujanti įmonė“ be įrodymo;
- „inovatyvūs sprendimai“ be paaiškinimo;
- pernelyg emocingų pažadų;
- vien techninių terminų be aiškaus rezultato klientui.

### CTA principas

CTA turi nusakyti kitą žingsnį:

- „Aptarkime jūsų projektą“
- „Peržiūrėti paslaugas“
- „Peržiūrėti patirtį ir geografiją“
- „Kaip vykdome projektus“
- „Atrasti karjeros galimybes“

## 13. Puslapių šablonai

### Paslaugos puslapis

1. Vidinio puslapio hero.
2. Problema ir rezultatas.
3. Darbų / kompetencijų sąrašas.
4. Proceso santrauka.
5. Susijusi patirtis ir įranga.
6. B2B DUK.
7. Kontaktinis CTA.

### Patirties puslapis

1. Hero su skaičiais.
2. Geografijos žemėlapis.
3. Projektų sąrašas.
4. Operatoriai ir gamintojai.
5. Sertifikatai.
6. Kontaktinis CTA.

### Apie mus

1. Hero.
2. Istorija ir kryptis.
3. Vertybės.
4. Komandos kompetencijos santrauka.
5. Komandos narių tinklelis.
6. Bendri komandos rodikliai.
7. Sertifikatai.
8. „Rinkis mus“ nuoroda.

### Karjera

1. Hero.
2. Atviros pozicijos ir filtrai.
3. Darbo sąlygos.
4. Atrankos procesas.
5. Academy ir mokymai.
6. Kandidato DUK.
7. Aplikavimo forma.

## 14. Administravimo komponentai

### Turinio kalbų skirtukai

Modulio redagavime naudojami trys vienodi skirtukai: **LT**, **EN** ir **DE**. Aktyvus skirtukas vizualiai susijungia su turinio sritimi, o neaktyvūs lieka neutralūs. Kiekviename skirtuke išlaikoma ta pati laukų seka, kad kalbos keitimas nekeistų redaktoriaus darbo modelio.

- Skirtukuose rodomi tik trumpi kalbų kodai LT, EN ir DE.
- Kalbai neutralūs nustatymai, pavyzdžiui, fonas ir modulio vieta, redaguojami tik LT skirtuke.
- Dinaminio modulio vertimo lauke visada kartu rodomas lietuviškas šaltinis.
- Skirtukai valdomi pele, lietimu ir klaviatūros rodyklėmis.

Puslapio sekcijų formose naudojamas kompaktiškas to paties komponento variantas:

- trys trumpi **LT / EN / DE** mygtukai rodomi kiekvienos tekstinės grupės pradžioje;
- vieno skirtuko pakeitimas sinchronizuoja visų to puslapio grupių kalbą;
- nuotraukos, nuorodos, pasirinkimai ir kiti kalbai neutralūs laukai lieka matomi ir bendri;
- EN arba DE režimu paslepiami kartotinio elemento pridėjimo, šalinimo ir perrikiavimo veiksmai;
- papildomos techninės būsenos ir pradinio vertimo paaiškinimai nerodomi.

## 15. Implementavimo taisyklės

### Tokenų naudojimas

Nerašyti naujo `#063354`, `28px` ar `780ms`, jei tai jau aprašyta tokenu. Naują tokeną kurti tik tada, kai reikšmė:

1. kartojasi bent trijuose komponentuose;
2. turi aiškią semantinę paskirtį;
3. negali būti išreikšta esamu tokenu.

### CSS pavyzdys

```css
.project-card {
  padding: var(--5g-space-7);
  border: var(--5g-border-width) solid var(--5g-line-light);
  color: var(--5g-color-text);
  background: var(--5g-color-white);
  transition: background var(--5g-duration-standard);
}
```

### Versijavimas

- **Patch 1.0.x:** teksto, dokumentacijos ir nekritiniai vizualiniai patikslinimai.
- **Minor 1.x:** naujas komponentas arba variantas.
- **Major x.0:** tinklelio, tipografikos ar pagrindinių spalvų lūžtantis pakeitimas.

Kiekvienas naujas komponentas turi turėti: paskirtį, variantus, būsenas, responsive elgesį, prieinamumo pastabas ir bent vieną panaudojimo pavyzdį.
