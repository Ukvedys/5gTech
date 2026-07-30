# Daugiakalbės svetainės valdymas

## Kalbos ir adresai

- Lietuvių kalba: `/`
- Anglų kalba: `/en/`
- Vokiečių kalba: `/de/`

Kiekvienas viešas puslapis turi atskirą, žmogui ir paieškos sistemoms suprantamą adresą. Pavyzdžiui:

- `/paslaugos/`
- `/en/services/`
- `/de/leistungen/`

## Kalbos parinkimas

Pirmą kartą atėjus į svetainę tikrinama naršyklės kalba:

- lietuvių arba neatpažinta kalba atveria lietuvišką versiją;
- anglų kalba atveria anglišką versiją;
- vokiečių kalba atveria vokišką versiją.

Lankytojo pasirinkimas kalbų perjungiklyje įsimenamas slapuke ir vėliau turi pirmumą prieš naršyklės nustatymą. Tiesiogiai atidarytas LT, EN arba DE adresas visada išsaugo pasirinktą kalbą.

Kalbų valdiklis yra šalia logotipo. Ramybės būsenoje rodoma tik aktyvi kalba. Kitos kalbos parodomos užvedus pelę, fokusuojant klaviatūra arba paspaudus valdiklį telefone.

Nepriešdėliniai atsakymai turi `Vary: Accept-Language, Cookie` antraštę, kad tarpinės talpyklos nesumaišytų skirtingų lankytojų kalbos pasirinkimo.

## Kaip redaguoti modulio kalbas

1. Atidarykite **Turinio moduliai** arba konkretaus puslapio modulių sąrašą.
2. Pasirinkite modulį. Dinaminio modulio kortelėje kalbas tiesiogiai atidaro nuoroda **LT / EN / DE**.
3. Modulio turinio viršuje pasirinkite **LT**, **EN** arba **DE**.
4. Redaguokite pasirinktos kalbos tekstus ir paspauskite **Atnaujinti**.

Kalba keičiama tame pačiame modulyje. Nereikia kurti atskiro angliško ar vokiško modulio, todėl sekcijos vieta, fonas ir ryšiai su puslapiais visoms kalboms lieka vienodi.

**LT** yra pagrindinis turinio šaltinis. **EN** ir **DE** skirtukuose pirmą kartą rodomas dabartinis pradinis svetainės vertimas. Išsaugojus pakeitimus, jūsų tekstas turi pirmenybę ir naudojamas visuose puslapiuose, kuriuose rodomas tas pats modulis.

Dinaminio modulio lietuviškas turinys ir toliau valdomas bendrame duomenų šaltinyje, pavyzdžiui, skiltyje **Paslaugos**, **Projektai** ar **Komanda**. EN ir DE skirtukai automatiškai parodo realiai tame modulyje matomus tekstus, todėl juos galima išversti neišeinant iš modulio.

## Kaip redaguoti puslapio sekcijų kalbas

„Apie mus“, Karjeros, Mokymų, Academy, Vadovų, Projektų vadovų, titulinio auditorijų ir bendrų nustatymų tekstų grupėse taip pat rodomi **LT / EN / DE** pasirinkimai.

1. Atidarykite puslapio turinio ekraną ir išskleiskite norimą sekciją.
2. Sekcijos pradžioje pasirinkite **LT**, **EN** arba **DE**.
3. Redaguokite matomus tekstus.
4. Paspauskite puslapio mygtuką **Išsaugoti pakeitimus**.

Kalbos pasirinkimas viename bloke sinchronizuojamas su kitais to paties puslapio blokais. Tai apsaugo nuo situacijos, kai vienu išsaugojimu netyčia sumaišomi skirtingų kalbų laukai.

Nuotraukos, įrašų pasirinkimai, nuorodos, kontaktiniai duomenys, sekcijų seka ir modulio ryšiai yra bendri visoms kalboms. Keičiant EN arba DE tekstą jie nesidubliuoja ir nepasikeičia.

Kartotinių elementų struktūra kuriama LT režime. EN ir DE režimu verčiami jau sukurtų elementų tekstai, o pridėjimo, šalinimo ir perrikiavimo veiksmai laikinai paslepiami.

## Kaip redaguoti paslaugų kalbas

1. Atidarykite **Paslaugos** ir pasirinkite norimą paslaugą.
2. Plačiame bloke **Paslaugos informacija** pasirinkite **LT**, **EN** arba **DE**.
3. Redaguokite paslaugos pavadinimą ir tekstus.
4. Paspauskite **Atnaujinti**.

Nuotrauka, įrangos pasirinkimai, publikavimo būsena ir paslaugų eilės tvarka yra bendri visoms kalboms.

## Techninis pradinių vertimų sluoksnis

Pradiniai visos svetainės vertimai laikomi 5G TECH papildinyje:

- `wordpress/wp-content/plugins/5gtech-core/languages/en.php`
- `wordpress/wp-content/plugins/5gtech-core/languages/de.php`
- `wordpress/wp-content/plugins/5gtech-core/languages/en-overrides.php`
- `wordpress/wp-content/plugins/5gtech-core/languages/de-overrides.php`

Pagrindiniai katalogai apima visą esamą svetainės turinį. `*-overrides.php` failuose laikomos žmogaus peržiūrėtos formuluotės ir terminija. WordPress administravime išsaugoti modulio ir puslapio sekcijų vertimai turi pirmenybę. Įprastam turinio redaktoriui šių failų keisti nereikia.

## Naujo turinio įkėlimas

1. Turinys pirmiausia sukuriamas lietuvių kalba WordPress administravime.
2. Tame pačiame modulyje atidaromas **EN** skirtukas ir peržiūrimas arba pataisomas angliškas tekstas.
3. Tame pačiame modulyje atidaromas **DE** skirtukas ir peržiūrimas arba pataisomas vokiškas tekstas.
4. Modulis išsaugomas vieną kartą paspaudus **Atnaujinti**.
5. Patikrinamas viešas LT, EN ir DE puslapis.

Svetainė nevykdo automatinio vertimo lankytojo naršyklėje ir nesiunčia turinio išorinei vertimo paslaugai.

## Patikrinimas

Visą daugiakalbę svetainę galima patikrinti komanda:

```bash
php tools/test-multilingual-site.php http://5gtech.test
php tools/test-wordpress-module-translations.php
php tools/test-wordpress-admin-translations.php
```

Testas patikrina:

- visus LT, EN ir DE maršrutus;
- kalbų perjungimą ir pasirinkimo įsiminimą;
- naršyklės kalbos aptikimą;
- vidinių nuorodų kalbą;
- formas, jų būsenas ir paiešką;
- `lang`, `hreflang` ir 404 atsakymus;
- neišverstas katalogo eilutes.

Antras testas papildomai patikrina modulio LT / EN / DE laukus, dinaminių modulių tekstų nuskaitymą ir WordPress administravime išsaugotų vertimų pirmenybę.

Trečias testas patikrina puslapių sekcijų kalbų valdiklius, vertimų išsaugojimą ir kelių eilučių tekstų perdavimą viešai svetainei.
