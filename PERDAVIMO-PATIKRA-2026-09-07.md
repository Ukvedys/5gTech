# 5G TECH — baigiamoji vietinė perdavimo patikra

2026-09-07. Šis protokolas papildo ankstesnes pataisų ataskaitas ir patikslina likusius perdavimo darbus.

## Sprendimas

**Vietiniai esamų puslapių redagavimo, formų apdorojimo ir atsarginės kopijos atkūrimo bandymai praėjo. Galima pereiti į bandomąjį perkėlimą kliento serveryje. Galutinio live priėmimo dar nėra.**

Kliento serverio adresas, prieiga ir patvirtintas bandomųjų laiškų gavėjas šiuo metu nepateikti. Joks išorinis diegimas, push ar tikras laiško siuntimas neatliktas.

## 1. Atsarginė kopija ir tikras vietinis atkūrimas — praėjo

- Padaryta nuosekli SQLite bazės kopija, naudojant SQLite backup mechanizmą.
- Supakuoti WordPress vykdymo failai: branduolys, tema, papildiniai, sukompiliuoti blokai, uploads ir vietinė konfigūracija. Neįtraukti `node_modules`, atskirai kopijuojama duomenų bazė ir diagnostinis `debug.log`; šie praleidimai svetainės vykdymui nereikalingi.
- Archyvas išskleistas į naują atskirą katalogą, bazė atkurta iš kopijos. Prieš testinius aplinkos pakeitimus failų palyginimas skirtumų nerado, DB `PRAGMA integrity_check` grąžino `ok`, buvo 402 `wp_posts` eilutės (įskaitant revizijas ir šiukšlinę).
- Pradinės ir atkurtos DB SHA-256 sutapo: `f8ac9091ce754c4273fe3a5bf0abc403eb18c4f6e947975b198fdf4f3bd798da`.
- Atkurta svetainė paleista tik per `127.0.0.1:8095`. Testinėje kopijoje pakeistas bazinis adresas, išjungtas cron ir priverstinai sulaikomi laiškai. Šie testiniai pakeitimai nėra produkcinio kodo dalis.
- Naršyklėje veikė prisijungimas, vizualinis blokų redaktorius ir vieša svetainė. Po bandymų testinis serveris sustabdytas, atkurtoje DB grąžinta pradinė kopija.

Pradiniai archyvai laikomi Git ignoruojamame `tmp/handoff-acceptance-2026-09-07/`: `database.sqlite` ir `wordpress-files.tar.gz`. Katalogo teisės 700, archyvų 600. Juose yra jautrių konfigūracijos ir paskyrų duomenų: **nekelti į Git, viešą serverį ar viešą failų nuorodą**. Tai vietinė kopija; ilgalaikį saugojimą ir išorinę kopiją dar reikia sutarti.

## 2. Kliento paskyros naršyklėje — praėjo tikrintos darbo eigos

Abi laikinos paskyros sukurtos tik atkurtoje DB; tikroje vietinėje svetainėje vartotojai nekeisti.

| Rolė | Gyvas Chrome bandymas | Rezultatas |
| --- | --- | --- |
| 5G TECH turinio redaktorius | „Apie mus“ bloko antraštės pakeitimas ir išsaugojimas | Tekstas išliko, visų 24 blokų struktūra išliko |
| Turinio redaktorius | Esamos nuotraukos pasirinkimas per mediateką, išsaugojimas | Nuotraukos ID išliko; failas matomas viešame puslapyje |
| Turinio redaktorius | Angliško titulinio teksto pakeitimas | Tekstas išliko, įrašo kalba liko EN |
| Turinio redaktorius | Bandymas atidaryti papildinių valdymą | Prieiga uždrausta |
| 5G TECH personalo redaktorius | Darbo skelbimo lauko pakeitimas ir „Atnaujinti“ | Reikšmė išliko |
| Personalo redaktorius | Darbuotojo profesinio aprašymo pakeitimas | Reikšmė išliko |
| Personalo redaktorius | Karjeros puslapio blokų redaktorius ir antraštės išsaugojimas | Redaktorius pasiekiamas, pakeitimas išsaugotas |
| Personalo redaktorius | Bendrų 5G TECH nustatymų adresas | Valdymo ekranas neprieinamas |

