# 5G TECH WordPress darbų paketai ir testavimo vartai

Šis dokumentas išskaido [WordPress integracijos planą](WORDPRESS-INTEGRACIJOS-PLANAS.md) į mažas, atskirai patikrinamas dalis.

## Darbo principas

Kiekvienas paketas turi baigtis veikiančiu rezultatu staging aplinkoje. Naujas paketas pradedamas tik tada, kai:

1. atliktas techninis patikrinimas;
2. atliktas privalomas dizaino atitikties patikrinimas;
3. turinio redaktorius atliko trumpą administravimo testą;
4. visi neatitikimai pataisyti;
5. paketo būsena pažymėta kaip priimta.

Vien ekrano nuotraukos neužtenka. Kiekvieną turinio funkciją reikia išbandyti WordPress administracijoje ir viešoje puslapio pusėje.

Patvirtintas HTML maketas ir dizaino sistema yra vienintelis vizualinis atskaitos taškas. WordPress integracijos metu negalima savarankiškai keisti sekcijų tvarkos, tekstų, komponentų, nuotraukų, tinklelio, spalvų ar sąveikų. Jei dinaminis turinys reikalauja naujo sprendimo, jis pirmiausia suprojektuojamas ir patvirtinamas, o tik tada įgyvendinamas WordPress.

### Ką šiame projekte reiškia 100 % dizaino atitiktis

100 % atitiktis reiškia, kad įgyvendintos visos patvirtinto maketo dizaino intencijos:

- tokia pati puslapio sekcijų seka ir informacijos hierarchija;
- tie patys matomi tekstai, CTA ir turinio tipai;
- tokie patys komponentų variantai, vaizdai ir jų proporcijos;
- toks pats 6 kolonų tinklelis, lygiavimas, tarpai ir turinio plotis;
- tokios pačios spalvos, tipografija, kraštinės ir būsenos;
- toks pats elgesys desktop, tablet ir mobile ekranuose;
- tokios pačios subtilios animacijos, hover ir focus būsenos;
- nėra darbinių paaiškinimų, demonstracinių užrašų ar savavališkai pridėto turinio.

Skirtingų operacinių sistemų šriftų rasterizavimo skirtumai nelaikomi dizaino neatitikimu. Visi matomi kompozicijos, dydžio, tarpo, spalvos, turinio ar elgsenos skirtumai laikomi neatitikimais ir turi būti pataisyti.

### Privalomi vartai po kiekvieno paketo

Kiekvienas paketas tikrinamas ta pačia seka:

1. **Funkcija** – veikia WordPress duomenys, nuorodos, būsenos ir administravimas.
2. **Turinys** – viešame puslapyje nėra darbinių tekstų; tekstai ir jų hierarchija sutampa su patvirtintu maketu.
3. **Desktop dizainas** – WordPress ir patvirtintas maketas palyginami 1440 px ir 1050 px pločiuose.
4. **Responsive dizainas** – palyginami 720 px ir 390 px pločiai; turinys neišeina už ekrano ir išlaiko numatytą hierarchiją.
5. **Būsenos** – patikrinami hover, focus, aktyvūs elementai, animacijos, `prefers-reduced-motion`, tušti ir ilgo turinio atvejai.
6. **Prieinamumas** – patikrinama klaviatūros navigacija, fokusas, antraščių tvarka, alternatyvūs tekstai ir kontrastas.
7. **Įrodymai** – išsaugomos keturių pločių ekrano nuotraukos ir užpildomas neatitikimų sąrašas.
8. **Pakartotinis patikrinimas** – pataisius neatitikimus pakartojami 2–7 vartai.

Paketas negali būti pažymėtas „Priimta“, jei:

- bent viename tikrinamame plotyje yra nepatvirtintas vizualinis skirtumas;
- trūksta patvirtintos sekcijos, komponento ar būsenos;
- WordPress rodo kitą tekstą ar kitą dizaino variantą nei maketas;
- administruojant turinį galima sugadinti tinklelio ar komponento struktūrą;
- neatitikimas tik pažymėtas „pataisyti vėliau“.

