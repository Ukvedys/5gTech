# 5G TECH sekcijų ir turinio valdymo auditas

**Data:** 2026-07-30  
**Audituota versija:** `5gtech-core` 0.12.0  
**Audito pobūdis:** WordPress administravimo informacijos architektūros, valdiklių, duomenų šaltinių ir puslapių surinkimo logikos peržiūra. Kodas ir duomenys audito metu nekeisti.

## 1. Svarbiausia išvada

Dabartinė problema nėra vien skirtinga ekranų išvaizda. Svetainėje lygiagrečiai veikia keli skirtingi turinio valdymo modeliai, o vienas viešas puslapis dažnai surenkamas iš kelių jų.

Redaktoriui žodis „modulis“ šiuo metu gali reikšti tris skirtingus dalykus:

1. sekcijos vietą puslapyje;
2. savarankišką pakartotinai naudojamą turinį;
3. techninį apvalkalą, kuris tik parodo duomenis iš kito šaltinio.

Todėl modulių sąraše matoma sekcija nebūtinai yra vieta, kurioje redaguojamas jos turinys.

Pagrindinė taisytina priežastis:

> Vienas viešas puslapis neturi vieno aiškaus valdymo ekrano, o vienas turinys ne visada turi vieną aiškų šeimininką.

---

## 2. Šiuo metu naudojami valdymo mechanizmai

### 2.1 Įprastas WordPress blokų redaktorius

Naudojamas:

- Naujienoms;
- Puslapiams.

Naujienose jis yra tinkamas, nes leidžia kurti antraštes, tekstą, nuotraukas, galerijas ir lenteles.

Puslapiuose jis klaidina, nes daugelio puslapių turinį sudaro tik vienas techninis `g5tech/*` blokas. Atidarius tokį puslapį redaktorius nemato realių sekcijų, kurias mato lankytojas.

### 2.2 Įprastas įrašo redaktorius su savomis metadėžėmis

Naudojamas:

- Paslaugoms;
- Projektams;
- DUK;
- Komandos nariams;
- Darbo skelbimams;
- Partneriams ir įrangai.

Kiekvienas tipas turi atskirai sukurtą laukų formą. Skiriasi:

- laukų plotis;
- grupavimas;
- paaiškinimai;
- ryšių pasirinkimas;
- nuotraukų vieta;
- matomumo taisyklė;
- kalbų prieinamumas;
- kartotinių elementų būdas.

### 2.3 Bendrų nustatymų ekranas

„5G TECH nustatymai“ valdo:

- bendrus skaičius;
- projekto proceso etapus;
- numatytą CTA;
- titulinio hero tekstus;
- titulinio sekcijų matomumą ir skaitinę tvarką;
- kontaktus ir rekvizitus;
- šalių bei sertifikatų tekstinius sąrašus.

Tame pačiame ekrane sumaišyti:

- tikrai globalūs duomenys;
- tik tituliniam puslapiui priklausantis turinys;
- puslapio struktūra;
- kontaktiniai duomenys;
- tekstiniai katalogai.

### 2.4 Atskiros vieno puslapio administravimo formos

Sukurtos keturiais skirtingais keliais:

- „Apie mus“ – po Komandos meniu;
- Karjeros puslapis – po Darbo skelbimų meniu;
- Mokymų puslapis – po 5G TECH nustatymais;
- Academy, Vadovams, Projektų vadovams ir titulinio auditorijos – po Turinio modulių meniu kaip „Puslapių sąrašai“.

Šios formos turi skirtingą:

- antraštę ir navigaciją;
- sekcijų išskleidimą;
- rikiavimo logiką;
- nuotraukų parinkiklį;
- kartotinių elementų struktūrą;
- išsaugojimo kelią;
- peržiūros pateikimą.

### 2.5 Turinio modulių biblioteka

Bibliotekoje yra:

- savarankiški moduliai;
- dinaminiai moduliai, kurie tik parodo kito šaltinio duomenis;
- puslapio modulių priskyrimai ir tvarka;
- nepriklausomos kopijos;
- šalinimas iš puslapio;
- trynimas iš bibliotekos;
- dar viena EN / DE vertimų vieta.

