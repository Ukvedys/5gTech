# Perkėlimo į kliento serverį eiga

## Dabartinė būsena — paleista 2026-09-08, apie 14:55 (Vilniaus laiku)

**Nauja svetainė viešai veikia adresu https://5gtech.lt/.** Naudotojas po skelbimų perkėlimo aiškiai patvirtino paleidimą. Visi 17 LT darbo skelbimų pasiekiami https://5gtech.lt/karjera/. Administratorius prisijungia per https://5gtech.lt/wp-admin/.

### Skelbimų vizualinė pataisa — 2026-09-08, apie 15:18

- Naudotojo prašymu pataisytas atlyginimo, komandiruotės, vietos ir vairuotojo kategorijos blokelių išdėstymas. Senoji bendra 6 stulpelių statistikos schema su 2/1/1/2 pločiais palikdavo nepilną eilutę, kai duomenų mažiau nei keturi; ilgas šalių tekstas išsikišdavo iš siauro trečio bloko (viešai išmatuota 254 px turinio, kai langelis 239 px).
- Tik darbo skelbimų `.single-g5_job` sąlygoms pritaikytos vienodo pločio, visą eilutę užpildančios flex eilutės; planšetėje 2 stulpeliai su pilno pločio paskutiniu nelyginiu bloku, telefone 1 stulpelis. Mažesnis reikšmių šriftas, saugus teksto laužymas, vienodi vidiniai tarpai, apačioje sulygiuoti paaiškinimai. Skelbimų sąrašams pridėtos trūkstamos šoninės rėmelio linijos. Kitų puslapių statistikos schema nepakeista.
- Pakeistas tik `wordpress/wp-content/themes/5gtech/assets/css/internal/shared.css`. WordPress laukai, duomenys, redaktorius ir statiniai maketai nekeisti. Vizualiai tikrinta darbalaukio, planšetės ir telefono versija, atskirai 3 ir 4 laukų eilutės. Visų 17 LT skelbimų DOM geometrija patikrinta 320, 768, 1051 ir 1440 px pločiuose: **68 puslapio / pločio kombinacijos, 0 teksto persipildymo, rėmelių ar puslapio pločio klaidų**. Vietinis titulinis patikrintas darbalaukyje / telefone, mobilus meniu atsidaro, EN perjungimas veikia.
- Vieno CSS failo archyvas `job-layout-fix.tar.gz` įkeltas ir išskleistas į veikiančią WordPress instaliaciją. Viešo ir vietinio pataisyto CSS SHA-256 sutampa: `7d0f6e6b90508dfe9c6233f3fdcac8726a1c3abd60b94ea54fb17bfdaf5e12a4`. Viešame skelbime patvirtinta 3 vienodo pločio langelių eilutė ir persipildymo nebuvimas 1800 / 360 px, sąrašuose abi šoninės linijos po 1 px. Failo modifikavimo laiku paremtas CSS versijavimas jau yra temoje.
- Ankstesnis CSS išsaugotas `tmp/job-layout-2026-09-08-0ORsV8/shared-before.css`; jo SHA-256 sutapo su Git HEAD prieš šią pataisą. Naujų Git commit / push neatlikta. Laikina mobilioji emuliacija išjungta.

### Paleidimas ir galutinė patikra