Jei dizainą nusprendžiama keisti, pirmiausia atnaujinamas patvirtintas HTML maketas ir dizaino sistema. Tik po patvirtinimo pakeitimas perkeliamas į WordPress ir tampa nauju atskaitos tašku.

### Kiekvieno paketo perdavimas

Su kiekvienu paketu pateikti:

- staging nuorodą;
- konkretų tikrinamą puslapį;
- nuorodą į patvirtintą maketą;
- testinio redaktoriaus paskyrą;
- 3–6 veiksmų testavimo instrukciją;
- dizaino atitikties kontrolinį sąrašą;
- 1440 px, 1050 px, 720 px ir 390 px palyginimo ekrano nuotraukas;
- patvirtinimą, kad visi rasti neatitikimai pašalinti;
- žinomų apribojimų sąrašą;
- Git pakeitimų identifikatorių;
- grįžimo į ankstesnę versiją būdą.

---

## 00. Saugi darbo aplinka

**Apimtis**

- lokali, staging ir production aplinkos;
- duomenų bazės ir medijos atsarginės kopijos;
- Git ribos;
- WordPress branduolio kontrolinis atskaitos taškas;
- klaidų žurnalas staging aplinkoje.

**Rezultatas**

Galima pradėti kurti nekeičiant veikiančios svetainės ir prireikus grįžti į ankstesnę būseną.

**Testas**

1. Atkurti bandomą duomenų bazės kopiją staging aplinkoje.
2. Patikrinti, kad `wp-admin` veikia.
3. Palyginti WordPress branduolio failus su pradine būsena.
4. Patikrinti, kad slapti duomenys nepatenka į Git.

**Priimta, kai**

- veikia atsarginės kopijos atkūrimas;
- nėra pakeistų `wp-admin`, `wp-includes` ir WordPress šaknies failų.

---

## 01. Tuščia 5G TECH tema

**Apimtis**

- `wp-content/themes/5gtech/`;
- `style.css`;
- `theme.json`;
- baziniai šablonai;
- temos įjungimas ir išjungimas.

**Rezultatas**

WordPress atpažįsta nuosavą 5G TECH temą. Tuščias puslapis atsidaro be klaidų.

**Testas**

1. Įjungti 5G TECH temą.
2. Atidaryti pagrindinį ir paprastą vidinį puslapį.
3. Išjungti temą ir laikinai įjungti numatytąją.
4. Patikrinti, kad įrašytas turinys nedingsta.

**Priimta, kai**

- nėra PHP ir naršyklės konsolės klaidų;
- tema neperrašo WordPress branduolio;
- turinys nepriklauso nuo temos įjungimo.

---

## 02. Dizaino sistemos pagrindas

**Apimtis**

- spalvos;
- šriftai;
- tipografijos skalė;
- 6 kolonų tinklelis;
- tarpai;
- mygtukai;
- formų laukai;
- bazinės šviesios ir tamsios sekcijos.

**Rezultatas**

WordPress redaktorius ir vieša svetainė naudoja tą pačią dizaino sistemą.

**Testas**

1. Atidaryti dizaino sistemos bandomąjį puslapį.
2. Palyginti su patvirtintu HTML katalogu.
3. Patikrinti 1440 px, 1050 px, 720 px ir 390 px pločius.
4. Patikrinti šriftų krovimą ir fokusavimo būsenas.

**Priimta, kai**

- pagrindiniai komponentai vizualiai sutampa su prototipu;
- tinklelis neišeina už ekrano;
- nėra savavališkų WordPress spalvų ir dydžių pasirinkimų.

---

## 03. Svetainės antraštė, meniu ir poraštė

**Apimtis**

- header;
- mobilus meniu;
- footer;
- logotipas;
- kontaktų CTA;
- globalūs kontaktiniai ir juridiniai duomenys.

**Rezultatas**

Visi puslapiai turi vienodą navigaciją ir poraštę.

