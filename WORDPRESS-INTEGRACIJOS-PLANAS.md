# 5G TECH svetainės integravimo į WordPress planas

**Parengta:** 2026-07-29  
**Esama WordPress versija projekte:** 7.0.2  
**Tikslas:** perkelti patvirtintą dizainą į WordPress taip, kad turinį galėtų saugiai administruoti netechniniai naudotojai, o WordPress branduolio ir numatytųjų temų failai liktų nepakeisti.

Detalus mažų, atskirai testuojamų darbų suskaidymas pateiktas dokumente [WORDPRESS-DARBU-PAKETAI.md](WORDPRESS-DARBU-PAKETAI.md).

Kiekvienam darbų paketui privalomi 100 % dizaino atitikties vartai. Paketas negali būti priimtas vien todėl, kad techniškai veikia: WordPress rezultatas turi būti palygintas su patvirtintu HTML maketu 1440 px, 1050 px, 720 px ir 390 px pločiuose, visi neatitikimai turi būti pataisyti ir patikra pakartota. Tiksli atitikties reikšmė, patikros seka ir būsenos aprašytos darbų paketų dokumente.

---

## 1. Pagrindinis techninis sprendimas

Kurti du atskirus 5G TECH sluoksnius:

### `5gtech` tema

Tema atsako tik už:

- `theme.json` dizaino tokenus;
- puslapių šablonus;
- antraštę ir poraštę;
- tinklelio, tipografijos ir komponentų stilius;
- adaptyvumą, prieinamumą ir animacijas;
- turinio pateikimą lankytojui.

Vieta:

```text
wordpress/wp-content/themes/5gtech/
```

Tai turi būti savarankiška nuosava tema. Neredaguoti `twentytwentythree`, `twentytwentyfour`, `twentytwentyfive` ar kitų įdiegtų temų.

### `5gtech-core` papildinys

Papildinys atsako už:

- turinio tipus;
- taksonomijas;
- laukų grupes;
- 5G TECH Gutenberg modulius;
- globalius įmonės duomenis;
- naudotojų teises;
- dinaminį sąrašų ir kortelių pateikimą;
- duomenų validavimą ir sanitizavimą.

Vieta:

```text
wordpress/wp-content/plugins/5gtech-core/
```

Turinio tipai turi būti papildinyje, ne temoje. Pakeitus dizainą ar temą, paslaugos, projektai, komandos nariai ir darbo pozicijos turi likti WordPress administracijoje.

### Failai, kurių neliečiame

Nekeisti:

- `wordpress/wp-admin/`;
- `wordpress/wp-includes/`;
- WordPress šaknies PHP failų;
- numatytųjų temų;
- trečiųjų šalių papildinių failų.

WordPress branduolys ir papildiniai atnaujinami įprastu būdu. Visas mūsų kodas gyvena tik nuosavoje temoje ir papildinyje.

---

## 2. Siūloma failų struktūra

```text
wordpress/
└── wp-content/
    ├── themes/
    │   └── 5gtech/
    │       ├── style.css
    │       ├── theme.json
    │       ├── functions.php
    │       ├── assets/
    │       │   ├── css/
    │       │   ├── js/
    │       │   ├── fonts/
    │       │   └── images/
    │       ├── parts/
    │       │   ├── header.html
    │       │   └── footer.html
    │       ├── templates/
    │       ├── patterns/
    │       └── inc/
    └── plugins/
        └── 5gtech-core/
            ├── 5gtech-core.php
            ├── includes/
            │   ├── post-types/
            │   ├── taxonomies/
            │   ├── settings/
            │   ├── capabilities/
            │   └── integrations/
            ├── blocks/
            ├── acf-json/
            └── build/
```

Kiekvienas modulis registruojamas vardų srityje `5gtech/`, pavyzdžiui:

```text
5gtech/hero
5gtech/stats
5gtech/process
5gtech/cta
```

Blokai registruojami per `block.json` ir WordPress serverio pusę. WordPress 7.0 projekte galima naudoti bendrą blokų metaduomenų manifestą.