- Naujoji kopija iškelta iš seno `public_html/new-site` į privatų gretimą katalogą. Sena svetainė pervadinta į `/domains/5gtech.lt/previous-site-2026-09-08`, nauja — į `/domains/5gtech.lt/public_html`. Nieko neperrašyta ir neištrinta. Senajam katalogui nustatyta 700, ankstesnė patikrinta `backup-before-migration-2026-09-08` kopija taip pat išsaugota. `private_html -> ./public_html` nuoroda lieka galiojanti. DNS ir pašto dėžučių nustatymai nekeisti.
- `.well-known` SSL aptarnavimo katalogas nukopijuotas į naują svetainę prieš perjungimą. Pritaikytos šakninės WordPress taisyklės ir HTTPS / be-www kanoninis adresas. Pašalintas laikinas IP ribojimas ir HTTP `X-Robots-Tag: noindex`. `wp-config.php` turi 600 teises, produkcinį režimą, išjungtą klaidų rodymą; WP-Cron atkurta.
- Tik naujoje `gtchlt_site2026` DB pritaikytas 311 tiksliai apribotų URL pakeitimų rinkinys. Pradinė patikra aptiko likusią Polylang kalbų atmintinę, kurios pradiniuose adresuose buvo `/new-site/`. Išvalyti tik `_transient_pll_languages_list`, jo galiojimo įrašas ir `rewrite_rules`, apribojus DB vardu; adresai persigeneravo teisingai. Turinys ir vartotojai šiuo veiksmu nekeisti.
- Viešo domeno pakartotinės patikros: **1115 bendrų patikrų — 0 klaidų; 666 darbo skelbimų patikros — 0 klaidų**. Testai atlikti nuosekliai su 800 ms pauzėmis tarp užklausų (`G5_HTTP_DELAY_MS`, naujas pasirenkamas testų nustatymas). Pirmoji intensyvi lygiagreti patikra gavo laikinų 503 ir neteisingų kalbų pradinių adresų; po atmintinės pataisos bei pakartojimo šių klaidų nėra. Tiksli pirmųjų 503 priežastis atskirai neįrodyta; tuo laikotarpiu WordPress versija taip pat pasikeitė iš 7.0.2 į 7.1.
- Vizualiai patikrintas viešas titulinis ir karjeros puslapis darbalaukyje bei 390 px emuliacijoje, skelbimų sąrašas (scrollWidth = 390), atsidarantis mobilus meniu, EN ir DE pradinių puslapių perjungimas. Laikina emuliacija išjungta. Prieš perjungimą vietiniai tie patys puslapiai ir meniu / kalbos jau buvo patikrinti.
- Pagrindinio domeno administratoriaus prisijungimas sėkmingas. Skelbimo išsaugojimas išbandytas prieš perjungimą. WordPress „Skaitymo“ nustatymuose įjungtas indeksavimas; viešame HTML nebėra `noindex`, canonical yra `https://5gtech.lt/`, `robots.txt` neblokuoja svetainės ir nurodo `https://5gtech.lt/wp-sitemap.xml` (HTTP 200). HTTP ir HTTPS www karjeros adresai patikrinti: abu grąžina 301 į `https://5gtech.lt/karjera/`.
- WordPress savikontrolė patvirtina veikiančius REST API, loopback, suplanuotas užduotis, HTTPS, autorizavimo antraštę ir failų įkėlimo galimybę. Peržiūros IP ribojimo sukeltų REST / loopback įspėjimų neliko.
- Bandomųjų kontaktų / CV laiškų nesiųsta pagal naudotojo nurodymą. Gavėjai anksčiau patikrinti administravime: `info@5gtech.lt` ir `atrankos@5gtech.lt`. **Tikro pristatymo patvirtinimas lieka naudotojui / klientui.**

### Saugumo patikros ribos ir priežiūros darbai