**Administravimo testas**

1. Pakeisti telefono numerį vienoje nustatymų vietoje.
2. Pridėti bandomą meniu punktą.
3. Pakeisti poraštės PDF nuorodą.
4. Patikrinti pakeitimus keliuose puslapiuose.

**Priimta, kai**

- vienas pakeitimas atsiranda visose reikalingose vietose;
- desktop ir mobilus meniu veikia klaviatūra;
- redaktorius negali sugadinti header ir footer struktūros.

---

## 04. `5gtech-core` papildinio karkasas

**Apimtis**

- nuosavas papildinys;
- kodų vardų sritis;
- aktyvavimo ir išjungimo logika;
- blokų registravimo vieta;
- nustatymų ir teisių struktūra.

**Rezultatas**

Verslo logika atskirta nuo temos.

**Testas**

1. Įjungti ir išjungti papildinį.
2. Patikrinti, kad tema be papildinio parodo aiškų administracinį perspėjimą, o ne PHP klaidą.
3. Vėl įjungti papildinį ir patikrinti jo būseną.

**Priimta, kai**

- papildinys įsijungia be klaidų;
- jokie WordPress ar trečiųjų šalių failai nepakeisti.

---

## 05. Pirmas vertikalus pjūvis – viena paslauga

Tai svarbiausias ankstyvas paketas. Jis patikrina visą architektūrą nuo administravimo iki viešo puslapio.

**Apimtis**

- `g5_service` turinio tipas;
- vienos paslaugos laukai;
- paslaugos hero;
- atliekamų darbų sąrašas;
- įrangos sąrašas;
- CTA;
- vienos paslaugos šablonas.

**Rezultatas**

„Mobiliojo ryšio tinklai“ valdomi WordPress administracijoje ir atvaizduojami pagal patvirtintą dizainą.

**Administravimo testas**

1. Pakeisti paslaugos antraštę.
2. Pridėti darbų sąrašo eilutę.
3. Pašalinti vieną įrangos elementą.
4. Pakeisti hero vaizdą.
5. Išsaugoti juodraštį ir peržiūrėti.
6. Paskelbti puslapį.

**Priimta, kai**

- visus veiksmus gali atlikti Turinio redaktorius;
- tinklelis ir dizainas nesikeičia nuo skirtingo teksto ilgio;
- išjungus temą paslaugos duomenys lieka WordPress.

---

## 06. Paslaugų sąrašas ir likusios paslaugos

**Apimtis**

- paslaugų archyvas;
- dinaminės kortelės;
- eiliškumas;
- šeši esami paslaugų įrašai;
- galimybė pridėti septintą paslaugą.

**Dizaino atskaitos taškas**

- `dizainas/maketai/5gtech-vidiniai-v1/paslaugos.html`;
- `dizainas/design-system/`;
- patvirtinti tekstai ir paslaugų eiliškumas.

**Dizaino atitikties darbai**

1. Atkurti patvirtintą tamsų paslaugų hero, jo aukštį, tekstą ir lygiavimą.
2. Paslaugų sąrašą rodyti tekstinių kortelių tinklelyje be savavališkai pridėtų nuotraukų: 3 kolonos virš 1050 px, 2 kolonos iki 1050 px ir 1 kolona iki 720 px.
3. Iš WordPress dinamiškai rodyti numerį, pavadinimą, trumpą aprašymą ir nuorodą, nekeičiant kortelės struktūros.
4. Grąžinti patvirtintą ISO 9001, ISO 14001, ISO 45001 ir SSVA sekciją, naudojant globalius duomenis.
5. Grąžinti patvirtintą baigiamąjį kontaktų CTA.
6. Patikrinti 1440 px, 1050 px, 720 px ir 390 px pločius šalia patvirtinto HTML maketo.
7. Pataisius visus skirtumus atlikti pakartotinį vizualinį ir administravimo testą.

**Testas**