39 paskelbti moduliai nereiškia 39 savarankiškų turinio blokų. Dauguma jų yra techniniai esamų PHP sekcijų apvalkalai.

### 2.6 Katalogai ir ryšiai

Paslaugos, projektai, komandos nariai, darbo skelbimai, DUK ir partneriai yra atskiri katalogai. Puslapiuose rodomi jų sąrašai.

Ryšiai valdomi nevienodai:

- varnelėmis;
- vienu išskleidžiamu sąrašu;
- taksonomijomis;
- daugybine post meta;
- pavadinimo teksto atpažinimu;
- kodo parinktais numatytais įrašais.

### 2.7 Kietai užkoduotos sekcijos ir tekstai

Dalies puslapių hero, CTA, formų etiketės, grupių pavadinimai ir teisiniai tekstai vis dar gyvena PHP renderinimo kode arba kalbų kataloguose.

Redaktoriui jie atrodo kaip puslapio turinys, bet WordPress administravime neturi savo valdymo vietos.

---

## 3. Vieno puslapio surinkimo pavyzdžiai

### Titulinis puslapis

Turinys ateina iš:

- „5G TECH nustatymų“;
- Paslaugų;
- Projektų;
- Partnerių ir įrangos;
- Komandos;
- Naujienų;
- struktūrinio „Titulinio auditorijų“ ekrano;
- Turinio modulių tvarkos;
- kietai užkoduoto renderinimo.

Vienam puslapiui administruoti reikia žinoti bent šešis meniu punktus.

### Apie mus

Turinys ateina iš:

- „Apie mus turinio“ formos;
- Komandos katalogo;
- bendrų rodiklių;
- Turinio modulių tvarkos;
- techninio puslapio bloko;
- dalies numatytųjų tekstų kode.

Viršuje rodomas modulių sąrašas, o žemiau – tekstai, kurie vizualiai taip pat atrodo kaip moduliai. Tai sukuria dublikato įspūdį, nors techniškai viena dalis valdo tvarką, kita – turinį.

### Karjera

Turinys ateina iš:

- Karjeros puslapio formos;
- Darbo skelbimų katalogo;
- Turinio modulių tvarkos;
- bendrų kontaktų;
- atskirų Academy, Mokymų ir DUK puslapių nuorodų.

### Mokymai

Turinys ateina iš:

- Mokymų puslapio formos;
- Partnerių ir įrangos katalogo;
- Turinio modulių bibliotekos;
- atskiro „Mokymų temos“ modulio;
- puslapio techninio bloko.

Šis puslapis turi daugiausia persidengiančių sprendimų tam pačiam turiniui.

---

## 4. Valdymo skirtumų matrica

| Sritis | Naudojami skirtingi sprendimai | Poveikis redaktoriui |
|---|---|---|
| Sekcijų tvarka | modulių drag-and-drop, skaitiniai svoriai, atskiri `section_order` masyvai, `menu_order`, fiksuota kodo tvarka | neaišku, kur keisti konkretaus puslapio tvarką |
| Sekcijos matomumas | pašalinimas iš puslapio, varnelė, įrašo būsena, atskiras aktyvumo laukas, galiojimo data, tuščias turinys | vienoda užduotis atliekama skirtingai |
| Kartotiniai elementai | kartotinis komponentas, fiksuoti numeruoti laukai, kelių eilučių tekstas, atskiri CPT įrašai | neaišku, ar galima pridėti naują elementą |
| Nuotraukos | specialusis paveikslėlis, savas media parinkiklis, puslapio featured image, kietas fallback | nuotrauka dažnai administruojama ne tame modulyje, kuriam priklauso |
| Įranga ir partneriai | keturi atskiri pasirinkimo vaizdai, tekstiniai sąrašai, katalogo įrašai | skirtingas UI ir skirtinga duomenų kokybė |
| Kalbos | techninis žodynas, modulio vertimai, puslapio formų vertimai, vietomis nieko | neaišku, kuris vertimas bus rodomas |
| Išsaugojimas | „Atnaujinti“, „Išsaugoti pakeitimus“, „Išsaugoti modulių tvarką“, WordPress `options.php` | vienam puslapiui reikia kelių išsaugojimų |
| Peržiūra | iframe, atskira nuoroda, WordPress preview, nėra peržiūros | nevienoda grįžtamojo ryšio kokybė |
| Turinio vieta | tas pats ekranas, kitas meniu, modulio biblioteka, kodas | redaktorius turi žinoti sistemos architektūrą |
| Trinimas | šiukšlinė, pašalinimas iš puslapio, trynimas iš bibliotekos, paslėpimo varnelė | didelė klaidingo veiksmo rizika |
| Adresai | WordPress slug, rankinis maršrutų katalogas, puslapio raktas | adresų valdymas atskirtas nuo turinio |

