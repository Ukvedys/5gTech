# Kodo ir turinio atnaujinimas

Atnaujinta 2026-09-07. Ši atmintinė pakeičia ankstesnę automatinio turinio sinchronizavimo instrukciją.

## Kas vyksta dabar

- Įprastas `push` į `main` atnaujina staging **kodą**, ne kliento WordPress turinį.
- Live diegimas paleidžiamas atskirai, rankiniu būdu per GitHub Actions.
- Turinys importuojamas tik rankiniame diegime aiškiai įjungus `import_content`. Pagal nutylėjimą ši parinktis išjungta.
- `SYNC-ON` vėliavėlė nebenaudojama. Jos kurti ar grąžinti nereikia.
- Importo paketas laikomas už viešo svetainės katalogo; importas vykdomas tik per PHP CLI.

## Kai klientas jau redaguoja svetainę

Kasdienio turinio šaltinis yra kliento WordPress. Kodo atnaujinimus vykdyti **be turinio importo**. Negalima aklai eksportuoti vietinės kūrimo kopijos ir ja pakeisti kliento duomenų.

## Vienkartinis perkėlimas

1. Suderinti galutinio turinio šaltinį ir pasidaryti DB bei failų kopiją.
2. Jei patvirtintas šaltinis yra vietinis WordPress, sukurti eksportą su `php tools/export-content-snapshot.php`, peržiūrėti JSON ir medijos skirtumus.
3. Pirmiausia importuoti į staging pasirinkus `import_content` ir patikrinti kalbas, nuotraukas, formas bei redagavimą.
4. Tik po patvirtinimo atlikti vienkartinį live importą.

Importas perrašo sutampančius įrašus ir valdomus nustatymus; tai nėra turinio sujungimas. Kopijoje nenurodyti kliento įrašai automatiškai nešalinami. Nutrūkus importui galima dalinė būsena, todėl būtinas atkūrimo planas.

Išsami ir pagrindinė instrukcija: [DIEGIMAS.md](../DIEGIMAS.md).