1. Sukurti bandomą septintą paslaugą.
2. Pakeisti jos eiliškumą.
3. Išjungti publikavimą.
4. Patikrinti, ar kortelė automatiškai atsiranda ir dingsta.
5. Palyginti hero, paslaugų korteles, ISO / SSVA ir CTA sekcijas su patvirtintu maketu keturiuose pločiuose.
6. Patikrinti, kad septinta paslauga pradeda naują tinklelio eilutę ir nesukuria naujo savavališko dizaino.

**Priimta, kai**

- kortelių nereikia kopijuoti rankiniu būdu;
- paslaugos naudoja vieną bendrą šabloną;
- yra visos patvirtinto maketo sekcijos;
- nėra nepatvirtintų nuotraukų, tekstų ar komponentų;
- visi keturi palyginimo pločiai atitinka patvirtintą dizainą;
- dizaino atitikties kontroliniame sąraše neliko atvirų punktų.

**2026-07-29 audito rezultatas**

Dabartinė WordPress versija neatitinka patvirtinto maketo: pakeistas hero, tekstas, paslaugų kortelių kompozicija ir stulpelių skaičius, nerodomi trumpi aprašymai, ISO / SSVA bei baigiamojo CTA sekcijos. Tai yra įgyvendinimo neatitikimas, o ne WordPress apribojimas. Paketas grąžintas į būseną „Taisoma“.

---

## 07. Pirmasis modulinių sekcijų rinkinys

**Moduliai**

- hero;
- redakcinis įvadas;
- teksto sekcija;
- medija;
- CTA.

**Rezultatas**

Iš penkių leistinų modulių galima sukurti paprastą landing puslapį.

**Administravimo testas**

1. Sukurti naują puslapį iš pradinio šablono.
2. Pridėti medijos modulį.
3. Perkelti CTA aukščiau.
4. Pašalinti redakcinį įvadą.
5. Pakeisti tekstus ir vaizdą.

**Priimta, kai**

- modulius galima pridėti, pašalinti ir perkelti;
- jų vidinės kolonos, spalvos ir tarpai lieka užrakinti;
- redaktorius nemato nereikalingų Gutenberg blokų.

---

## 08. Patikimumo moduliai

**Moduliai**

- skaičiai;
- sertifikatai;
- penki projekto etapai;
- darbo standartų sekcija.

**Rezultatas**

Globalūs pasitikėjimo duomenys valdomi vienoje vietoje ir naudojami keliuose puslapiuose.

**Testas**

1. Pakeisti vieną bendrą skaičių.
2. Išjungti vieną sertifikatą.
3. Pakeisti vieno proceso etapo paaiškinimą.
4. Patikrinti Titulinį, Patirties ir Paslaugos puslapius.

**Priimta, kai**

- globalių duomenų nereikia taisyti keliuose puslapiuose;
- tuščias arba išjungtas elementas nepalieka tarpo.

---

## 09. Partneriai ir įrangos gamintojai

**Apimtis**

- `g5_partner`;
- tipas „operatorius“ arba „gamintojas“;
- logotipas;
- eiliškumas;
- slenkanti logotipų eilutė;
- paprastas logotipų tinklelis.

**Testas**

1. Pridėti naują gamintoją.
2. Įkelti jo logotipą ir alternatyvų tekstą.
3. Įjungti rodymą tituliniame.
4. Pakeisti eiliškumą.
5. Išjungti įrašą.

**Priimta, kai**

- tas pats įrašas gali būti rodomas keliuose moduliuose;
- animacija sustoja pagal `prefers-reduced-motion`;
- logotipai neįrašyti rankomis į HTML.

---

## 10. Projektai, geografija ir žemėlapis

**Apimtis**

- `g5_project`;
- šalies ir technologijos taksonomijos;
- projekto kortelė;
- projekto puslapis;
- projektų sąrašas;
- veiklos žemėlapis.

**Testas**

1. Sukurti projektą Suomijoje.
2. Priskirti paslaugą ir technologiją.
3. Įkelti vaizdą.
4. Patikrinti projekto kortelę ir puslapį.
5. Patikrinti, ar Suomija atsiranda žemėlapyje.
6. Išjungti projektą.