---

## 3. Administravimo principas

Ne visi puslapiai turi būti vienodai laisvai redaguojami.

### 3.1. Moduliniai puslapiai

Tituliniame, persona landing puslapiuose ir paprastuose informaciniuose puslapiuose administratorius gali:

- pridėti leistiną sekciją;
- pašalinti nereikalingą sekciją;
- keisti sekcijų eilę;
- keisti tekstus ir mediją;
- pasirinkti, kuriuos įrašus rodyti.

Administratorius negali:

- kurti savavališkų kolonų;
- keisti spalvų;
- keisti šriftų ir jų dydžių;
- keisti tarpų;
- įterpti nepatvirtintų blokų;
- išardyti sekcijos vidinės struktūros.

### 3.2. Fiksuotos struktūros puslapiai

Paslaugos, komandos nario profilis, darbo pozicija, projektas ir naujiena turi bendrą šabloną. Administratorius pildo laukus, bet nekeičia sekcijų architektūros.

Tai sumažina klaidų tikimybę ir užtikrina, kad visi to paties tipo puslapiai atrodytų vienodai.

### 3.3. Turinio režimas

Vidinė modulio struktūra užrakinama naudojant `templateLock: "contentOnly"` arba lygiavertį blokų užrakinimą:

- redaktorius mato tekstą, vaizdą ir pasirinkimus;
- techniniai konteineriai bei tinklelis paslepiami;
- negalima netyčia ištrinti reikalingos kolonos ar pakeisti išdėstymo.

Naudotojams paliekamas tik 5G TECH blokų rinkinys ir keli saugūs WordPress blokai: pastraipa, antraštė, sąrašas, nuoroda, paveikslėlis ir lentelė.

---

## 4. Turinio tipai

| Administracijos skiltis | WordPress tipas | Pagrindinė paskirtis |
|---|---|---|
| Puslapiai | standartinis `page` | Titulinis, Apie mus, Patirtis, Kontaktai, Academy, Mokymai, persona landingai |
| Naujienos | standartinis `post` | Naujienos, projektų istorijos, techninės įžvalgos |
| Paslaugos | `g5_service` | Vienoda šešių paslaugų struktūra ir būsimos naujos paslaugos |
| Projektai | `g5_project` | Patirties įrašai, geografija, paslaugos, įranga ir galerija |
| Komanda | `g5_team` | Žmonės, kontaktai, kompetencijos, šalys ir kvalifikacijos |
| Darbo pozicijos | `g5_job` | Aktyvios pozicijos ir jų išjungimas be programuotojo |
| Partneriai ir įranga | `g5_partner` | Operatoriai, įrangos gamintojai ir logotipai |
| DUK | `g5_faq` | Klausimai pagal auditoriją, paslaugą arba karjeros temą |

Vidiniai tipų identifikatoriai trumpi, su `g5_` prefiksu ir ne ilgesni nei 20 simbolių.

### Taksonomijos

- `g5_country` – projekto ir komandos patirties šalys;
- `g5_sector` – telekomunikacijos, energetika, inžinerinės sistemos;
- `g5_partner_type` – operatorius, gamintojas, kita;
- `g5_faq_topic` – klientams, kandidatams, konkrečiai paslaugai;
- `g5_job_location` – Lietuva, Europa, biuras;
- `g5_technology` – 2G, 3G, 4G, 5G ir kitos sistemos.

---

## 5. Laukai pagal turinio tipą

### Paslauga

- pavadinimas ir trumpas aprašymas;
- hero vaizdas;
- paslaugos kategorija;
- atliekamų darbų sąrašas;
- susijusi įranga ir gamintojai;
- susiję DUK;
- susiję projektai;
- CTA tekstas ir kontaktas;
- meniu bei kortelių eiliškumas;
- publikavimo būsena.

### Projektas

- projekto pavadinimas;
- metai;
- šalis;
- paslaugų kryptys;
- technologijos;
- darbų apimtis;
- rezultatas;
- įranga;
- pagrindinis vaizdas ir galerija;
- viešumo lygis;
- susijęs operatorius arba klientas tik tada, kai galima jį viešinti.

