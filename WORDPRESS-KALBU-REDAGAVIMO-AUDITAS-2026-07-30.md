# 5G TECH kalbų redagavimo auditas

**Data:** 2026-07-30  
**Audituota versija:** `5gtech-core` 0.12.0  
**Kalbos:** LT, EN, DE  
**Audito pobūdis:** kodo, vietinės duomenų bazės ir veikiančių WordPress administravimo ekranų peržiūra. Kodas ir duomenys audito metu nekeisti.

## 1. Svarbiausia išvada

Vieša svetainė turi LT, EN ir DE versijas, tačiau tai dar nėra nuosekli daugiakalbė turinio valdymo sistema.

Šiuo metu egzistuoja trys atskiri vertimo šaltiniai:

1. techniniai EN ir DE žodyno failai papildinyje;
2. vertimai, išsaugoti konkrečiame turinio modulyje;
3. vertimai, kuriuos turėtų išsaugoti puslapio ar įrašo forma.

Todėl būtina atskirti:

- **tekstas viešame EN / DE puslapyje rodomas išverstas**;
- **tekstą galima redaguoti WordPress administravime**.

Pirmas teiginys dažnai yra teisingas, antras – ne. Didelė dalis esamų vertimų lankytojui rodoma iš techninio žodyno, kurio turinio redaktorius WordPress administravime nemato.

### Dabartinė reali būklė

- LT / EN / DE skirtukai patikimai matomi tik **Turinio modulio** redaktoriuje.
- Projektų, DUK, komandos narių, darbo skelbimų, partnerių, naujienų ir įprastų puslapių redaktoriuose kalbų pasirinkimo nėra.
- Paslaugų bei specialių puslapio formų kode vertimų sąsaja numatyta, bet audituotoje veikiančioje administracijoje jos skirtukai nepasirodė.
- Duomenų bazėje nėra išsaugoto `g5tech_admin_content_translations` rinkinio. Vadinasi, specialių puslapio formų vertimai šiuo metu faktiškai remiasi pradiniu techniniu žodynu.
- Iš 39 paskelbtų turinio modulių savo vertimus duomenų bazėje turi tik 2.

Tai reiškia, kad dabartinis EN / DE vaizdas yra labiau pradinis techninis vertimo sluoksnis nei pilnai administruojamas daugiakalbis turinys.

---

## 2. Kalbų redagavimo aprėptis

Žymėjimas:

- **Veikia** – kalbą galima pasirinkti ten, kur redaguojamas originalas, ir pakeitimą gali atlikti redaktorius.
- **Dalinai** – vertimo mechanizmas kode yra arba tekstą galima keisti kitame ekrane, tačiau šaltinis ir vertimas atskirti.
- **Nėra** – WordPress administravime nėra būdo pakeisti EN / DE reikšmę.
- **Nereikia** – duomuo visoms kalboms turi būti bendras.