---

## 5. Kiekvienos pagrindinės svetainės dalies valdymo auditas

| Svetainės dalis | Dabartinis pagrindinis šaltinis | Papildomi šaltiniai | Pagrindinė problema |
|---|---|---|---|
| Pagrindinis | Bendri nustatymai | 5 katalogai, struktūrinė forma, moduliai, kodas | nėra vieno puslapio ekrano |
| Paslaugų sąrašas | Paslaugų CPT | moduliai, bendri nustatymai, kodas | puslapio antraštės ne kartu su sąrašu |
| Paslauga | Paslaugos metadėžė | DUK, partneriai, bendras CTA | vertimų sąsaja neveikia, forma atskira |
| Projektų sąrašas | Projektų CPT | moduliai, kodas | projektų kalbos ir puslapio antraštės nevaldomos kartu |
| Projektas | Projekto metadėžė | paslauga, šalys, technologijos | nėra kalbų, keli skirtingi ryšių valdikliai |
| Patirtis | PHP sekcijos ir bendri duomenys | projektai, partneriai, moduliai | didžioji puslapio kopija neturi aiškaus šeimininko |
| Apie mus | Apie mus opcijų forma | komanda, bendri duomenys, moduliai | struktūra ir turinys dubliuojasi ekrane |
| Komandos narys | Komandos metadėžė | partnerių operatoriai | nėra kalbų ir nuoseklaus sąrašų komponento |
| Karjera | Karjeros opcijų forma | darbo skelbimai, moduliai | vienas puslapis valdomas per du skirtingus meniu |
| Darbo skelbimas | Darbo skelbimo metadėžė | kandidatavimo forma | nėra kalbų, kitas laukų UI |
| Academy | Struktūrinė forma | moduliai, kodas | hero ir CTA ne tame pačiame modelyje |
| Mokymai | Mokymų opcijų forma | modulis, partneriai, puslapio blokas | persidengiantys turinio šaltiniai |
| DUK kandidatams | DUK katalogas | moduliai, kodo grupės | klausimai ir grupių turinys valdomi atskirai |
| Vadovams | Struktūrinė forma | moduliai, komanda, kodas | dalis turinio neturi administruojamo lauko |
| Projektų vadovams | Struktūrinė forma | partneriai, moduliai, kodas | įranga ir tekstai turi skirtingus šaltinius |
| Kontaktai | Bendri nustatymai | komanda, moduliai, formos kodas | kontaktų kortelės ir formos tekstai ne vienoje vietoje |
| Naujienų sąrašas | Naujienų CPT | moduliai, kodas | puslapio antraštės atskirtos nuo naujienų |
| Naujiena | WordPress blokų redaktorius | kategorijos, žymos, featured image | vienintelis normalus rich-content redaktorius, bet be kalbų |
| Privatumas / slapukai | techninis puslapio blokas ir kodas | bendri kontaktai | redaktorius realaus teisinio teksto nemato |
| Kandidatuoti | techninis puslapio blokas ir formos kodas | darbo skelbimai, nustatymai | formos turinys neadministruojamas kaip puslapio sekcija |

---

## 6. Kas dabartiniame sprendime yra gera ir turi būti išsaugota