### Komandos narys

- vardas ir pavardė;
- pareigos;
- nuotrauka;
- el. paštas ir telefonas;
- profesinė patirtis nuo;
- darbo 5G TECH pradžia;
- šalys;
- operatoriai ir projektų tipai;
- kompetencijos;
- kvalifikacijos ir atestatai;
- statistikos rodikliai;
- kortelės eiliškumas;
- rodyti arba nerodyti viešai.

### Darbo pozicija

- pareigų pavadinimas;
- vieta ir darbo pobūdis;
- atlygis, jei viešinamas;
- atsakomybės;
- reikalavimai;
- ką siūlo įmonė;
- komandiruočių ir rotacijos sąlygos;
- galiojimo data;
- kandidatavimo formos pasirinkimas;
- aktyvi arba uždaryta pozicija.

### Partneris arba įrangos gamintojas

- pavadinimas;
- logotipas;
- tipas;
- susijusios paslaugos;
- eiliškumas;
- rodyti slenkančioje eilutėje;
- alternatyvus logotipo tekstas.

### DUK

- klausimas;
- atsakymas;
- auditorija;
- susijusi paslauga;
- eiliškumas;
- publikavimo būsena.

---

## 6. Globaliai valdomi duomenys

Sukurti vieną administracijos skiltį **„5G TECH nustatymai“**:

- įmonės rekvizitai;
- pagrindiniai telefonai ir el. paštai;
- socialinių tinklų nuorodos;
- bendri įmonės skaičiai;
- ISO ir kiti sertifikatai;
- numatytasis kontaktų CTA;
- patvirtintos veiklos šalys;
- penki projekto etapai;
- poraštės nuorodos;
- elgesio kodekso PDF;
- numatytieji formų gavėjai.

Duomenys, kurie kartojasi keliuose puslapiuose, įvedami vieną kartą. Pavyzdžiui, pakeitus įgyvendintų objektų skaičių, jis turi atsinaujinti tituliniame ir Patirties puslapyje.

Techniniams nustatymams naudoti WordPress Settings API. Jei pasirenkamas ACF PRO, globaliam turiniui galima naudoti ACF Options Page, tačiau laukų struktūra turi būti saugoma kode arba versijuojamame `acf-json`.

---

## 7. Reikalingi puslapių moduliai

### Universalūs moduliai

| Modulis | Ką redaguoja administratorius | Kas lieka užrakinta |
|---|---|---|
| Hero | žyma, antraštė, tekstas, CTA, vaizdas / video | tinklelis, užtamsinimas, aukštis, tipografija |
| Redakcinis įvadas | žyma, antraštė, tekstas, medija | kolonų santykis ir tarpai |
| Skaičiai | rodikliai, paaiškinimai, eiliškumas | 6 kolonų logika ir tipografija |
| Kortelių tinklelis | kortelių turinys arba įrašų pasirinkimas | 3 × 2 ir mobilus išdėstymas |
| Procesas | žingsnių turinys arba globalios eigos pasirinkimas | numeracija, vidurio linija ir animacija |
| Medija | paveikslėlis / video, alternatyvus tekstas | formatas, proporcija ir tinklelio ribos |
| Logotipų eilutė | partnerių pasirinkimas ir eiliškumas | judėjimo greitis, tarpai ir prieinamumas |
| Žemėlapis | šalys, tekstas, vaizdas | žemėlapio kompozicija |
| Sertifikatai | pasirenkami sertifikatai | kortelių dizainas ir ikonų dydžiai |
| DUK | klausimų kategorija arba konkretūs klausimai | akordeono sąveika ir stiliai |
| CTA | antraštė, tekstas, mygtukas, kontaktas | fono ir tinklelio kompozicija |
| Tekstas | antraštės, pastraipos, sąrašai ir nuorodos | teksto plotis ir tipografijos skalė |

### Dinaminiai moduliai

Šie moduliai patys pasiima informaciją iš WordPress įrašų:

- paslaugų tinklelis;
- komandos peržiūra;
- naujausių naujienų tinklelis;
- projektų tinklelis;
- darbo pozicijų sąrašas;
- įrangos ir partnerių sąrašas;
- susijusių DUK sąrašas;
- susijusių projektų sąrašas;
- kontaktų blokas;
- formos blokas.

Administratorius pasirenka filtrą, įrašų skaičių ir eiliškumą, tačiau nekopijuoja kortelių tekstų rankiniu būdu.

### Moduliai, kurių negalima pašalinti

- svetainės antraštė;
- poraštė;
- pagrindinis puslapio `h1`;
- turinio tipo privalomi laukai;
- privatumo sutikimas formose;
- būtini formų klaidų ir sėkmės pranešimai;
- techniniai SEO ir prieinamumo elementai.

---

## 8. Šablonai

Sukurti šiuos temos šablonus:

```text
front-page.html
page.html
page-modular.html
single-g5_service.html
archive-g5_service.html
single-g5_project.html
archive-g5_project.html
single-g5_team.html
single-g5_job.html
archive-g5_job.html
home.html
single.html
archive.html
search.html
404.html
```

Pakartotinės dalys:

```text
parts/header.html
parts/footer.html
parts/mobile-navigation.html
```

Titulinio puslapio pradinis modulių rinkinys įkeliamas kaip užrakintas pradinis šablonas. Redaktorius gali išjungti arba pašalinti tik pasirenkamas sekcijas.

---

## 9. Dabartinio prototipo perkėlimas

| Dabartinis sprendimas | WordPress sprendimas |
|---|---|
| `tokens.css` | `theme.json` presetai + temos CSS kintamieji |
| `components.css` ir `shared.css` | temos komponentų ir blokų stiliai |
| Atskiruose HTML failuose kartojamas header / footer | temos template parts |
| `service-pages.js` saugomi paslaugų tekstai | `g5_service` įrašai ir jų laukai |
| JavaScript `innerHTML` generuojami puslapiai | WordPress šablonai ir serverio pusėje generuojami blokai |
| Rankomis kartojami skaičiai | globalūs nustatymai arba dinaminiai blokai |
| Rankomis kartojamos komandos kortelės | `g5_team` įrašai |
| Rankomis kartojami logotipai | `g5_partner` įrašai |
| Statinis darbo pozicijos puslapis | `g5_job` įrašas |
| Statinis DUK | `g5_faq` įrašai ir kategorijos |
| Prototipo formos | specializuotas formų papildinys ir realus laiškų pristatymas |

Esami prototipai lieka dizaino ir vizualinės regresijos etalonu. Jie nėra kopijuojami į WordPress kaip vienas didelis HTML laukas.

---

## 10. Papildinių strategija

Naudoti kuo mažiau papildinių. Kiekvienas papildinys turi turėti vieną aiškią paskirtį.

### Rekomenduojami sprendimų tipai

1. **ACF PRO** – rekomenduojamas struktūriniams laukams ir patogiai administracijai.
   - Laukų grupes laikyti `5gtech-core` papildinyje.
   - Naudoti `block.json`.
   - Nenaudoti „Flexible Content“ kaip antros, nuo Gutenberg atskirtos puslapių konstravimo sistemos.
   - Jei ACF licencijos atsisakoma, laukus ir blokų sąsajas kurti natyviai; tai pareikalaus daugiau kūrimo ir priežiūros.

2. **Formų papildinys** – kontaktų ir CV formoms.
   - Failų įkėlimas;
   - apsauga nuo šlamšto;
   - adresatai pagal formos temą;
   - aiškus duomenų saugojimo terminas;
   - duomenų eksportas ir ištrynimas;
   - SMTP integracija.

3. **Daugiakalbystė** – Polylang Pro arba kitas vienas pasirinktas sprendimas.
   - LT, EN ir DE;
   - išverčiami turinio tipai ir taksonomijos;
   - kalbai pritaikyti URL;
   - neversti skaičių ir globalių juridinių duomenų keliose vietose rankiniu būdu.