- Prieš perjungimą inventorizuoti 4699 naujos kopijos failai. Sutampantiems vietinės paruoštos kopijos failams SHA-256 skirtumų nėra (įskaitant galutinę i18n pataisą). Papildomi failai — suplanuota serverio konfigūracija, WordPress / Polylang kalbų paketai ir išsaugotas elgesio kodekso PDF. Nepersiųsti kūrimui skirti `blocks-src` / paketų konfigūracijos failai nėra trūkstamas vykdomas kodas. Tai ribota vientisumo patikra, ne visos serverio paskyros saugumo auditas.
- Sena `.user.ini` automatiškai įkeldavo seno Wordfence kodą iš absoliutaus `public_html` kelio (INI cache 300 s). Naujoje `.user.ini` ši priklausomybė panaikinta. Trumpam naudotas tik komentarus turintis `wordfence-waf.php` suderinamumo failas, ne senas kodas; praėjus cache laikui jis iškeltas į privatų domeno katalogą. Viešas jo adresas ir diagnostikos adresai grąžina 404, titulinis ir toliau 200. Abu konfigūracijos archyvai laikomi už `public_html`, jų 600 teisės patvirtintos serverio failų tvarkyklėje.
- Senajame puslapyje rastas reklaminis šlamštas neperkeltas. Sena versija nebepasiekiama viešai, jos failai / DB išsaugoti. **Rekomenduojama atskira IV paskyros saugumo patikra ir prieigų pakeitimas**, nes vien svetainės pakeitimas neįrodo, kad pirminė įsilaužimo priežastis pašalinta. Pašto dėžučių, kitų DB ar visos paskyros auditas nebuvo šio paleidimo dalis.
- Po WP-Cron atkūrimo administravime patvirtinta **WordPress 7.1** (prieš perjungimą 7.0.2); rankinis branduolio atnaujinimas nebuvo paleistas. Galutinės HTTP ir mobilios patikros atliktos jau šiai viešai versijai. Vietinėje repeticijos kopijoje dar 7.0.2, jos negalima aklai perkelti ant gyvos 7.1.
- Savikontrolėje liko įskiepio atnaujinimo įspėjimas (Polylang; prieš paleidimą naudota 3.7.5), rekomendacijos dėl serverio SQL versijos / neprivalomų PHP modulių, numatytosios atsarginės temos ir puslapių cache. Šie priežiūros pakeitimai šiuo paleidimu netaikyti; nėra teiginio, kad visi saugumo / serverio rekomendacijų punktai uždaryti.
- GitHub SSH diegimas į IV neperkonfigūruotas; commit / push neatlikti. Tolimesniam diegimui naudoti produkcinį WordPress šaltinį ir saugoti gyvos DB turinį, paskyras bei konfigūraciją. Statiniai maketai neliečiami.

### Grįžimo kelias (tik esant poreikiui, dabar netaikyti)

Naujoji DB yra `gtchlt_site2026`, senoji — `gtchlt_nawsad89asdj`. Prieš bet kokį grįžimą išsaugoti naujus gyvos svetainės duomenis. Katalogus galima grįžtamai sukeisti, seno katalogo teises grąžinus į 755; `.well-known` yra abiejose kopijose. Senos DB iš naujo importuoti vien dėl katalogų grąžinimo nereikia. Tačiau sena svetainė turi įtartino šlamšto požymių — jos viešai negrąžinti be atskiro sprendimo ir saugumo įvertinimo. Slaptažodžių čia nėra; vietinė atmintinė už Git ribų atnaujinta pagrindiniu administravimo adresu.

## Istorinė būsena — po darbo skelbimų perkėlimo, prieš galutinį patvirtinimą

**Paleidimas naudotojo prašymu pristabdytas. Neperjungti be naujo patvirtinimo.** Visi 17 senos svetainės lietuviškų darbo skelbimų perkelti į vietinį WordPress ir serverio peržiūros kopiją: 4 Lietuvos, 8 Europos ir 5 biuro pozicijos. Esama Europos pradinio lygio pozicija atnaujinta (ne dubliuota), likusios 16 sukurtos. EN ir DE esami vertimai palikti; naujų 16 pozicijų vertimai nekurti, nes šaltinis lietuviškas.