1. **Katalogo principas.** Paslauga, projektas, žmogus, darbo skelbimas, DUK ir naujiena turi būti atskiri objektai, naudojami keliuose puslapiuose.
2. **Bendra modulių biblioteka.** Ji naudinga tikriems pakartotinai naudojamiems blokams.
3. **Bendri ryšiai.** Nuotraukos, įrangos pasirinkimai, sekcijų tvarka ir matomumas neturi būti dubliuojami kiekvienai kalbai.
4. **Kartotinių elementų komponentas.** Naujesniuose ekranuose jis jau leidžia pridėti, šalinti ir perrikiuoti elementus.
5. **Viešo puslapio peržiūra.** Tai gera kryptis, kurią verta standartizuoti.
6. **WordPress branduolys nepakeistas.** Visa integracija gyvena temoje ir papildinyje; šį principą būtina išlaikyti.

---

## 7. Siūlomas vieningas valdymo modelis

### 7.1 Keturi aiškūs turinio sluoksniai

#### 1. Katalogo objektai

Objektai, turintys savo puslapį arba naudojami keliuose puslapiuose:

- paslaugos;
- projektai;
- komandos nariai;
- darbo skelbimai;
- DUK;
- partneriai ir įranga;
- naujienos.

Jų tekstai ir visos kalbos redaguojami pačiame įraše.

#### 2. Bendri duomenys

Vienoje vietoje valdomi:

- kontaktai ir rekvizitai;
- bendri skaičiai;
- sertifikatai;
- šalys;
- proceso etapai;
- numatytasis CTA.

Šie duomenys naudojami keliuose puslapiuose ir niekur nekopijuojami.

#### 3. Puslapio sekcijos

Vienam puslapiui priklausantis turinys:

- hero;
- sekcijų antraštės;
- vienkartinės kortelės;
- vietiniai sąrašai;
- puslapio CTA.

Visa tai valdoma viename konkretaus puslapio ekrane.

#### 4. Pakartotinai naudojamas modulis

Bibliotekoje lieka tik tokia sekcija, kuri:

- realiai rodoma bent dviejuose puslapiuose;
- turi tą patį turinį;
- turi keistis visur vienu metu.

Dinaminis apvalkalas, kuris tik iškerpa PHP sekciją iš kito puslapio, neturi būti pristatomas redaktoriui kaip savarankiškas turinio modulis.

### 7.2 Vieno puslapio valdymo ekranas

Kiekvienas puslapis turi vieną ekraną su tokia pačia struktūra:

1. puslapio pavadinimas;
2. LT / EN / DE pasirinkimas;
3. sekcijų sąrašas su tempimu ir matomumo jungikliu;
4. išskleidžiamos sekcijų turinio kortelės ta pačia tvarka;
5. vienas nuotraukos valdiklis;
6. vienas kartotinių elementų komponentas;
7. gyva peržiūra;
8. vienas mygtukas „Išsaugoti pakeitimus“.

Hero ir baigiamasis CTA gali būti fiksuotos vietos, bet vis tiek turi būti matomi bendrame sekcijų sąraše.

### 7.3 Vienas veiksmas – vienas pavadinimas

| Veiksmas | Vienodas pavadinimas |
|---|---|
| Atidaryti puslapio valdymą | Redaguoti puslapį |
| Atidaryti katalogo šaltinį | Redaguoti: Paslaugos / Projektai / Komanda |
| Peržiūrėti viešą puslapį | Atidaryti puslapį ↗ |
| Pašalinti sekciją tik iš šio puslapio | Pašalinti iš puslapio |
| Ištrinti bendrą modulį | Ištrinti iš bibliotekos |
| Išsaugoti visą puslapį | Išsaugoti pakeitimus |

---

## 8. Rekomenduojamas sutvarkymo kelias

### 0 etapas – užfiksuoti duomenų modelį

Prieš keičiant sąsają kiekvienam viešam tekstui ir objektui priskirti vieną šeimininką:

- katalogas;
- bendri duomenys;
- puslapio sekcija;
- pakartotinai naudojamas modulis.

Rezultatas – lentelė „vieša sekcija → vienintelis duomenų šaltinis“.

### 1 etapas – vienas kalbų modelis katalogo objektams