| Turinio šaltinis | LT / EN / DE sąsaja veikiančiame WP | Kas turi būti verčiama | Kas turi likti bendra | Įvertinimas |
|---|---:|---|---|---|
| Turinio moduliai | Taip | modulio žyma, antraštė, tekstas, dinaminio modulio matomi tekstai | tema, vieta puslapyje, ryšiai | **Dalinai** – vertimas yra antroje, konkuruojančioje vietoje |
| Paslaugos | Audito metu nematoma | pavadinimas, kategorija, aprašymas, darbai, CTA tekstai | nuotrauka, gamintojų pasirinkimai, tvarka | **Neveikia veikiančioje sąsajoje** |
| Projektai | Ne | pavadinimas, santrauka, vieta, darbai, rezultatas | metai, nuotrauka, susijusi paslauga, matomumas | **Nėra** |
| Projektų šalys | Ne | šalies pavadinimas | priskyrimas projektui | **Nėra** |
| Projektų technologijos | Ne | dažniausiai nereikia, išskyrus paaiškinimus | 2G, 3G, 4G-LTE, 5G reikšmės | **Priimtina tik dabartinėms reikšmėms** |
| DUK | Ne | klausimas ir atsakymas | tema, grupė, ryšiai su paslaugomis | **Nėra** |
| Komandos nariai | Ne | pareigos, santrauka, sritis, atsakomybės, patirtis, šalys, kompetencijos | vardas, kontaktai, metai, nuotrauka, operatorių ryšiai | **Nėra** |
| Darbo skelbimai | Ne | pavadinimas, vieta, santrauka, atsakomybės, reikalavimai, pasiūlymas | būsena, data, atlygis, techniniai pasirinkimai | **Nėra** |
| Partneriai, operatoriai ir įranga | Ne | bendrinius pavadinimus, pvz. „Optiniai tinklai“, „Matavimo įranga“ | prekių ženklus, logotipus, tipą, tvarką | **Dalinai** – prekių ženklams nereikia, bendriniams pavadinimams reikia |
| Naujienos | Ne | pavadinimas, visas blokų turinys, ištrauka, kategorijos, žymos, nuotraukų užrašai | publikavimo data, pagrindinė nuotrauka | **Nėra** |
| Įprasti WordPress puslapiai | Ne | puslapio pavadinimas ir redaguojamas blokų turinys | šablonas, pagrindinė nuotrauka | **Nėra** |
| Bendri 5G TECH nustatymai | Audito metu nematoma | rodiklių paaiškinimai, proceso tekstai, CTA, hero, adresas, šalys, sertifikatų paaiškinimai | telefonas, el. paštas, kodai, skaičiai, tvarka | **Numatyta kode, bet neveikia sąsajoje** |
| „Apie mus“ turinys | Audito metu nematoma | hero, visų sekcijų tekstai, nuotraukų užrašai, CTA | nuotraukos, sekcijų tvarka | **Numatyta kode, bet neveikia sąsajoje** |
| Karjeros puslapio turinys | Audito metu nematoma | hero, privalumai, atrankos eiga, augimo kortelės | sekcijų tvarka, nuorodų tikslai | **Numatyta kode, bet neveikia sąsajoje** |
| Mokymų puslapio turinys | Audito metu nematoma | hero, temos, įrangos ir aplinkos antraštės, CTA, nuotraukos alt tekstas | nuotrauka, įrangos pasirinkimai, tvarka | **Numatyta kode, bet neveikia sąsajoje** |
| Academy, Vadovams, Projektų vadovams, titulinio auditorijos | Audito metu nematoma | visų kortelių ir sekcijų tekstai | elementų struktūra, ryšiai, nuorodos | **Numatyta kode, bet neveikia sąsajoje** |
| Kontaktų ir kandidatavimo formos | Ne | etiketės, instrukcijos, klaidos, sėkmės pranešimai | gavėjai ir techniniai nustatymai | **Nėra redaktoriui** – valdoma techniniame žodyne |
| Privatumo ir slapukų politika | Ne | visas teisinis tekstas | – | **Nėra** |
| SEO pavadinimai ir aprašymai | Ne | meta pavadinimas, aprašymas, socialinių tinklų tekstai | nuotrauka gali būti bendra | **Nėra atskiro kalbinio redagavimo** |
| Nuotraukų alt tekstai ir užrašai | Ne | alt ir caption, kai juose yra kalbinė informacija | pats failas | **Nėra nuoseklaus modelio** |
| EN / DE adresai ir slug’ai | Ne | lokalizuotas puslapio ar įrašo adresas | objekto tapatybė | **Nėra** – adresai įrašyti kode |

---

## 3. Vietos, kuriose kalbos šiuo metu neredaguojamos

### 3.1 Kritiniai katalogo įrašai

Šie objektai yra naudojami keliuose puslapiuose, todėl jų vertimas turi gyventi pačiame įraše:

#### Projektai

Trūksta EN ir DE laukų:

- projekto pavadinimui;
- trumpam aprašymui;
- vietai;
- atliktų darbų sąrašui;
- rezultatui;
- projekto šalies pavadinimui;
- lokalizuotam adresui.

Dėl to nei Projektų puslapis, nei Patirties puslapio projektų blokai negali būti normaliai prižiūrimi visomis kalbomis.

#### DUK

Trūksta EN ir DE:

- klausimui;
- atsakymui.

Vienas DUK įrašas gali būti rodomas keliose paslaugose ir kandidatų DUK puslapyje, todėl vertimas turi būti saugomas vieną kartą pačiame DUK įraše.

#### Komandos nariai

Trūksta EN ir DE:

- pareigoms;
- trumpai santraukai;
- pagrindinei sričiai;
- atsakomybei;
- patirties aprašymui;
- šalių ir kompetencijų sąrašams.

Ši spraga matoma tituliniame, „Apie mus“, Kontaktų ir asmens profilio puslapiuose.