- Turinys iš `https://5gtech.lt/karjera/`, 17 Elementor skelbimų sričių, perkeltas į esamus `g5_job` laukus. Skelbimų maketai ir TVS mechanizmas nepakeisti. Atlyginimai ir sąlygos išlaikyti; privalumai pažymėti kaip privalumai, ne paversti reikalavimais. Pakoreguotos kelios aiškios rašybos klaidos; administracinės pozicijos pasikartojantis reklaminis įvadas sutrumpintas. Bendras atrankos procesas jau pateikiamas karjeros puslapyje.
- Nauji failai: `deploy/content/legacy-jobs.json` (šaltinio atitikmenys), `tools/migrate-legacy-jobs.php` (tik CLI, vietinio WordPress importas), `tools/test-legacy-jobs.php` (tik skaitymo HTTP / turinio / kandidatavimo patikra). Atnaujinta `deploy/content/snapshot.json` kopija — 215 įrašų.
- Serverio importui naudotas privatus `jobs-only.sql.gz`: atnaujina tik šių skelbimų įrašus, jų laukus ir LT kalbos ryšius; tikrina DB vardą `gtchlt_site2026`, peržiūros URL ir temos pavadinimą. Nėra DROP, vartotojų, slaptažodžių ar svetainės parinkčių pakeitimų. Pirmas bandymas nutrūko prieš pirmą INSERT dėl serverio collation skirtumo; pridėtas aiškus `utf8mb4_unicode_520_ci`, pakartotas importas sėkmingas. Pakartotinio importo nedubliavimas išbandytas izoliuotoje vietinėje DB.
- Vietinė DB kopija prieš pakeitimą: privatus `local-before-jobs.sqlite`. Serverio administravime matomi 19 įrašų: 17 LT ir 2 ankstesni EN / DE. Patikrintas administracinės pozicijos redaktorius ir sėkmingas išsaugojimas (`Įrašas atnaujintas`).
- Abiejose kopijose: **1115 bendrų HTTP patikrų, 0 klaidų**, ir **666 skelbimų turinio / sąrašo / iš anksto pasirinktos kandidatūros patikros, 0 klaidų**. Tikri laiškai / CV nesiųsti. Naudotojas nurodė, kad juos išbandys pats arba klientas; tai nėra priežastis siųsti bandomą užklausą automatiškai.
- Vizualiai patikrinti serverio skelbimų sąrašas darbalaukyje ir telefone, ilgiausio pavadinimo pozicija telefone (390 px, horizontalus plotis neišsiplečia), vietinis titulinis ir karjeros puslapis darbalaukyje / telefone. Vietinis mobilus meniu atsidaro, EN → LT perjungimas veikia. Naršyklės plotis atstatytas. Izoliuotas vietinis MariaDB serveris po repeticijos išjungtas.
- Seno karjeros puslapio DOM apačioje pastebėtos pašalinės suaugusiųjų svetainių reklaminės nuorodos. Jos nepateko į skelbimų duomenis. Tai senos svetainės galimo kompromitavimo požymis, ne nustatyta užkrėtimo priežastis. Prieš paleidimą būtina įvertinti paskyros / senų failų izoliavimą; naujai kopijai senų įskiepių ar visos senos DB neperkelti.

Toliau — iki šio papildymo atlikti perkėlimo patikrinimai. Pagrindinio domeno perjungimas dar neatliktas.

**Viešoji svetainė dar neperjungta.** `https://5gtech.lt/` tebėra sena svetainė; po PHP atnaujinimo ji atsako HTTP 200. Nauja kopija veikia adresu `https://5gtech.lt/new-site/`, kataloge `/domains/5gtech.lt/public_html/new-site`, su atskira DB `gtchlt_site2026`.

### Atlikta ir patvirtinta