Meniu rodo rolėms skirtas skiltis. Papildinių, vartotojų ir temos administravimo teisės nesuteiktos. Tai tikrintų darbo eigų priėmimas, ne visų galimų teisių kombinacijų saugumo auditas.

Atkurtame viešame „Apie mus“ puslapyje patvirtinti pakeistas tekstas ir nauja nuotrauka. Patikrinti darbalaukio bei 360 px vaizdai, titulinio ir vidinio puslapio mobilus meniu, „Escape“ ir vidinio puslapio LT → EN nuoroda. Į anglišką vidinį puslapį pereinama tame pačiame testiniame domene, be horizontalaus iškritimo.

## 3. Formos ir CV — vietinis apdorojimas praėjo

- 110 realių HTTP ir rezultato patikrų izoliuotoje atkurtoje aplinkoje.
- Kontaktai ir kandidatavimas: LT / EN / DE, sėkmė, imituota pašto klaida, netinkamas saugos raktas ir nepateiktas sutikimas.
- 12 pasiektų, bet sulaikytų pašto transporto iškvietimų; **0 išorinių pristatymų**.
- 6 realūs multipart PDF įkėlimai pasiekė pašto apdorojimą. Priedo kontrolinė suma sutapo su bandomuoju failu, laikinos priedų kopijos po apdorojimo pašalintos.
- `Reply-To` išlaikė bandomojo siuntėjo adresą. Tai ne SMTP, pašto dėžutės ar šlamšto filtrų patikra.
- Papildomai pakartoti dabartinės svetainės testai: 58 blokų serializavimo ciklai, 28 formos naršyklės logikos patikros, 1003 kalbinės HTTP regresijos patikros — praėjo.

Diagnostikos scenarijai liko privačiame `/tmp/5gtech-handoff-svs4Z4/` kataloge. Tai nėra diegimo paketas ar ilgalaikė testų saugykla.

## 4. Dokumentacija ir originalių duomenų apsauga

- Pataisyta pasenusi `deploy/TURINYS-PER-GIT.md`: paprastas push nebeaprašomas kaip automatinis kliento turinio perrašymas; nebesiūloma naudoti SYNC-ON.
- `DIEGIMAS.md` patikslintos CV įkėlimo ribos ir būtinybė produkcijoje pašalinti vietinio pašto sulaikymą.
- Patikros metu tikros vietinės DB įrašų turinys ir esamų meta eilučių reikšmės, palyginus su pradine kopija, nepakito. Tikroje bazėje nėra `test_g5_*` paskyrų.
- Naujos produkcinės funkcijos šiame etape nekeistos. `design:design-critique` gairės naudotos tikroms kliento darbo eigoms patikrinti, o ne spręsti vien iš administravimo vaizdo.

## 5. Likę tikslinio serverio priėmimo vartai

1. Pateiktas staging adresas ir saugi prieiga; patvirtintas domenas ir tikslinis WordPress katalogas. Nekelti į live vien todėl, kad vietiniai bandymai praėjo.
2. MySQL / MariaDB importas staging, kalbų ir medijos ryšiai, pakartotinio importo ir kliento turinio išlikimo patikra.
3. Tikras kontaktų laiško ir CV gavimas patvirtintoje pašto dėžutėje, Reply-To ir priedas. Patikrinti sėkmę bei nesėkmę su realia hostingo pašto konfigūracija ir podėliu.
4. Hostingo DB **ir failų** kopija bei atkūrimo bandymas staging. Vietinis SQLite bandymas šio vartų punkto nepakeičia.
5. HTTPS, tikro domeno canonical / hreflang / sitemap, senų adresų peradresavimai, indeksavimo režimas ir mobili Safari / iOS bei Android patikra.
6. Kliento patvirtinti veiklos teiginiai, kontaktai, sertifikatai, atlygis, vertimai ir vaizdų naudojimo teisės.

Specialių puslapių kopijavimo į naują bendrinį puslapį dizaino apribojimas lieka: šis etapas patvirtina **esamų puslapių redagavimą**, ne universalią naujų landing puslapių kūrimo funkciją. Jei klientui jos reikia, tai atskiras užbaigtinas scenarijus.