#### Darbo skelbimai

Trūksta EN ir DE:

- pozicijos pavadinimui;
- lygiui ar sričiai;
- darbo vietai;
- santraukai;
- atsakomybėms;
- reikalavimams;
- įmonės pasiūlymui.

Karjeros puslapio bendri tekstai ir konkretus darbo pasiūlymas dabar valdomi skirtingais mechanizmais. Net išvertus bendrą Karjeros puslapį, pats skelbimas lieka neadministruojamas kitomis kalbomis.

#### Naujienos

Naujienoms naudojamas normalus WordPress blokų redaktorius, bet nėra ryšio tarp LT, EN ir DE turinio. Trūksta:

- pavadinimo vertimų;
- atskiro EN ir DE blokų turinio;
- ištraukos;
- kategorijų ir žymų vertimų;
- nuotraukų užrašų ir alt tekstų;
- lokalizuoto adreso.

Techninis tikslių eilučių pakeitimas netinka būsimoms naujienoms, lentelėms, galerijoms ar sudėtingesniems blokams.

### 3.2 Puslapiai ir specialios formos

Šių puslapių WordPress įrašų turinį sudaro tik vienas techninis blokas, todėl įprastas puslapio redaktorius nėra realus turinio šaltinis:

- Pagrindinis;
- Kontaktai;
- Privatumo politika;
- Patirtis;
- Apie mus;
- Karjera;
- Naujienos;
- Slapukų politika;
- Kandidatuoti;
- 5GTECH Academy;
- Mokymai;
- DUK kandidatams;
- Vadovams;
- Projektų vadovams.

Dalies jų turinys valdomas atskirose formose, dalies – nustatymuose, dalies – PHP kode, o kalbinė sąsaja nėra patikimai prieinama.

### 3.3 Bendriniai katalogo pavadinimai

„Partneriai ir įranga“ viename kataloge laiko skirtingos prigimties objektus:

- neverčiamus prekių ženklus: Ericsson, Nokia, Huawei;
- verčiamus bendrinius pavadinimus: Optiniai tinklai, Matavimo įranga, Saulės moduliai, Konstrukcijos, Apsaugos įranga.

Vienoda taisyklė „šis katalogas neverčiamas“ čia netinka. Reikia atskirti prekių ženklą nuo bendrinio įrangos ar sistemos pavadinimo.

### 3.4 Taksonomijos

Šalių, naujienų kategorijų ir žymų pavadinimai neturi EN / DE redagavimo:

- `g5_project_country`;
- `category`;
- `post_tag`.

Technologijų taksonomija kol kas gali būti bendra, jei naudojami tik 2G, 3G, 4G-LTE ir 5G trumpiniai.

### 3.5 Formos, sisteminės žinutės ir kietai užkoduotas turinys

Kontaktų, kandidatavimo, paieškos, 404, teisinių puslapių ir kai kurių hero bei CTA tekstų vertimai ateina iš papildinio kalbų failų. Jie gali būti išversti viešai, tačiau jų negali pakeisti turinio redaktorius.

Tai ne turinio valdymas, o programuotojo prižiūrimas pradinis žodynas.

### 3.6 Lokalizuoti adresai

EN ir DE puslapių adresai aprašyti rankiniu būdu PHP maršrutų sąraše. WordPress administravime negalima pakeisti:

- EN ar DE puslapio slug’o;
- projekto slug’o;
- darbo skelbimo slug’o;
- komandos nario slug’o;
- naujienos slug’o.

Esamiems konkretiems įrašams dalis adresų įrašyta kode. Naujas įrašas gauna tik išverstą pirmą adreso segmentą, o jo LT slug’as lieka tas pats.

---

## 4. Kodėl esama sistema yra trapi

### 4.1 Vertimas siejamas su visu lietuvišku sakiniu

Vertimo raktas yra tikslus LT tekstas. Pataisius lietuvišką sakinį, ankstesnis EN / DE vertimas lieka susietas su sena formuluote.

Pavyzdys:

- buvo: „Aptarkime jūsų projektą.“
- tapo: „Aptarkime techninę užduotį.“

Sistemai tai du visiškai skirtingi raktai. Naujam LT tekstui gali būti vėl parodytas pradinis techninis vertimas, nors senasis buvo žmogaus pataisytas.

### 4.2 Vertimų raktų erdvė yra globali