- Seno `public_html` ir privataus `backup-before-migration-2026-09-08` katalogo **12 551 failas sutampa pagal SHA-256**: nėra trūkstamų, papildomų ar pasikeitusių failų. Ankstesnis valdymo pulto dydžių neatitikimas nėra likęs blokeris. DB kopijos atkūrimo patikra aprašyta istoriniame žurnale žemiau.
- Į naują DB sėkmingai importuotas pilnas SQL. Patvirtintas tikras serverio prisijungimas: 12 lentelių, 294 `wp_posts` eilutės, vienas administratorius. Sena DB `gtchlt_nawsad89asdj` ir neidentifikuota `gtchlt_yyxz1` nepakeistos.
- Sukurtas administratorius `eduardas`, el. paštas `eduardas@object.lt`; prisijungimas per naujos svetainės WordPress formą sėkmingas. Bandomojo `local_build_only` naudotojo naujoje DB nėra. Pateiktas slaptažodis saugomas vietinėje macOS Keychain `login`, įraše `5gtech.lt — WordPress — eduardas`. Šiame dokumente ir Git slaptažodžių nėra.
- Naujos kopijos `wp-config.php` su atskiros DB prisijungimu įkeltas ir turi teises **600**. Konfigūracijoje peržiūros režimas `staging`, `DISABLE_WP_CRON=true`, `WP_DEBUG=false`, `FORCE_SSL_ADMIN=true`, `DISALLOW_FILE_EDIT=true`.
- Peržiūros `.htaccess` leidžia tik operatoriaus dabartinį IP. Apsaugos veikimas išbandytas trumpam pakeitus ją į `Require all denied`: gautas HTTP 403, po to atkurta tik operatoriui skirta taisyklė. WordPress `blog_public=0`, puslapiuose yra `noindex, nofollow`. Prieiga nėra skirta viešam klientų testavimui iš kito tinklo.
- Laikinas tik skaitymui skirtas diagnostikos failas iškeltas iš `public_html` į privatų `/domains/5gtech.lt`; jo buvęs viešas adresas grąžina 404. Viešo DB importuotojo nekurta.
- Rastos ir sutvarkytos perkėlimo failų teisės: 228 vieši temos / įskiepio / medijos failai archyvuose turėjo 600; naujoje serverio kopijoje jiems nustatyta 644. CSS po pataisos atsako HTTP 200, dizainas ir logotipas įsikrauna. `wp-config.php` neliečiamas šios pataisos. **Senų kodo / medijos archyvų vienų nepakanka: reikia ir `public-permissions.tar.gz` pataisos arba normalizuoti teises prieš naują pakavimą.**
- Pataisytas produkcinio `5gtech-core/includes/i18n.php` nuorodų apdorojimas WordPress pakatalogyje ir temos navigacijos šakninių nuorodų susiejimas su diegimo keliu. Į serverį įkelta `url-patch.tar.gz` pataisa; statiniai maketai nekeisti. Įprastų šakninių adresų elgsena išlaikyta.
- Naujas `tools/test-i18n-installation-path.php`: **93 patikros, 0 klaidų**. Vietinės svetainės HTTP patikra: **1003, 0 klaidų**. Kliento serverio peržiūros HTTP patikra: **1003, 0 klaidų**, taip pat po PHP atnaujinimo. Testas pritaikytas pakatalogiui ir HTTP antraščių raidžių dydžiui; formų bandymuose trūksta privalomų laukų, todėl laiškai nesiunčiami.
- Vietinėje ir kliento serverio kopijose vizualiai patikrintas titulinis bei kontaktų puslapis darbalaukio ir 390 px pločiuose, mobilus meniu, LT / EN / DE perjungimas. Laikinas naršyklės plotis atstatytas.
- Atkurta WordPress svetainės kalba `lt_LT`, įdiegtas oficialus lietuviškas WordPress paketas ir atnaujinti Polylang LT / DE vertimai. Administravimas lietuviškas; bendrųjų nustatymų kalbos pakeitimas nelietė adresų ar administratoriaus el. pašto.
- Serverio PHP atnaujinta **8.1 → 8.3**. Prieš pakeitimą domenų sąraše patvirtintas tik `5gtech.lt`, subdomenų 0. WordPress serverio informacijoje patvirtinta **PHP 8.3.33, cgi-fcgi**. DNS ir pašto dėžučių nustatymai nekeisti.
- Gavėjai vizualiai patvirtinti naujos svetainės administravime: `info@5gtech.lt` ir `atrankos@5gtech.lt`. **Tikri bandomieji laiškai / CV dar nesiųsti ir gavimas nepatvirtintas.** Formos laukų reikšmės naršyklės DOM išraše gali būti paslėptos: gavėjai patikrinti ekrano vaizdu, o ne tuščiomis DOM `value` reikšmėmis.

### Kas liko prieš viešą perjungimą