Pradėti nuo:

1. Projektų;
2. DUK;
3. Komandos;
4. Darbo skelbimų;
5. Naujienų;
6. Paslaugų.

Originalas ir jo LT / EN / DE versijos turi būti viename įrašo ekrane. Techninis žodynas lieka tik sisteminėms sąsajos etiketėms ir kaip vienkartinis pradinių vertimų importas.

### 2 etapas – vienas puslapio redaktoriaus karkasas

Sukurti vieną bendrą administravimo karkasą ir pirmiausia pritaikyti:

1. „Apie mus“;
2. Mokymams;
3. Karjerai.

Šie trys puslapiai dabar geriausiai parodo visus reikalingus atvejus: katalogus, nuotraukas, kartotinius elementus, sekcijų tvarką, modulius ir peržiūrą.

Po kiekvieno puslapio patikrinti:

- visas kalbas;
- sekcijų tvarką;
- matomumą;
- nuotraukas;
- kartotinius elementus;
- peržiūrą;
- senų duomenų migraciją.

### 3 etapas – perkelti likusius puslapius į tą patį karkasą

Eilė:

1. Pagrindinis;
2. Patirtis;
3. Paslaugų ir Projektų sąrašai;
4. Academy;
5. Vadovams;
6. Projektų vadovams;
7. DUK kandidatams;
8. Kontaktai;
9. Naujienų sąrašas;
10. Kandidatuoti;
11. Privatumo ir slapukų politika.

### 4 etapas – išvalyti konkuruojančius mechanizmus

Tik po sėkmingos migracijos:

- pašalinti modulio vertimus dinaminiams moduliams;
- pašalinti skaitinius sekcijų svorius;
- pašalinti nebenaudojamas atskiras `section_order` opcijas;
- panaikinti kietai užkoduotą viešą turinį;
- palikti vieną media parinkiklį;
- palikti vieną partnerių pasirinkimo komponentą;
- palikti vieną kartotinių elementų komponentą;
- palikti vieną išsaugojimo ir pranešimų sistemą.

### 5 etapas – automatinis ir rankinis priėmimo testas

Kiekvienam puslapiui tikrinti:

1. LT, EN ir DE turinį;
2. visas redaguojamas sekcijas;
3. sekcijų pridėjimą, šalinimą ir rikiavimą;
4. katalogo objektų atnaujinimą visuose jų panaudojimuose;
5. nuotraukų ir ryšių išlikimą;
6. peržiūros atitikimą viešam puslapiui;
7. redaktoriaus ir personalo redaktoriaus teises;
8. mobile ir desktop rezultatą;
9. duomenų išlikimą po išsaugojimo;
10. senų ekranų ir dublikatų nebuvimą.

---

## 9. Ko nerekomenduojama daryti

1. Nedėti kalbų skirtukų po vieną į kiekvieną esamą ekraną neapsisprendus, kur gyvena originalas.
2. Nekurti dar vieno vertimų žodyno ar dar vienos modulio rūšies.
3. Neperrašyti visko vienu dideliu etapu be tarpinių testų.
4. Nedubliuoti nuotraukų, ryšių ir sekcijų tvarkos kiekvienai kalbai.
5. Neleisti redaktoriui rinktis WordPress šablonų katalogo objektų redaktoriuje.
6. Nenaudoti tuščio teksto kaip sekcijos paslėpimo mechanizmo.
7. Nelaikyti viešo teksto tik PHP faile, jei jį gali reikėti pakeisti klientui.

---

## 10. Galutinis tikslas

Redaktorius neturi žinoti:

- ar turinys techniškai saugomas opcijoje, post meta ar atskirame įraše;
- ar sekciją surenka PHP, blokas ar šablonas;
- kuris vertimų katalogas turi pirmenybę;
- kuriame meniu slepiasi konkretaus puslapio tekstas.

Jis turi:

1. pasirinkti puslapį arba katalogo objektą;
2. pasirinkti kalbą;
3. pakeisti turinį;
4. perrikiuoti ar išjungti sekciją;
5. pamatyti peržiūrą;
6. vieną kartą išsaugoti.

