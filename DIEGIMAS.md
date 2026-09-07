# „5G TECH“ diegimas ir turinio perkėlimas

Atnaujinta 2026-09-07. Produkcinis šaltinis – „WordPress“ tema ir „5gtech-core“, ne `dizainas/maketai`.

## Svarbiausia

- Įprastas `push` į `main` atnaujina tik **staging kodą**.
- Į **live** diegiama tik rankiniu „GitHub Actions“ paleidimu.
- Turinys ir jo nuotraukos keliami tik rankiniu būdu pažymėjus `import_content`. Numatytoji reikšmė – išjungta.
- `SYNC-ON` nebenaudojamas. Importas per HTTP draudžiamas; vykdymas tik per SSH / PHP CLI.
- Kopija ir importo scenarijai laikomi privačiame SSH vartotojo kataloge `$HOME/.g5tech-deploy/<svetainės-identifikatorius>`, ne `public_html`.
- Senas `wp-content/g5-deploy` per diegimą perkeliamas į privatų `legacy` katalogą. Failai išlieka atkuriami, tačiau viešo importo scenarijaus grąžinti negalima.

## Serverio paruošimas

Reikia atskiros veikiančios „WordPress“ instaliacijos su MySQL / MariaDB, HTTPS, PHP 8.2 ar naujesniu ir tinkamomis PHP plėtinių bei failų įkėlimo ribomis. Vietinė SQLite DB tiesiogiai į MySQL nekopijuojama.

SSH aplinkoje turi veikti PHP CLI, WP-CLI (`wp`), `rsync`, `curl`, `sha256sum` ir `mariadb-dump` arba `mysqldump`. CLI ir svetainė turi naudoti suderinamas PHP versijas.

Prieš importą aktyvuoti temą `5gtech`, papildinius `5gtech-core` ir `polylang`. Importas pats jų neaktyvuoja. Pirmai instaliacijai failus galima įkelti rankiniu būdu, aktyvuoti ir tada importuoti. Tik kodo diegimo HTTP patikra iki pirmo importo gali nepraeiti, nes dar nėra tikslinių puslapių.

GitHub → Settings → Secrets and variables → Actions:

| Paslaptis | Turinys |
| --- | --- |
| `SSH_HOST` | Serverio adresas |
| `SSH_PORT` | SSH prievadas |
| `SSH_USER` | SSH vartotojas |
| `SSH_PRIVATE_KEY` | Diegimui skirtas privatus SSH raktas |
| `REMOTE_PATH_STAGING` | Absoliutus staging „WordPress“ kelias |
| `REMOTE_PATH_LIVE` | Absoliutus live „WordPress“ kelias |

Viešą rakto dalį įdėti į hostingo SSH prieigas. Privačios dalies nekelti į Git. Keliai turi rodyti į konkrečią svetainę, ne serverio ar vartotojo katalogo šaknį.

## Įprastas kodo atnaujinimas

1. Patikrinti pakeitimus vietinėje svetainėje, įrašyti į Git ir atlikti `push`.
2. Staging diegimas patikrina PHP sintaksę, paruošia „Polylang“, sukompiliuoja blokus ir sukuria DB kopiją.
3. Atnaujinama tema, „5gtech-core“ ir „Polylang“; turinio importas nevykdomas.
4. Patikrinama aktyvi tema, papildiniai ir keturi pagrindiniai HTTP adresai, išvalomas objektų ir, jei naudojama, „LiteSpeed“ podėlis.
5. Patikrinus staging, „Actions → Diegimas į serverį → Run workflow“ pasirinkti `live`, **nežymėti** `import_content`.

Temos ir dviejų valdomų papildinių katalogai sinchronizuojami su `--delete`: rankinių serverio pataisų juose nelaikyti. „WordPress“ branduolys, vartotojai ir kiti papildiniai šiuo scenarijumi nekeliami.

## Vienkartinis turinio perkėlimas

**Importas perrašo sutampančius įrašus ir valdomus nustatymus.** Kliento pakeitimai tame pačiame įraše nesujungiami. Importas nėra transakcija: nutrūkus galima dalinė būsena. Būtina patikrinta atkūrimo kopija ir sustabdytas lygiagretus redagavimas.

