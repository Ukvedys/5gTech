# Darbo tęsimas kitame kompiuteryje

Atnaujinta 2026-11-18. Repozitorija: https://github.com/Ukvedys/5gTech

## Šaltinis ir gyva svetainė

- Gyva svetainė: https://5gtech.lt/; administravimas: https://5gtech.lt/wp-admin/.
- Produkcinis kodas: `wordpress/wp-content/themes/5gtech` ir `wordpress/wp-content/plugins/5gtech-core`.
- `dizainas/maketai` yra tik istorinis dizainas. Nekeičiame jo vietoje WordPress.
- `deploy/content/snapshot.json` – ankstesnė turinio kopija, ne naujausias gyvos DB eksportas. Neimportuoti aklai ant kliento turinio.

## Paskutiniai darbai

- Perkelti 17 LT skelbimų, pataisytas jų išdėstymas, kalbų ir diegimo kelių tvarkymas.
- SMTP pataisymas aprašytas `SMTP-PATAISYMAS-2026-10-30.md`.
- 2026-11-01 gyvoje DB išjungti komentarai ir jų el. pašto pranešimai. 57 komentarai perkelti į šiukšlinę (negalutinai ištrinti). Tai DB nustatymai, ne Git pakeitimai.
- 2026-11-05 įjungtas Cloudflare Turnstile kontaktų ir CV formose: `includes/turnstile.php`, abiejų renderinimo kelių integracija, EN/DE pranešimai, serverio tokeno/action/hostname patikra.
- Formos JS siuntimas pakeistas į `form.getAttribute('action')`: paslėptas `name="action"` laukas naršyklėje užgoždavo DOM savybę.
- Gyvoje svetainėje patikrintas atmetimas su tuščiu/netikru tokenu ir sėkmingas kontaktų bei CV bandymas naršyklėje. Pašto gavėjų dėžutės atskirai nepatikrintos.

## Kas nepatenka į Git – būtina prieš parduodant kompiuterį

Git nėra pilna WordPress ar slaptažodžių atsarginė kopija. Atskirai saugiai perkelti:

1. Slaptažodžių saugyklos įrašus: WordPress, IV, MySQL, SMTP ir prieigą prie Cloudflare paskyros. Nelaikyti slaptažodžių repozitorijoje.
2. Vietinį `/Users/eduardas/.config/5gtech/` katalogą (yra privatūs duomenys), jei reikia vietinės konfigūracijos. Naudoti šifruotą saugyklą, ne viešą GitHub.
3. Jei reikia tiksliai atkurti vietinę aplinką: ignoruojamus `wordpress/wp-config.php`, `wordpress/wp-content/database/`, `wordpress/wp-content/uploads/`, SQLite integraciją. Saugoti privačiai.
4. Naują gyvos svetainės DB ir failų atsarginę kopiją gauti iš IV prieš tolesnius diegimus. Šiuo Git push nauja serverio kopija nekuriama.

Serveryje SMTP konfigūracija yra `/domains/5gtech.lt/5gtech-smtp.php` už public_html. Turnstile `G5TECH_TURNSTILE_SITE_KEY` ir `G5TECH_TURNSTILE_SECRET` yra produkciniame `wp-config.php`. Jų Git nėra ir neturi būti. Cloudflare paskyroje widget vadinasi „5G TECH – kontaktų ir CV formos“.

## Naujame kompiuteryje

Klonuoti repozitoriją, įdiegti PHP/Node ir reikiamas projekto priklausomybes. Vietinei WordPress kopijai reikės privataus DB/failų atkūrimo ir vietinio domeno `5gtech.test`. Nesujungti vietinės kopijos su produkcine DB ir neaktyvinti produkcinio SMTP bandymams.

Prieš bet kokį diegimą palyginti gyvą kodą ir išsaugoti atsarginę kopiją. GitHub workflow turi atskirą rankinį turinio importą – jo nenaudoti vien kodo atnaujinimui. Šis perdavimo commit pažymėtas `[skip ci]`, kad kompiuterio pakeitimui skirtas įkėlimas nepaleistų serverio diegimo.

Seno kompiuterio netrinti, kol naujame nepatikrinti prisijungimai ir privačios kopijos.