**Priimta, kai**

- projektas automatiškai maitina Patirties puslapį;
- neviešinami duomenys nepatenka į kortelę ar struktūrinius duomenis.

---

## 11. Komanda ir individualus profilis

**Apimtis**

- `g5_team`;
- komandos kortelė;
- profilio puslapis;
- kontaktai;
- kompetencijos;
- šalys;
- kvalifikacijos;
- komandos peržiūra tituliniame.

**Testas**

1. Sukurti bandomą komandos narį.
2. Pakeisti pareigas ir nuotrauką.
3. Pridėti kompetenciją.
4. Išjungti viešą kontaktą.
5. Pakeisti kortelės eiliškumą.
6. Išjungti žmogaus rodymą viešai.

**Priimta, kai**

- nereikia kurti profilio puslapio rankiniu būdu;
- paslėptas laukas nepalieka tuščios antraštės;
- asmens duomenys rodomi tik pagal pasirinktą viešumo būseną.

---

## 12. Naujienos

**Apimtis**

- standartiniai WordPress įrašai;
- naujienų archyvas;
- įrašo puslapis;
- naujienų kortelės;
- kategorijos;
- titulinio naujienų peržiūra.

**Testas**

1. Sukurti juodraštį.
2. Įkelti pagrindinį vaizdą.
3. Paskelbti naujieną.
4. Patikrinti archyvą ir titulinį.
5. Suplanuoti kitą publikavimo laiką.

**Priimta, kai**

- naujienai nereikia programuotojo;
- ištrauka ir vaizdas teisingai rodomi visose kortelėse.

---

## 13. Darbo pozicijos

**Apimtis**

- `g5_job`;
- pozicijų sąrašas;
- vienos pozicijos puslapis;
- lokacija;
- galiojimo data;
- darbo sąlygos;
- aktyvi arba uždaryta būsena.

**Testas**

1. Sukurti naują poziciją.
2. Nustatyti galiojimo datą.
3. Paskelbti ir patikrinti sąraše.
4. Pakeisti būseną į uždarytą.
5. Patikrinti, kad uždaryta pozicija neberodoma sąraše.

**Priimta, kai**

- personalo darbuotojas savarankiškai valdo pozicijas;
- pasibaigusi pozicija nepriima kandidatūrų.

---

## 14. DUK

**Apimtis**

- `g5_faq`;
- temos ir auditorijos;
- DUK blokas;
- klausimų pasirinkimas arba automatinis filtravimas.

**Testas**

1. Sukurti klausimą kandidatams.
2. Sukurti klausimą konkrečiai paslaugai.
3. Patikrinti, kad kiekvienas rodomas tik tinkamoje vietoje.
4. Pakeisti eiliškumą.

**Priimta, kai**

- vienas klausimas gali būti panaudotas keliuose puslapiuose;
- nereikia kopijuoti atsakymo į atskirus puslapius.

---

## 15. Kontaktų ir kandidatavimo formos

**Apimtis**

- kontaktų forma;
- kandidatavimo forma;
- CV įkėlimas;
- gavėjai;
- SMTP;
- apsauga nuo šlamšto;
- privatumo sutikimas;
- klaidų ir sėkmės būsenos;
- duomenų saugojimo ir ištrynimo taisyklės.

**Testas**

1. Išsiųsti teisingą kontaktų užklausą.
2. Patikrinti laiško gavimą ir Reply-To.
3. Pateikti kandidatūrą su leidžiamu CV.
4. Bandyti neleidžiamą failo tipą.
5. Bandyti formą be privalomo sutikimo.
6. Ištrinti bandomojo kandidato duomenis.

**Priimta, kai**

- laiškai realiai pristatomi;
- lankytojui nerodoma klaidinga sėkmė;
- asmens duomenys turi aiškų saugojimo procesą.

---

## 16. Titulinio puslapio surinkimas

**Apimtis**