Jeigu tas pats LT sakinys rodomas dviejose skirtingose vietose, sistema jam leidžia tik vieną EN ir vieną DE versiją. Konteksto skirtumas neišsaugomas.

Tai ypač pavojinga trumpoms etiketėms, pavyzdžiui:

- „Patirtis“;
- „Projektai“;
- „Vieta“;
- „Rezultatas“;
- „Komanda“.

### 4.3 Originalas ir vertimas valdomi skirtingose vietose

Dinaminio modulio atveju:

- LT turinys redaguojamas jo šaltinyje;
- EN / DE galima redaguoti modulio įraše;
- viešą rezultatą dar gali pakeisti techninis žodynas.

Redaktoriui sunku suprasti, kuris pakeitimas laimės ir kur ieškoti neteisingo teksto.

### 4.4 Puslapio formų kalbų sąsaja priklauso nuo JavaScript

Kode specialioms formoms paruoštas paslėptas vertimų kontekstas, o LT / EN / DE skirtukus turi sukurti administravimo JavaScript. Audito metu jie nepasirodė:

- paslaugos redaktoriuje;
- bendruose nustatymuose;
- „Apie mus“ turinyje;
- Karjeros puslapio turinyje;
- Mokymų turinyje;
- struktūriniuose Academy, Vadovų, Projektų vadovų ir titulinio auditorijų ekranuose.

Kadangi papildinio versija nekinta keičiant administravimo failus, viena galimų priežasčių yra pasenusi naršyklės asset’ų kopija. Tačiau nepriklausomai nuo konkrečios priežasties rezultatas redaktoriui tas pats – kalbų valdymo nėra.

### 4.5 Dabartinių vertimų saugojimo būklė

Audituotoje vietinėje duomenų bazėje:

- 39 paskelbti turinio moduliai;
- tik 2 moduliai turi `_g5tech_module_translations`;
- nėra `g5tech_admin_content_translations` opcijos su išsaugotais puslapio formų vertimais;
- EN ir DE techniniuose failuose yra daugiau kaip 700 pradinių porų kiekvienai kalbai.

Tai patvirtina, kad vieša daugiakalbė svetainė daugiausia remiasi failuose laikomais pradiniais vertimais, o ne WordPress administravime prižiūrimu turiniu.

---

## 5. Prioritetai

### P0 – būtina prieš perduodant svetainės valdymą klientui

1. Projektų vertimai.
2. DUK vertimai.
3. Komandos narių vertimai.
4. Darbo skelbimų vertimai.
5. Naujienų vertimai.
6. Patikimai veikiantys kalbų skirtukai paslaugose ir puslapio turinio formose.
7. EN / DE slug’ų ir SEO tekstų redagavimas.

### P1 – būtina prieš aktyvų naujo turinio kūrimą

1. Šalių, kategorijų ir žymų vertimai.
2. Bendrinių „Partneriai ir įranga“ pavadinimų vertimai.
3. Nuotraukų alt ir caption vertimai.
4. Formų, teisinių tekstų ir kitų kietai užkoduotų tekstų perkėlimas į administruojamą turinį.

### P2 – architektūros supaprastinimas

1. Atsisakyti vertimo modulio ekrane, kai originalas gyvena kitame šaltinyje.
2. Atsisakyti tikslaus LT sakinio kaip ilgalaikio vertimo rakto.
3. Palikti vieną aiškų vertimo šaltinį kiekvienam turinio objektui.

---

## 6. Priėmimo kriterijai

Daugiakalbis valdymas laikomas sutvarkytu tik tada, kai:

1. kiekviename įraše ar puslapio sekcijoje, kur yra viešas tekstas, matomi LT / EN / DE pasirinkimai;
2. originalas ir jo vertimai redaguojami tame pačiame ekrane;
3. nuotraukos, ryšiai, datos, skaičiai ir sekcijų tvarka nesidubliuoja be reikalo;
4. pakeitus LT tekstą EN / DE vertimai neprarandami;
5. naujam projektui, DUK, komandos nariui, darbo skelbimui ir naujienai nereikia programuotojo;
6. galima valdyti lokalizuotą puslapio adresą ir SEO tekstus;
7. redaktorius gali aiškiai matyti, kuri kalba neužpildyta;
8. automatinis testas patikrina ne tik viešo puslapio vertimą, bet ir visų redaguojamų laukų kalbų aprėptį;
9. vienas tekstas turi vieną aiškų duomenų šaltinį ir vieną vertimo vietą.