1. Sutarti, kuri aplinka yra galutinio turinio šaltinis, padaryti kopiją.
2. Jei šaltinis – vietinis „WordPress“, vykdyti `php tools/export-content-snapshot.php`. Peržiūrėti `deploy/content/snapshot.json` ir `deploy/uploads/` skirtumus; įrašyti į Git.
3. Kopija turi būti `schema_version: 2`, su kiekvieno įrašo `source_id` ir visais vaizdais. Sena schema atmetama.
4. Rankiniu būdu paleisti **staging**, pažymėti `import_content`.
5. Patikrinti kalbas, nuotraukas, meniu, formas ir redagavimą. Tik po to atskirai importuoti į **live**.
6. Klientui pradėjus redaguoti serverio „WordPress“, įprastus diegimus vykdyti be importo.

Importas pagal tipą ir adresą suranda įrašus, perskaičiuoja įrašų bei nuotraukų ID, sutvarko vertimų ryšius ir išsaugo kopijoje nenurodytus kliento įrašus. Pakartotinis importas išbandytas atskiroje SQLite kopijoje; tiksliniame MySQL serveryje dar reikalingas staging bandymas.

Rankinis paleidimas per SSH:
```bash
php /privatus/kelias/sync-content.php --wordpress=/tikslus/wordpress/kelias --import-content
```
Prieš tai nuotraukas įkelti į tikslinį `wp-content/uploads`. Be `--import-content` scenarijus baigia darbą net neįkėlęs „WordPress“.

Vietinė kopija: `bash tools/atnaujinti-lokalia.sh --import-content`. Šis įrankis automatinės DB kopijos nekuria – ją pasidaryti iš anksto.

`tools/prepare-handoff-snapshot.php` yra vienkartinis senos kopijos paruošimo įrankis, ne įprasto diegimo dalis. Perdavimo kopija jau paruošta.

## Kopijos ir atkūrimas

Actions DB kopiją išsaugo šalia „WordPress“ esančiame privačiame `.g5tech-backups` kataloge, apribotomis teisėmis. Kopijos klaida sustabdo diegimą. Prieš pirmą perkėlimą hostingo priemonėmis papildomai padaryti temos, papildinių ir uploads failų kopiją.

Automatinio rollback nėra. Sustabdžius diegimus ir redagavimą atkurti ankstesnę DB, su ja suderinamus temos, papildinių ir uploads failus; išvalyti podėlius ir patikrinti formas bei adresus. Atkūrimą pirmiausia išbandyti staging. Kopijų saugojimo terminą sutarti su klientu.

## Patikros prieš atidavimą klientui

- Tiksliniame serveryje: HTTPS, MySQL importas, visi LT / EN / DE puslapiai, vaizdai ir peradresavimai.
- Darbalaukyje ir telefone: titulinis bei vidiniai puslapiai, meniu, kalbos, jokio horizontalaus perslinkimo.
- Kontaktų ir kandidatavimo formos: **tikras laiško gavimas** bei CV priedas. Vietinis testas laiškų nesiunčia ir neįrodo SMTP veikimo.
- Kliento redaktoriaus paskyra: pakeisti bloko tekstą, nuotrauką ir vertimą, patikrinti išsaugojimą.
- Kliento patvirtinti kontaktai, komandos duomenys, sertifikatai, skaičiai, vertimai ir vaizdų naudojimo teisės.
- Indeksavimo nustatymai, domeno nuorodos, privatumo informacija ir atsarginių kopijų atkūrimas.

HTTP regresija: `php tools/test-multilingual-site.php`. Tikrina vietinę svetainę; siunčiamos tik nepilnos / neteisingos formos, neinicijuojančios laiškų.

Senas `tools/test-wordpress-modules.php` tikrina ankstesnį modulių kompozitorių ir šiuo metu nepraeina. Jis nėra dabartinio blokų redaktoriaus priėmimo testas. Dabartinių blokų saugojimą tikrina `tools/test-block-editor.php` atskiroje testinėje DB.