4. **SEO** – vienas SEO papildinys.

5. **Slapukų sutikimas** – vienas sprendimas, integruotas su realiai naudojamais analitikos įrankiais.

6. **Laiškų pristatymas** – SMTP arba transakcinio pašto paslauga.

Talpyklą, atsargines kopijas, ugniasienę ir vaizdų optimizavimą pirmiausia spręsti hostingo lygiu. Nedubliuoti tos pačios funkcijos keliais papildiniais.

---

## 11. Administracijos supaprastinimas

Sukurti tris praktines roles:

### Administratorius

- atnaujinimai;
- papildiniai;
- naudotojai;
- techniniai nustatymai;
- visi turinio tipai.

### Turinio redaktorius

- puslapiai;
- paslaugos;
- projektai;
- naujienos;
- DUK;
- partneriai;
- medija.

Negali keisti temos, papildinių, svetainės kodo, globalių stilių ir blokų užraktų.

### Personalo redaktorius

- komandos nariai;
- darbo pozicijos;
- kandidatavimo įrašai pagal privatumo taisykles.

Nemato techninių ir nereikalingų administracijos meniu punktų.

Papildomai:

- lietuviški laukų pavadinimai;
- trumpi pavyzdžiai po sudėtingesniais laukais;
- privalomų laukų validacija;
- logiškas laukų grupavimas į korteles;
- automatinė peržiūra prieš publikavimą;
- įrašų būsenos „Juodraštis“, „Peržiūrai“, „Paskelbta“;
- galiojimo data darbo pozicijoms;
- negalima įjungti tuščios kortelės ar nepilno profilio.

---

## 12. Saugumas ir kodo kokybė

Kiekvienam įvedamam duomeniui:

- tikrinti naudotojo teises;
- validuoti leidžiamą reikšmę;
- sanitizuoti prieš saugojimą;
- escape'inti pateikiant HTML;
- naudoti nonce būseną keičiantiems veiksmams;
- nerašyti tiesioginių SQL užklausų, kai yra WordPress API;
- nenaudoti nepatikimų failų kelių ar nepatikrintų įkėlimų.

Kodo standartai:

- WordPress Coding Standards;
- PHP_CodeSniffer;
- `eslint` ir `stylelint`;
- jokio inline JavaScript turinio generavimo;
- blokų CSS krauti tik ten, kur blokas naudojamas;
- versijuoti temos ir papildinio išleidimus;
- klaidas registruoti žurnale, nerodyti lankytojui.

---

## 13. Git ir diegimas

Versijuoti:

- `wp-content/themes/5gtech/`;
- `wp-content/plugins/5gtech-core/`;
- ACF JSON arba PHP laukų registraciją;
- kompiliavimo konfigūraciją;
- migravimo scenarijus;
- šį planą ir dizaino sistemą.

Neversijuoti:

- `wp-config.php` su slaptais duomenimis;
- `wp-content/uploads/`;
- talpyklos;
- žurnalų;
- vietinės duomenų bazės kopijų;
- trečiųjų šalių papildinių kodo, jei jie diegiami per valdomą procesą.

Naudoti tris aplinkas:

1. lokali;
2. staging;
3. production.

Visi WordPress, PHP ir papildinių atnaujinimai pirmiausia tikrinami staging aplinkoje.

---

## 14. Įgyvendinimo etapai

### 0 etapas – sprendimų patvirtinimas

- patvirtinti ACF PRO arba natyvių laukų pasirinkimą;
- pasirinkti formų, daugiakalbystės, SEO ir slapukų papildinius;
- patvirtinti naudotojų roles;
- patvirtinti, kurie titulinio moduliai privalomi;
- užšaldyti pirmos versijos turinio modelį.

**Rezultatas:** trumpas techninių sprendimų dokumentas.

### 1 etapas – temos ir papildinio karkasas