1. Naudotojas / klientas rankiniu būdu išbandys užklausos ir CV gavimą. Agentas bandomųjų laiškų nesiunčia; pristatymas ir SMTP poreikis dar nepatvirtinti.
2. Patikrinti redaktoriaus įkėlimą / išsaugojimą tiksliniame serveryje ir senų puslapių nukreipimų žemėlapį. Vien HTTP testas neatstoja viso redagavimo ar pašto pristatymo testo.
3. Paruošti galutinį šakninį `.htaccess`, išsaugoti `.well-known` SSL kelią, panaikinti tik laikinas peržiūros taisykles, atkurti produkcinį konfigūracijos režimą ir WP-Cron. Peržiūros prieigos ribojimas šiuo metu lemia loopback / REST savikontrolės įspėjimus; viešoje aplinkoje būtina pakartoti patikrą. Negalima jų ignoruoti po perjungimo.
4. `production-urls.sql.gz` yra paruoštas tikslus 311 peržiūros URL pakeitimų atvirkštinis rinkinys. Jis dar **neimportuotas**. Jo WHERE sąlygos tikrina seną reikšmę: jeigu peržiūros metu parinktis buvo redaguota, reikia atskirai patikrinti, ar atvirkštinis pakeitimas jai pritaikytas. Turinio parinktys per administravimą nebuvo išsaugotos; pakeista tik `WPLANG` ir su atnaujinimais / prisijungimu susiję WordPress duomenys.
5. Katalogus perjungti neperrašant senos svetainės: naują kopiją pirma iškelti iš seno `public_html`, išsaugoti seną katalogą privačiai, naują pervadinti į `public_html`. Šiuo metu jokio tokio perjungimo neatlikta. `private_html` nuoroda turi likti galiojanti.
6. Po perjungimo patikrinti pagrindinį domeną, HTTPS, visus URL / failus, mobilią versiją, kalbas, formas ir administravimą; tik tada įjungti indeksavimą. Tikrinti ir kitus nei operatoriaus IP klientus.
7. WordPress siūlo 7.1 ir Polylang 3.8.8 atnaujinimus; šioje repeticijoje naudojama išbandyta WordPress 7.0.2 / Polylang 3.7.5. Šie versijų atnaujinimai dar netaikyti. GitHub SSH diegimas į IV neperkonfigūruotas; commit / push neatlikti.

Privatūs perkėlimo failai: `tmp/iv-migration-2026-09-08-TJKJgQ9W` (Git ignoruojamas). Slaptažodžių nuorodų atmintinė: `/Users/eduardas/.config/5gtech/prisijungimai.md`. Apsaugotų SQL ir konfigūracijos archyvų nekelti į viešą katalogą ar Git.

## Ankstesnės eigos žurnalas

Toliau pateikti ankstesnių etapų įrašai. Juose esantys „dar neįkelta / nesukurta / netestuota“ teiginiai aprašo tų etapų momentą; galiojanti būsena pateikta aukščiau.

## Patikrinta

- Prisijungta prie kliento „Interneto vizijos“ paskyros ir „DirectAdmin“.
- Tikslas: `5gtech.lt`, serveris `titnagas.serveriai.lt`, planas „Verslui“.
- Dabartinė serverio PHP versija: 8.1. Nepakeista.
- Veikianti svetainė yra „WordPress“, katalogas `/domains/5gtech.lt/public_html`; `private_html` yra nuoroda į jį.
- Esamos svetainės DB identifikuota pagal `wp-config.php`: `gtchlt_nawsad89asdj`, prefiksas `wp_`, apie 99 MB. Antros paskyros DB paskirtis netirta, jos nekeisti.
- Formų gavėjai vietiniame „WordPress“ ir `deploy/content/snapshot.json`: užklausos `info@5gtech.lt`, CV `atrankos@5gtech.lt`.

## Atlikti serverio veiksmai

- Per failų tvarkyklės „Copy“ nukopijuotas `public_html` į to paties domeno privatų gretimą katalogą `backup-before-migration-2026-09-08`. Esamų failų perrašymas nebuvo pasirinktas.
- Kopijos katalogui nustatytos teisės `700` (tik šiam katalogui, ne rekursyviai).
- Kopijoje matomi WordPress katalogai, konfigūracija ir paslėpti failai. Tai dar **ne patvirtinta pilna atkūrimo kopija**: valdymo pultas šaltiniui rodo 598.4 MB, kopijai 549.6 MB. Reikia failų skaičiaus / kontrolinių sumų palyginimo; vien dydžių skirtumas neįrodo nei praradimo, nei sėkmės.

## Blokeris ir tolesni veiksmai

