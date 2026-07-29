# 5G TECH WordPress vietinio etapo testų ataskaita

Data: 2026-07-29  
Aplinka: `http://5gtech.test`

## Vizualinis palyginimas

Titulinis puslapis palygintas su patvirtintu HTML maketu šiuose pločiuose:

- 1440 × 1000 px;
- 1050 × 900 px;
- 720 × 900 px;
- 390 × 844 px.

Palyginimo vaizdai saugomi kataloge `testai/visual-qa/2026-07-29/`.

Patikrinta:

- sekcijų seka ir matomas turinys;
- 6 kolonų tinklelis;
- hero vaizdas, CTA ir rodikliai;
- desktop ir mobile antraštės variantai;
- paslaugų, proceso, ISO / SSVA, geografijos, partnerių, komandos ir naujienų sekcijos;
- subtilios įėjimo ir slinkimo animacijos;
- `prefers-reduced-motion` CSS būsena.

Titulinio administravimo scenarijai:

- pakeista ir grąžinta hero antraštė;
- išjungta ir vėl įjungta gamintojų sekcija;
- sukeista įžangos ir paslaugų sekcijų eilė;
- grąžinta patvirtinto maketo sekcijų tvarka;
- po valdymo testo pakartotinai patikrintas 1440 px vaizdas.

Projektų archyvas ir vieno projekto puslapis patikrinti 1440 px, 1050 px, 720 px ir 390 px pločiuose. Komponentas įtrauktas į dizaino sistemą, o ekrano nuotraukos saugomos tame pačiame vizualinio patikrinimo kataloge.

## Funkciniai scenarijai

### Paslaugos

- Rodomos 6 administruojamos paslaugos.
- Laikina 7-oji paslauga atsirado archyve ir po ištrynimo dingo.
- Vienos paslaugos turinys, darbai, gamintojai ir centralizuotas DUK atsinaujina iš WordPress duomenų.

### Partneriai

- Įkeltas „Ericsson“ logotipas rodomas paslaugos puslapyje.
- Išjungus „Rodyti tituliniame“, gamintojų skaičius tituliniame pasikeitė iš 9 į 8.
- Grąžinus būseną, tituliniame vėl rodomi 9 gamintojai.

### Komanda

- Viešas Aleksandro profilis atsidaro.
- Profilio neturintys nariai nukreipiami į komandos skiltį.
- Kontaktų puslapyje rodomi vadovybės, projektų ir personalo kontaktai.
- Paslėptas kontaktas viešame puslapyje nerodomas.

### Naujienos

- Paskelbti 3 įrašai rodomi archyve ir tituliniame.
- Vieną įrašą pavertus juodraščiu, abiejose vietose liko 2 kortelės.
- Vėl paskelbus įrašą, grįžo 3 kortelės.

### Darbo pozicijos

- Aktyvi pozicija rodoma karjeros puslapyje.
- Karjeros puslapio hero, darbo sąlygų, atrankos ir augimo tekstai valdomi atskiroje skiltyje **Karjera · skelbimai → Karjeros puslapis**.
- Modulių „Kodėl verta rinktis“, „Atrankos eiga“ ir „Augimas“ redagavimo mygtukai atidaro atitinkamą Karjeros turinio grupę; tik „Atviros pozicijos“ atidaro darbo skelbimų sąrašą.
- Išjungus aktyvią būseną, pozicija dingo iš sąrašo.
- Tiesioginis uždarytos pozicijos adresas nukreipė į karjeros sąrašą.
- Grąžinus aktyvią būseną, pozicija vėl rodoma.
- Administracijos meniu skiltis pavadinta „Karjera · skelbimai“.
- Pasirinkus redaguoti puslapį „Karjera“, atidaromas darbo skelbimų sąrašas.
- Sąraše iš karto matoma vieta, grupė, galiojimo data ir ar skelbimas rodomas svetainėje.
- Patikrinta naujo darbo skelbimo forma ir visi administruojami laukai.

### DUK

- Paslaugų DUK naudojamas centralizuotai.
- Kandidatų DUK suskirstytas į 4 grupes ir turi 18 klausimų.
- Pridėjus laikiną klausimą viešame puslapyje buvo 19 klausimų; pašalinus vėl 18.

### Projektai

- Sukurtas atskiras projektų turinio tipas, šalių ir technologijų pasirinkimai.
- Projektui galima priskirti vieną esamą paslaugą, vaizdą, darbų sąrašą ir rezultatą.
- Laikinas Suomijos projektas atsirado projektų archyve ir Patirties puslapyje.
- Projekto puslapis atsidarė, o Suomija buvo rodoma Patirties puslapio geografijoje.
- Išjungus viešą būseną projektas dingo iš abiejų sąrašų, o tiesioginė nuoroda nukreipė į projektų archyvą.
- Baigus testą laikinas įrašas pašalintas.

