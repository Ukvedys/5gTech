# Kandidatavimo formos SMTP pataisymas — 2026-10-30

## Priežastis

Produkcijos Apache žurnalas patvirtino `Call to undefined function PHPMailer\PHPMailer\mail()` iš `includes/forms.php:478`. Pilnas bandymas su PDF prieš pataisymą grąžino HTTP 500. Serverio apsaugos nekeistos.

## Pakeitimai

- `5gtech-core/includes/mail-transport.php`: autorizuotas SMTP, TLS (465), siuntėjas `svetaine@5gtech.lt`, serveris `titnagas.serveriai.lt`. Kandidato Reply-To išsaugomas.
- `includes/forms.php`: įkeliamas transporto modulis; kontaktų ir kandidatavimo siuntimą apgaubia klaidų apdorojimas, kad PHP transporto klaida nebegrąžintų 500. Priedo valymas vykdomas ir po pagautos klaidos.
- Slaptažodis saugomas serveryje **už public_html ribų**, `/domains/5gtech.lt/5gtech-smtp.php`, teisės 600. Nėra Git. Vietinė atmintinė nurodo Keychain įrašą.
- Archyvai `smtp-fix.tar.gz` ir `smtp-fix-clean.tar.gz` yra privačiame domeno kataloge, teisės 600. Išskleistas tik clean archyvas.
- Prieš keitimą produkcinio forms.php tekstas sutapo su vietiniu (22176 simbolių, FNV 927994945). Ankstesnė versija saugoma ignoruojamame `tmp/smtp-fix-2026-10-30/forms-before.php`.

## Patikros

- Abiejų PHP failų sintaksė tinkama.
- Siuntimo apvalkalo normalus ir Error keliai patikrinti; 28 JS formos grįžtamojo ryšio patikros sėkmingos.
- Produkcijoje pakartotas vartotojo autorizuotas bandymas su tikru vieno puslapio testiniu PDF: HTTP 200, `success: true`, „Ačiū. Jūsų kandidatūra išsiųsta.“
- Testo tekstas vartotojo vardu prašo patvirtinti gavimą į eduardas@object.lt. Gavėjo pašto dėžutės pristatymas dar nepriklausomai nepatvirtintas.
- Produkcijos titulinis ir kandidatavimo puslapis vizualiai patikrinti 1440 ir 390 pločiais; mobilus meniu atsidaro ir užsidaro, EN perjungimas veikia. Ekrano emuliacija išjungta.
- Vietinė http://5gtech.test neatsakė per 5 s, todėl vietinė vizualinė patikra šio darbo metu neįvyko; tikrinta produkcija.

## Perkėlimai ateityje

SMTP paslaptis turi likti katalogu aukščiau WordPress ABSPATH; vien Git diegimas jos neperkelia. Nekeisti ar neperrašyti esamų turinio/kalbų duomenų. Jei reikia atšaukti kodą, grąžinti forms-before.php kaip forms.php; SMTP išjungimas grąžintų ankstesnę neveikiančio PHP mail problemą.