- patvirtintos titulinio sekcijos;
- dinaminės paslaugos;
- skaičiai;
- procesas;
- partneriai;
- komanda;
- naujienos;
- žemėlapis;
- baigiamasis CTA.

**Testas**

1. Išjungti pasirenkamą sekciją.
2. Pakeisti dviejų sekcijų eilę.
3. Pakeisti hero tekstą ir vaizdą.
4. Patikrinti, kad dinaminės kortelės atsinaujina.
5. Patikrinti visą puslapį keturiuose ekrano pločiuose.

**Priimta, kai**

- administratorius valdo turinį, bet negali sugadinti dizaino;
- puslapis vizualiai atitinka patvirtintą prototipą.

---

## 17. Daugiakalbystė

Šį paketą pradėti tik tada, kai LT turinio struktūra stabili. Kitaip kiekvienas modelio pakeitimas būtų kartojamas tris kartus.

**Apimtis**

- LT, EN ir DE;
- turinio tipų ryšiai;
- meniu;
- globalūs duomenys;
- formos;
- kalboms pritaikyti URL.

**Testas**

1. Išversti vieną paslaugą į EN ir DE.
2. Patikrinti kalbos perjungimą.
3. Patikrinti neišverstą įrašą.
4. Pakeisti bendrą skaičių.
5. Patikrinti meniu, formą ir 404 kiekviena kalba.

**Priimta, kai**

- kalbų versijos nėra savarankiškos, nesusietos puslapių kopijos;
- kalbos perjungimas veda į atitinkamą turinį.

---

## 18. SEO, URL ir 301 nukreipimai

**Apimtis**

- title ir meta description;
- canonical;
- sitemap;
- socialinių tinklų vaizdai;
- struktūriniai duomenys;
- 301 žemėlapis;
- paieška;
- 404.

**Testas**

1. Atidaryti seną paslaugos URL.
2. Patikrinti vieną paslaugą, naujieną, poziciją ir projektą.
3. Patikrinti puslapį be pagrindinio vaizdo.
4. Patikrinti neišverstą turinį.
5. Patikrinti sitemap.

**Priimta, kai**

- seni adresai neprarandami;
- nėra pasikartojančių title ir canonical;
- juodraščiai nepatenka į sitemap.

---

## 19. Turinio migracija

Migruoti dalimis, ne visą svetainę vienu veiksmu.

### 19A – paslaugos ir globalūs duomenys

- šešios paslaugos;
- skaičiai;
- sertifikatai;
- kontaktai;
- procesas.

### 19B – komanda, partneriai ir projektai

- žmonės;
- operatoriai;
- gamintojai;
- geografija;
- projektai.

### 19C – karjera ir DUK

- darbo pozicijos;
- Academy;
- mokymai;
- kandidatų DUK.

### 19D – naujienos, teisiniai ir pagalbiniai puslapiai

- naujienos;
- privatumo politika;
- slapukai;
- kontaktai;
- 404.

**Kiekvienos dalies testas**

1. Palyginti įrašų skaičių prieš ir po migracijos.
2. Atidaryti bent tris skirtingus įrašus.
3. Patikrinti vaizdus, nuorodas ir specialiuosius simbolius.
4. Patikrinti, kad migraciją galima saugiai pakartoti.

**Priimta, kai**

- migracija neatlieka dublikatų;
- nereikia rankomis taisyti duomenų bazės.

---

## 20. Administracijos supaprastinimas ir mokymas

**Apimtis**

- Turinio redaktoriaus rolė;
- Personalo redaktoriaus rolė;
- nereikalingų meniu paslėpimas;
- laukų instrukcijos;
- juodraščio ir peržiūros eiga;
- trumpa administravimo atmintinė.

**Kliento testas**

Turinio redaktorius be programuotojo pagalbos turi:

1. pakeisti telefono numerį;
2. paskelbti naujieną;
3. pridėti partnerio logotipą;
4. išjungti titulinio sekciją.

Personalo redaktorius turi:

1. paskelbti darbo poziciją;
2. ją uždaryti;
3. pakeisti komandos nario kontaktą.

**Priimta, kai**

- visi veiksmai atliekami pagal vieno puslapio atmintinę;
- redaktoriai nemato dizaino ir sistemos nustatymų, kurių neturi keisti.

---

## 21. Galutinis testas ir paleidimas

**Apimtis**

- visų ankstesnių testų pakartojimas;
- visų puslapių galutinis palyginimas su patvirtintais maketais;
- prieinamumas;
- našumas;
- saugumas;
- realių laiškų pristatymas;
- atsarginės kopijos;
- WordPress atnaujinimas staging aplinkoje;
- paleidimo ir grįžimo planas.

**Priėmimo scenarijai**

1. B2B lankytojas randa paslaugą, patirtį ir pateikia užklausą.
2. Kandidatas randa poziciją ir pateikia CV.
3. Redaktorius paskelbia naujieną.
4. Personalo redaktorius uždaro poziciją.
5. Administratorius pakeičia globalų kontaktą.
6. WordPress atnaujinamas nepakeičiant mūsų temos ir papildinio.
7. Atkuriama staging kopija.
8. Kiekvienas viešas puslapis palyginamas su patvirtintu maketu 1440 px, 1050 px, 720 px ir 390 px pločiuose.
9. Pakeitus dinaminį turinį patikrinama, kad dizaino tinklelis, tarpai ir komponentai lieka nepakitę.

**Priimta, kai**

- nėra kritinių ar aukšto prioriteto klaidų;
- visi pagrindiniai scenarijai veikia mobile ir desktop;
- visų puslapių dizaino atitikties sąrašai uždaryti be atvirų punktų;
- nėra nepatvirtintų nukrypimų nuo maketų;
- galima grįžti į prieš paleidimą buvusią versiją.

---

## Rekomenduojama pirmoji testavimo seka

Pirmam etapui nereikia statyti visos svetainės. Pakanka šių paketų:

```text
00 → 01 → 02 → 03 → 04 → 05
```

Po 05 paketo jau galima įvertinti:

- ar tema integruojasi nekeičiant WordPress branduolio;
- ar pasirinktas laukų sprendimas patogus klientui;
- ar 6 kolonų dizainas tiksliai atkuriamas;
- ar redaktorius gali keisti turinį nesugadindamas maketo;
- ar turinio ir dizaino atskyrimas veikia praktiškai.

Tik priėmus pirmą vertikalų paslaugos pjūvį verta masiškai kurti likusius modulius ir migruoti turinį.

---

## Būsenų lentelė

Galimos dizaino atitikties būsenos: `Neaudituota`, `Neaktualu`, `Neatitinka`, `Tikrinama`, `100 %`.