### Administravimo rolės

- Turinio redaktorius gali valdyti naujienas, paslaugas, projektus, partnerius, DUK ir bendrus 5G TECH nustatymus.
- Turinio redaktorius negali keisti komandos, darbo pozicijų, puslapių struktūros, temos ar papildinių.
- Personalo redaktorius gali valdyti komandą, darbo pozicijas ir kandidatų DUK.
- Personalo redaktorius negali keisti paslaugų, projektų, naujienų ar bendrų svetainės nustatymų.
- Abiem rolėms parengta administracijos darbo atmintinė ir tiesioginės nuorodos į leidžiamas skiltis.
- Po meniu „Komanda“ veikia „Apie mus“ tekstų, nuotraukų ir sekcijų eiliškumo valdymas; išsaugojimas ir viešo puslapio rezultatas patikrinti.
- „Apie mus“ sekcijų tvarka valdoma tuo pačiu modulių sąrašu kaip kituose puslapiuose.
- Numatytojoje modulių tvarkoje „Pagrindinė komanda“ rodoma prieš skiltį „Kokybiškas ir tvarus augimas“.
- Testinės paskyros po teisių patikrinimo pašalintos.

### Mokymų puslapio administravimas

- Sukurta atskira skiltis **5G TECH nustatymai → Mokymų puslapis**.
- Atskirais laukais valdomi hero tekstai, mokymų temos, įrangos pasirinkimas, aplinkos nuotrauka ir baigiamasis kvietimas.
- Mokymų temos įvedamos po vieną elementą eilutėje.
- Įranga ir gamintojai pasirenkami checkbox laukais iš bendro katalogo „Partneriai ir įranga“.
- Pavadinimai ir logotipai Mokymų puslapyje gaunami iš tų pačių centrinių įrašų kaip Paslaugų ir kitos svetainės skiltys.
- Mokymų programos, naudojamos įrangos ir mokymų aplinkos sekcijų tvarka keičiama bendrame puslapio modulių sąraše.
- Tame pačiame lange rodoma tikra viešo Mokymų puslapio peržiūra.
- Išsaugojimas patikrintas nekeičiant patvirtinto turinio; viešas puslapis po išsaugojimo liko korektiškas.
- Tiesioginė WordPress puslapio „Mokymai“ redagavimo nuoroda automatiškai atidaro supaprastintą valdymo formą.

### Turinio modulių biblioteka

- Sukurtas atskiras turinio tipas **Turinio moduliai**.
- Sukurtas bendras valdymo ekranas **Turinio moduliai → Puslapių moduliai**.
- Visos pagrindinės sekcijos prijungtos kaip moduliai prie 13 puslapių šablonų: Titulinio, Paslaugų, Projektų, Patirties, „Apie mus“, Karjeros, Academy, Mokymų, kandidatų DUK, Vadovų, Projektų vadovų, Kontaktų ir Naujienų.
- Bibliotekoje sukurti 38 dinaminiai moduliai, kurie naudoja esamus bendrus katalogus, nustatymus ir formas.
- „Mokymų temos“ perkeltos į centrinį modulį ir prijungtos prie Mokymų puslapio.
- Puslapiuose fiksuota palikta tik pradžia ir baigiamasis kvietimas; tarp jų esantys moduliai perrikiuojami bendru sąrašu.
- Dinaminių modulių kortelės turi tiesioginį mygtuką į tikrąjį turinio šaltinį.
- Įprasta WordPress nuoroda **Redaguoti puslapį** atidaro atitinkamo puslapio modulių valdymą.
- Patikrinta, kad pakeitus susietą modulį turinys atsinaujina visuose puslapiuose, kuriuose jis naudojamas.
- Visų palaikomų puslapių valdyme veikia mygtukas **Įkelti turinio modulį**.
- Išbandytas susieto modulio įkėlimas, perrikiavimas, pašalinimas tik iš pasirinkto puslapio ir nepriklausomos kopijos sukūrimas.
- Veiksmas **Sukurti nepriklausomą kopiją** sukuria juodraštį be puslapio priskyrimo; kopija turi savo turinį ir nebesiejama su originalo pakeitimais.
- Dinaminėms sekcijoms nepriklausoma kopija neleidžiama, nes jos sąmoningai naudoja bendrą duomenų šaltinį.
- Veiksmas **Ištrinti iš bibliotekos** pašalina modulį iš visų puslapių ir perkelia jį į šiukšlinę.
- Patikrinta, kad atkurtas modulis grįžta kaip juodraštis ir automatiškai neprijungiamas prie ankstesnių puslapių.
- Automatinis modulių valdymo testas atliko 355 patikras, įskaitant visų 38 dinaminių modulių atvaizdavimą, turinio ir personalo redaktorių teises.
- Pakartotinai patikrinta 18 pagrindinių archyvų, puslapių ir atskirų įrašų adresų; visi grąžino HTTP 200 be PHP klaidų ar įspėjimų.
- Po bandymų visi testiniai moduliai pašalinti, o pradinis susiejimas atkurtas.

