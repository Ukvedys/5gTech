# Turinys per git push

Nuo šiol `git push` į `main` atnaujina Hostingeryje IR kodą, IR turinį.

## Kaip veikia
1. `tools/export-content-snapshot.php` sukuria turinio momentinį įrašą
   `deploy/content/snapshot.json` (puslapiai, komanda, paslaugos, projektai,
   naujienos, pozicijos, DUK, partneriai, nustatymai) ir nukopijuoja naudojamas
   nuotraukas į `deploy/uploads/`.
2. Push'as paleidžia diegimą: kodas įkeliamas kaip anksčiau, tada serveryje
   paleidžiamas `deploy/sync-content.php`, kuris turinį atnaujina pagal įrašą
   (pagal slug'us; kiek kartų paleisi — rezultatas tas pats).

## Kasdienis darbas
1. Redaguoji turinį LOKALIOJE svetainėje (Local).
2. Projekto šaknyje: `php tools/export-content-snapshot.php`
3. `git add -A && git commit -m "turinys" && git push`
4. Po kelių minučių Hostingeris identiškas lokaliai svetainei.

## ⚠️ SVARBIAUSIA TAISYKLĖ
Kol įjungtas sinchronizavimas, TIESA yra lokali svetainė. Serveryje darytus
turinio pakeitimus kitas push'as PERRAŠYS.

**Paleidžiant 5gtech.lt gyvai — IŠTRINTI failą `deploy/content/SYNC-ON` ir
padaryti push.** Nuo tada serverio turinys tampa tiesa ir diegimas jo nebeliečia
(keliauja tik kodas).
