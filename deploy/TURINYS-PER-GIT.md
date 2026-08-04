# Turinys per git push

Nuo šiol `git push` į `main` atnaujina Hostingeryje IR kodą, IR turinį.

## Kaip veikia
1. `tools/export-content-snapshot.php` sukuria turinio momentinį įrašą
   `deploy/content/snapshot.json` (puslapiai, komanda, paslaugos, projektai,
   naujienos, pozicijos, DUK, partneriai, nustatymai, kalbos ir vertimų ryšiai)
   ir nukopijuoja naudojamas nuotraukas į `deploy/uploads/`.
2. Push'as paleidžia diegimą į staging. Prieš pakeitimus Hostinger serveryje
   sukuriama duomenų bazės atsarginė kopija.
3. Įkeliama tema, `5gtech-core`, `Polylang`, nuotraukos ir sinchronizavimo
   paketas. Tada paleidžiamas `deploy/sync-content.php`.
4. Įrašai atnaujinami pagal tipą ir slug'ą. Serverio bandomieji valdomų tipų
   įrašai, kurių momentiniame įraše nėra, pašalinami. Kitų papildinių duomenys,
   vartotojai ir nesusiję `uploads/` failai neliečiami.

## Kasdienis darbas
1. Redaguoji turinį LOKALIOJE svetainėje (Local).
2. Projekto šaknyje: `php tools/export-content-snapshot.php`
3. `git add -A && git commit -m "turinys" && git push`
4. Po kelių minučių Hostinger staging turinys atitinka vietinę svetainę.

## ⚠️ SVARBIAUSIA TAISYKLĖ
Kol įjungtas sinchronizavimas, TIESA yra lokali svetainė. Serveryje darytus
turinio pakeitimus kitas push'as PERRAŠYS.

**Paleidžiant 5gtech.lt gyvai — IŠTRINTI failą `deploy/content/SYNC-ON` ir
padaryti push.** Nuo tada serverio turinys tampa tiesa ir diegimas jo nebeliečia
(keliauja tik kodas).