### Kartotiniai puslapių elementai

- Sukurtas vienodas valdiklis kortelėms, etapams, vertybėms ir sąrašams.
- Patikrintas naujo elemento pridėjimas, pašalinimas, automatinė numeracija ir pertempimo įjungimas.
- „Apie mus“ puslapyje patikrintos 4 valdomos grupės: faktai, vertybės, strateginės kryptys ir kompetencijos.
- Karjeros puslapyje patikrintos 3 valdomos grupės: privalumai, atrankos etapai ir augimo kortelės.
- Bendruose nustatymuose patikrinti rodikliai ir projekto etapai. Titulinio proceso indikatorius prisitaiko prie realaus etapų skaičiaus.
- Skiltyje **Turinio moduliai → Puslapių sąrašai** patikrinti Academy, Vadovų, Projektų vadovų ir titulinio auditorijų valdikliai.
- Projektų vadovų įranga pasirenkama iš bendro katalogo; pradiniame variante pažymėti 5 gamintojai iš 11 galimų.
- Patikrinti vieši Academy, Vadovų, Projektų vadovų, titulinis, „Apie mus“ ir Karjeros puslapiai. Nauji duomenų šaltiniai atvaizduojami be horizontalaus slinkimo ar PHP klaidų.
- Automatinis testas laikinai prideda naują vertybę, Karjeros privalumą, bendrą rodiklį ir titulinio auditoriją, patikrina jų atvaizdavimą ir po bandymo atkuria pradinius duomenis.

### SEO, paieška ir 301

- Paslaugos puslapyje automatiškai rodomas turinio santrauką atitinkantis `meta description`.
- Patikrintos Open Graph ir X / Twitter žymos bei galiojantis JSON-LD.
- WordPress sitemap veikia, o autorių sitemap išjungtas.
- Laikinas neviešas projektas į projektų sitemap nepateko.
- Paieškos puslapyje rodomos administruojamos paslaugų santraukos.
- Paieškos ir 404 puslapiai turi `noindex`; 404 grąžina HTTP 404.
- Seni `/5gtech-telecom-academy/`, `/rinkis-mus/` ir `/karjeros-forma/` adresai grąžina HTTP 301 į naujus atitikmenis.
- Paieškos ir 404 vaizdai patikrinti 1440 px ir 390 px pločiuose; horizontalaus slinkimo nėra.

### Kontaktų forma

- Teisinga užklausa validuota ir perduota `wp_mail`.
- Patikrintas gavėjas `info@5gtech.lt` ir `Reply-To`.
- Neteisingas el. paštas grąžino aiškią klaidos būseną.
- Vietinėje aplinkoje laiškai perimami, todėl testas nesiunčia laiškų realiems gavėjams.

### Kandidatavimo forma

- Teisinga kandidatūra su PDF CV validuota ir perduota `wp_mail`.
- Gavėjas – personalo el. paštas.
- Neleistinas failo tipas atmestas.
- CV po išsiuntimo svetainėje nesaugomas.
- Produkcinėje aplinkoje dar būtinas realus SMTP pristatymo testas.

## Nuorodų patikra

Patikrinti 25 pagrindiniai vieši adresai. Visi grąžino HTTP 200:

- titulinis, paslaugų archyvas ir 6 paslaugos;
- patirtis, apie mus ir komandos profilis;
- karjera, pozicija, kandidatavimas, Academy, mokymai ir DUK;
- naujienos;
- kontaktai;
- vadovų ir projektų vadovų puslapiai;
- privatumo ir slapukų politika.
- projektų archyvas ir vieno projekto puslapis.

## Dar liko

- realaus SMTP pristatymo testas staging aplinkoje;
- kliento administravimo priėmimo testas su turinio ir personalo redaktorių rolėmis;
- galutinis senų URL eksportas ir 301 žemėlapio užbaigimas prieš paleidimą;
- daugiakalbystė ir galutinis paleidimo testas.