- Bendras „DirectAdmin“ kopijų įrankis neleidžia kopijuoti failų ir DB duomenų, nes paskyra viršija 10 GB. Metaduomenų kopija neatstoja svetainės kopijos.
- Paspaudus tikslinės DB „Download .gz“, Chrome rodė `ERR_BLOCKED_BY_CLIENT`. Naudotojas vėliau pats atsisiuntė kopiją. Naršyklės blokavimas neapeitas.
- Atsisiųsta `gtchlt_nawsad89asdj_1788859338.sql.gz`: 7 814 986 baitai, išskleidus 78 036 497 baitai; gzip vientisumo patikra sėkminga, yra eksporto pabaigos žyma. SHA-256: `09a2a5b315ede9dc7af0fd93e78cec3068353a497e9799126f24080f83ad90c6`.
- Privati kopija: `tmp/iv-migration-2026-09-08-TJKJgQ9W/old-live.sql.gz` (katalogas 700, failas 600, Git ignoruojamas).
- Atkūrimas išbandytas izoliuotame vietiniame MariaDB 12.3.2 egzemplioriuje be tinklo prieigos: visos 73 lentelės atkurtos, `wp_posts` turi 1607 eilutes, `home` ir `siteurl` yra `https://5gtech.lt`. Lentelių patikra: 72 OK, `wp_wfls_role_counts` saugojimo tipas CHECK nepalaiko; klaidų nepranešta. Testinis serveris po patikros sustabdytas. Tai DB kopijos atkūrimo, ne pilnos svetainės atkūrimo kliento serveryje patvirtinimas.
- Naudotojas sukūrė naują bazę ir naudotoją `gtchlt_site2026`; „DirectAdmin“ rezultatas patvirtintas. Naudotojo prašymu esamas naujos DB slaptažodis išsaugotas vietinėje macOS Keychain `login` saugykloje, įrašu `5gtech.lt — IV MySQL — gtchlt_site2026`. Išsaugojimas patikrintas; naujos DB prisijungimas dar netestuotas. Slaptažodis neįrašytas į Git ar šį dokumentą. Prisijungimų atmintinė už projekto ribų: `/Users/eduardas/.config/5gtech/prisijungimai.md` (600). IV ir DirectAdmin slaptažodžiai šiuo veiksmu neišsaugoti.
- Patikrinti gautą DB archyvą ir failų kopijos pilnumą. Prieš live pakeitimą būtinas patikimas atkūrimo kelias.
- Naują WordPress instaliaciją / MySQL importą išbandyti izoliuotai. Neperrašyti esamos DB ir nediegti vietinės SQLite ar vietinio pašto sulaikymo papildinio.
- Esamas GitHub SSH diegimas šiam IV serveriui dar nepatikrintas ir neperkonfigūruotas. PHP atnaujinimo bei saugaus CLI / importo galimybes patikrinti prieš pakeičiant svetainę.
- Po perkėlimo reikalinga tikslinio serverio HTTPS, LT/EN/DE, mobilių vaizdų, redagavimo ir tikro laiškų bei CV gavimo patikra.

## Naujos svetainės perkėlimo repeticija

- Privačiame `tmp/iv-migration-2026-09-08-TJKJgQ9W/new-site` paruošta atskira WordPress kopija su MySQL/MariaDB baze `g5_new_build`. Vietinis egzempliorius neturi tinklinės DB prieigos; produkcinė DB nepaliesta.
- Importuota 199 turinio įrašai. Po pataisymų daugiakalbystės ir puslapių patikra: **1003 patikros, 0 klaidų**. Naujas logotipo / ikonos ID perkėlimo testas: **5 patikros sėkmingos**.
- Vizualinė repeticija aptiko trūkstamą logotipą: eksportas neįtraukdavo WordPress `site_logo`. Pataisytas eksportas, priedo įtraukimas, importo leidžiamų nustatymų sąrašas ir priedų ID susiejimas. Snapshot papildytas tik logotipo nustatymais bei priedu; likęs patvirtintas turinys nepakeistas.
- Po pakartotinio importo titulinio puslapio logotipas matomas darbalaukyje ir mobiliajame pločiuose. Mobilus meniu atsidaro, jo kontaktų nuoroda veikia; vidiniame puslapyje išbandytas EN → LT → DE kalbų perjungimas. Laikinas naršyklės mobilus plotis atstatytas.
- Atskiroje vietinėje kopijoje yra tik bandomasis administratorius `local_build_only`. Jo ir vietinio `wp-config.php` **neperkelti į kliento serverį**. Galutinis SQL eksportas bei produkcinio administratoriaus sukūrimas dar neatlikti.
- Paruošti du archyvai minėtame privačiame tmp kataloge: `new-site-code.tar.gz` (58 839 656 baitai) ir `new-site-media.tar.gz` (11 866 878 baitai). Archyvų vientisumas patikrintas; juose nėra vietinio `wp-config.php`, SQLite DB, vietinių mu-plugins ar node_modules. Ankstesnio vientiso `new-site-files.tar.gz` **nenaudoti**: jis viršija serverio 64 MB įkėlimo ribą ir yra pasenęs.
- Serverio įkėlimo formoje pasirinktas privatus `/domains/5gtech.lt`, ne `public_html`. Failo pasirinkimo operacija baigėsi laukimo klaida; **joks naujos svetainės archyvas dar neįkeltas**. Reikia naudotojui Chrome plėtinyje įjungti „Allow access to file URLs“, tada pakartoti įkėlimą.
- Serverio periodinių užduočių forma priima URL, ne komandą. CLI-only turinio importo apsauga nesusilpninta ir viešas HTTP importuotojas nekurtas.
- Prieš perjungimą dar būtina: patvirtinti senos svetainės failų kopijos pilnumą, išbandyti naują DB bei importą serveryje, apsaugoti bandomą aplinką, išsaugoti naudojamą seno `/wp-content/uploads/2025/04/` elgesio kodekso PDF ir įvertinti senų adresų nukreipimus. Po perjungimo patikrinti realų užklausų ir CV laiškų gavimą.