| Paketas | Būsena | Dizaino atitiktis | Staging nuoroda | Kas testavo | Pastabos |
|---|---|---|---|---|---|
| 00 Saugi aplinka | Vykdoma | Neaktualu | http://5gtech.test | Codex | Vietinė SQLite aplinka veikia; produkcinės kopijos ir atkūrimo testas bus atliekami prieš paleidimą. |
| 01 Tema | Testuojama | 100 % | http://5gtech.test | Codex | Nuosava tema veikia nekeičiant WordPress branduolio. Galutinis priėmimas – su 21 paketu. |
| 02 Dizaino sistema | Testuojama | 100 % | http://5gtech.test | Codex | Patvirtinti tokenai, 6 kolonų gridas ir responsive taisyklės perkelti į temą. |
| 03 Header ir footer | Testuojama | 100 % | http://5gtech.test | Codex | Desktop ir mobile variantai patikrinti; titulinio mobile antraštė atitinka atskirą patvirtintą variantą. |
| 04 Core papildinys | Testuojama | Neaktualu | http://5gtech.test/wp-admin/ | Codex | Verslo logika laikoma nuosavame papildinyje; WordPress branduolys nekeičiamas. |
| 05 Viena paslauga | Testuojama | 100 % | http://5gtech.test/paslaugos/mobiliojo-rysio-tinklai/ | Codex | Puslapis ir administravimo laukai sutikrinti su patvirtintu paslaugos maketu. |
| 06 Visos paslaugos | Testuojama | 100 % | http://5gtech.test/paslaugos/ | Codex | Ankstesni hero, kortelių, ISO / SSVA ir CTA neatitikimai pašalinti; 6 ir 7 kortelių scenarijai patikrinti. |
| 07 Baziniai moduliai | Testuojama | 100 % | http://5gtech.test/wp-admin/admin.php?page=g5tech-settings | Codex | Globalūs rodikliai, procesas, CTA ir kontaktai naudojami visuose reikalinguose puslapiuose. |
| 08 Patikimumo moduliai | Testuojama | 100 % | http://5gtech.test/patirtis/ | Codex | Rodikliai, šalys, ISO / SSVA ir partnerių grupės yra dinaminės. |
| 09 Partneriai | Testuojama | 100 % | http://5gtech.test/wp-admin/edit.php?post_type=g5_partner | Codex | Centralizuoti įrašai, logotipai ir titulinio rodymo būsena patikrinti. |
| 10 Projektai ir žemėlapis | Testuojama | 100 % | http://5gtech.test/projektai/ | Codex | Projektų katalogas, šalys, technologijos, susijusi paslauga, kortelė, puslapis ir automatinė peržiūra Patirties puslapyje veikia. |
| 11 Komanda | Testuojama | 100 % | http://5gtech.test/apie-mus/ | Codex | Komandos sąrašas, kontaktų matomumas ir individualus profilis veikia. |
| 12 Naujienos | Testuojama | 100 % | http://5gtech.test/naujienos/ | Codex | Naudojami standartiniai WordPress įrašai; juodraščio ir titulinio atsinaujinimo scenarijai patikrinti. |
| 13 Darbo pozicijos | Testuojama | 100 % | http://5gtech.test/karjera/ | Codex | Aktyvi, uždaryta ir nukreipimo būsenos patikrintos. |
| 14 DUK | Testuojama | 100 % | http://5gtech.test/duk/ | Codex | Paslaugų ir kandidatų klausimai centralizuoti; vienas įrašas gali būti naudojamas keliose vietose. |
| 15 Formos | Testuojama | 100 % | http://5gtech.test/kontaktai/ | Codex | Kontaktų ir kandidatavimo formos, CV validacija, sutikimai ir klaidų būsenos veikia. Produkciniam priėmimui dar reikia SMTP pristatymo testo. |
| 16 Titulinis | Testuojama | 100 % | http://5gtech.test/ | Codex | WordPress ir maketas palyginti 1440, 1050, 720 ir 390 px pločiuose; hero tekstai, nuotrauka, sekcijų rodymas ir eiliškumas valdomi administracijoje. |
| 17 Kalbos | Nepradėta | Neaudituota |  |  |  |
| 18 SEO ir nukreipimai | Testuojama | 100 % | http://5gtech.test/wp-sitemap.xml | Codex | Automatiniai aprašymai, socialinių tinklų žymos, struktūriniai duomenys, filtruojamas sitemap, firminė paieška ir 404 bei 3 patvirtinti senų URL nukreipimai veikia. Galutinis 301 sąrašas tikrinamas prieš paleidimą. |
| 19 Migracija | Vykdoma | Neaktualu | http://5gtech.test/paslaugos/ | Codex | Perkeltos paslaugos, komanda, partneriai, DUK, naujienos, karjera, teisiniai ir baziniai globalūs duomenys. |
| 20 Administracija ir mokymas | Testuojama | Neaktualu | http://5gtech.test/wp-admin/admin.php?page=g5tech-guide | Codex | Sukurtos turinio ir personalo redaktorių rolės, apribotos teisės, supaprastintas meniu ir viena darbo atmintinė. Liko kliento priėmimo testas. |
| 21 Paleidimas | Nepradėta | Neaudituota |  |  |  |