- sukurti `5gtech` temą;
- sukurti `5gtech-core` papildinį;
- perkelti spalvas, tipografiją ir tarpus į `theme.json`;
- prijungti lokalius šriftus;
- sukurti header ir footer template parts;
- parengti build ir kodo kokybės procesą.

**Rezultatas:** tuščias WordPress puslapis jau atrodo kaip 5G TECH sistema.

### 2 etapas – turinio modelis ir administracija

- registruoti turinio tipus ir taksonomijas;
- sukurti laukus;
- sukurti globalių nustatymų skiltį;
- sukurti roles ir teises;
- paslėpti nereikalingus administracijos įrankius;
- parengti juodraščio ir publikavimo eigą.

**Rezultatas:** administratorius gali suvesti realų turinį dar neturint visų puslapių dizaino.

### 3 etapas – universalūs moduliai

- hero;
- redakcinis įvadas;
- skaičiai;
- kortelių tinklelis;
- procesas;
- medija;
- logotipų eilutė;
- žemėlapis;
- sertifikatai;
- DUK;
- CTA;
- tekstinis turinys.

Kiekvienam moduliui sukurti:

- `block.json`;
- redagavimo laukus;
- serverio pusės pateikimą;
- atskirą CSS;
- prieinamumo būsenas;
- mobilią versiją;
- turinio ribojimus.

**Rezultatas:** iš leistinų modulių galima surinkti titulinį ir informacinius puslapius.

### 4 etapas – dinaminiai šablonai

- paslaugų archyvas ir viena paslauga;
- projektų archyvas ir vienas projektas;
- komandos sąrašas ir profilis;
- darbo pozicijų sąrašas ir pozicija;
- naujienų archyvas ir įrašas;
- DUK;
- paieška ir 404.

**Rezultatas:** naujas įrašas automatiškai gauna tinkamą dizainą.

### 5 etapas – formos ir integracijos

- kontaktų forma;
- kandidatavimo forma ir CV;
- adresatų taisyklės;
- SMTP;
- apsauga nuo šlamšto;
- privatumo sutikimai;
- saugojimo ir ištrynimo taisyklės;
- daugiakalbystė;
- SEO ir struktūriniai duomenys;
- slapukų sutikimas.

**Rezultatas:** veikia realūs verslo procesai, ne prototipo imitacija.

### 6 etapas – turinio migracija

- perkelti paslaugų duomenis iš `service-pages.js`;
- sukurti komandos įrašus;
- sukurti darbo pozicijas;
- perkelti DUK;
- sukelti partnerius, gamintojus ir logotipus;
- perkelti naujienas;
- sukelti vaizdus į Media Library;
- suvesti alt tekstus;
- sukurti 301 nukreipimus;
- patikrinti vidines nuorodas.

Migraciją atlikti per WordPress API, WP-CLI arba vienkartinį migravimo scenarijų. Neredaguoti duomenų bazės rankiniu būdu.

### 7 etapas – testavimas ir perdavimas

- palyginti su patvirtintais HTML prototipais;
- kiekvienam puslapiui atlikti 1440 px, 1050 px, 720 px ir 390 px vizualinį palyginimą;
- išsaugoti palyginimo ekrano nuotraukas ir uždaryti visus rastus neatitikimus;
- po kiekvienos pataisymų serijos pakartoti dizaino patikrą;
- patikrinti 6 kolonų tinklelį;
- desktop, tablet ir mobile;
- klaviatūra ir ekrano skaitytuvas;
- formų sėkmės ir klaidų būsenos;
- teisių testas su Turinio ir Personalo redaktoriais;
- neleistinų blokų testas;
- našumas ir Core Web Vitals;
- SEO, sitemap, canonical ir struktūriniai duomenys;
- atsarginių kopijų atkūrimo testas;
- WordPress atnaujinimo testas staging aplinkoje;
- trumpas administratorių mokymas;
- vieno puslapio administravimo atmintinė.

**Rezultatas:** svetainę galima perduoti klientui ir saugiai prižiūrėti.

---

## 15. Priėmimo kriterijai

Integracija laikoma baigta, kai:

1. WordPress branduolio ir numatytųjų temų failai nepakeisti.
2. Išjungus 5G TECH temą turinys lieka duomenų bazėje.
3. Administratorius gali pridėti ir pašalinti leistinus modulius.
4. Redaktorius negali sugadinti tinklelio, spalvų ar komponentų struktūros.
5. Nauja paslauga, pozicija, projektas ar komandos narys sukuriami be programuotojo.
6. Globalus skaičius arba kontaktas keičiamas vienoje vietoje.
7. Dinaminės kortelės nekopijuojamos rankomis.
8. Formos realiai siunčia duomenis ir rodo tikrą būseną.
9. Veikia LT, EN ir DE turinio ryšiai.
10. Visi puslapiai atitinka patvirtintą dizaino sistemą.
11. Svetainė išlieka veikianti po WordPress atnaujinimo staging aplinkoje.
12. Kliento redaktorius po mokymo savarankiškai paskelbia bandomą naujieną ir darbo poziciją.

---

## 16. Ko nedaryti

- Neredaguoti WordPress branduolio.
- Neredaguoti numatytosios temos.
- Nedėti turinio tipų į temos `functions.php`.
- Nenaudoti Elementor ar kito neriboto page builder, jei užduotį išsprendžia kuruojami Gutenberg moduliai.
- Nekurti viso puslapio viename WYSIWYG lauke.
- Nelaikyti paslaugų turinio JavaScript faile.
- Nekopijuoti globalių skaičių ir kontaktų į kelis puslapius.
- Neleisti klientui savavališkai valdyti spalvų, šriftų, tarpų ir kolonų.
- Nekurti tos pačios funkcijos keliuose papildiniuose.
- Nediegti ir neatnaujinti tiesiai produkcinėje svetainėje be staging patikros.

### Valdomų viso puslapio modulių pastaba

Kai puslapio maketas yra griežtai kontroliuojamas ir visas jo vaizdas generuojamas vienu dinaminiu bloku, klientui nerodomas klaidinantis bendras Gutenberg langas. Tokiam puslapiui kuriama:

- paprasta turinio forma su aiškiai pavadintais laukais;
- sąrašų įvedimas po vieną elementą eilutėje;
- nuotraukos pasirinkimas iš WordPress Media Library;
- tikra viešo puslapio peržiūra tame pačiame administravimo lange;
- automatinis perėjimas iš įprasto puslapio „Redaguoti“ veiksmo į tinkamą valdymo formą.

Ši schema pirmiausia įgyvendinta Mokymų puslapiui. Ji išlaiko dizaino sistemą nekintamą, bet leidžia turinį administruoti be techninių žinių.

---

## 17. Oficialūs techniniai šaltiniai

- [WordPress: temos struktūra](https://developer.wordpress.org/themes/core-concepts/theme-structure/)
- [WordPress: `theme.json`](https://developer.wordpress.org/themes/global-settings-and-styles/introduction-to-theme-json/)
- [WordPress: šablonai ir template parts](https://developer.wordpress.org/themes/core-concepts/templates/)
- [WordPress: blokų registravimas](https://developer.wordpress.org/block-editor/getting-started/fundamentals/registration-of-a-block/)
- [WordPress: turinio režimas ir blokų užrakinimas](https://developer.wordpress.org/block-editor/how-to-guides/curating-the-editor-experience/block-locking/)
- [WordPress: redaktoriaus funkcijų ribojimas](https://developer.wordpress.org/block-editor/how-to-guides/curating-the-editor-experience/disable-editor-functionality/)
- [WordPress: custom post types papildinyje](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/)
- [WordPress: roles ir capabilities](https://developer.wordpress.org/plugins/users/roles-and-capabilities/)
- [WordPress: Settings API](https://developer.wordpress.org/plugins/settings/settings-api/)
- [WordPress: sauga](https://developer.wordpress.org/apis/security/)
- [ACF: blokai ir `block.json`](https://www.advancedcustomfields.com/resources/blocks/)
- [ACF: Local JSON](https://www.advancedcustomfields.com/resources/local-json/)