## Įkėlimas po Chrome leidimo įjungimo

- Naudotojas įjungė plėtinio prieigą prie failų URL. Pakartotinis failų pasirinkimas ir įkėlimas veikia; ankstesnis įkėlimo blokeris išspręstas.
- `new-site-code.tar.gz` ir `new-site-media.tar.gz` įkelti į `/domains/5gtech.lt`. Abiem atskirai gautas `Upload successful`, failų sąraše matomi atitinkamai 56.1 MiB ir 11.3 MiB dydžiai.
- Abu archyvai išskleisti į `/domains/5gtech.lt`, rezultatai `File Extracted`. Sukurtas **privatus** `/domains/5gtech.lt/new-site`, o ne perrašytas `public_html`. Patikrinti WordPress branduolio katalogai, `wp-content/uploads/2026`; `wp-config.php` sąraše nėra.
- Archyvo peržiūroje serverio tar įrankis ignoravo macOS `LIBARCHIVE.xattr.com.apple.provenance` metaduomenis. Išskleidimas patvirtintas; visų failų kontrolinių sumų patikra serveryje dar neatlikta.
- Vietinių įkeltų archyvų SHA-256: kodas `5a9bebecfcede668e3fb4dfd2b1938c5c2ea829147c91d9eaae17e1b0eb6b48a`; medija `52eb605e65bcbd19f6b85fc34d18e6d42dedee5b02d172998ad6b42b2ef7d7d3`.
- Naujoje kopijoje sukurti `wp-content/uploads/2025/04` katalogai. Esamo elgesio kodekso kopijavimas per DirectAdmin `Copy` grąžino klaidą, todėl nepakeistas viešai pasiekiamas PDF atsisiųstas ir įkeltas į naujos kopijos tą patį santykinį kelią. Įkėlimas patvirtintas `Upload successful`; originalas nepakeistas. Vietinės PDF kopijos SHA-256: `a89c8edebdab0b27ad7655ef2a72654bdb00a137c96b99f49846468bda4f0f0f`.
- DirectAdmin DB sąraše `gtchlt_site2026` vis dar 0 B. Importo forma leidžia pasirinkti būtent šią bazę ir dabartinę DirectAdmin autentifikaciją. **SQL neįkeltas, importas nepaleistas.** Formos numatytoji bazė yra sena — prieš bet kokį importą būtina aiškiai pasirinkti ir patikrinti `gtchlt_site2026`.
- Naudotojo paklausta, kokį el. paštą naudoti pagrindinei naujos WordPress administratoriaus paskyrai. Paskyra dar nesukurta; bandomojo `local_build_only` vartotojo neperkelti.

**Istorinio įkėlimo etapo pabaiga. Naujausia būsena — dokumento pradžioje; prisijungimų paslaptys šiame dokumente nesaugomos.**
