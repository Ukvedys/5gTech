# Redaktoriaus UX taisymo planas

2026-08-03. Būsena: visi puslapiai redaguojami vienoje vietoje (Puslapiai → blokų redaktorius), bet redaktorius nerodo dizaino, dinaminės sekcijos rodo tik užrašus, nėra kalbų perjungimo.

Principai: joks darbas nepradedamas be atskiro patvirtinimo; kiekvienas etapas baigiasi matomu rezultatu, kurį patikrini pats; visi pakeitimai pirmiausia patikrinami debesies kopijoje palyginant visus viešus puslapius su etalonu (dizainas negali pasikeisti).

---

## 1 etapas. Redaktorius atrodo kaip svetainė

Problema, kurią sprendžia: „nesuprantu, ką valdau" — nėra spalvų, fonų, nuotraukų, dinaminės sekcijos rodo tik tekstą-pakaitalą.

Darbai:

1.1. Svetainės stilius redaktoriuje. Į blokų redaktorių įkeliamas temos CSS ir šriftai (WordPress `add_editor_style` mechanizmas). Tamsios sekcijos taps tamsios, antraštės — tikro dydžio ir spalvos, tinklelio linijos matomos. Pastaba: svetainės CSS rašytas viešam puslapiui, todėl dalis taisyklių redaktoriuje reikalaus korekcijų — tam skirta didžioji šio punkto darbo dalis.

1.2. Tikros dinaminių sekcijų peržiūros. Sekcijos, kurios turinį ima iš katalogų (Paslaugų kortelės, Projektai, Naujienos, Darbo skelbimai, Partnerių logotipai, Komanda, Kontaktų kortelės, formos), redaktoriuje rodys tikrą savo vaizdą su tikrais duomenimis (WordPress `ServerSideRender`). Vietoj „Kortelės imamos iš skilties Paslaugos" matysi pačias korteles.

1.3. Nuotraukų blokai. Sekcijos su nuotraukomis (media-frame, hero) redaktoriuje rodys pasirinktą nuotrauką, o ne tuščią vietą.

1.4. Patikra: debesies kopijoje atidaromi visi 16 puslapių redaktoriuje, palyginami visi 50 viešų maršrutų su etalonu (vieša svetainė nepakinta — redaktoriaus pakeitimai jos neliečia).

Ką pamatysi pats: atidarai Puslapiai → Pagrindinis — matai tamsų hero su šūkiu, paslaugų korteles su numeriais, naujienas; spusteli tekstą — redaguoji vietoje; pliusu įterpi naują sekciją ir ji iškart atrodo kaip svetainėje.

Apimtis: vienas darbo seansas, ~46 blokai (daugumai užtenka CSS, ~15 dinaminių reikia peržiūrų).

---

## 2 etapas. Kalbos (Polylang)

Problema, kurią sprendžia: redaktoriuje nėra kaip perjungti ir redaguoti EN/DE tekstų (seni ekranai su LT/EN/DE tabais išjungti, o pakaitalas dar nepadarytas).

Darbai:

2.1. Įdiegiamas Polylang; kalbos LT (numatytoji), EN, DE su dabartiniais adresų prefiksais /en/ ir /de/ — vieši URL nesikeičia, SEO nenukenčia.

2.2. EN/DE puslapių kopijos sugeneruojamos automatiškai iš dabartinio vertimų katalogo (en.php / de.php + tavo darytos pataisos). Nieko iš naujo versti nereikia — skriptas perkelia esamus vertimus į blokus.

2.3. Kalbų perjungiklis: redaktoriuje prie kiekvieno puslapio atsiranda LT/EN/DE, viešoje svetainėje kalbų meniu veikia kaip dabar.

2.4. Katalogo įrašai (paslaugos, projektai, naujienos, darbo pozicijos, DUK klausimai): etapo pradžioje kartu nusprendžiam, kurie verčiami per Polylang, o kurie laikinai lieka su dabartiniu katalogu. Tai atskiras sprendimo taškas — be jo nepradedama.

2.5. Senasis vertimų sluoksnis išjungiamas tik tada, kai visi 56 maršrutai LT/EN/DE patikrinti debesies kopijoje.

Ką pamatysi pats: atidarai puslapį, viršuje pasirenki EN — matai anglišką turinį ir keiti jį taip pat, kaip lietuvišką; svetainėje visos trys kalbos veikia kaip iki šiol.

Apimtis: didžiausias likęs darbas, skaidomas į 2–3 patikros taškus (diegimas ir LT veikimas → EN/DE kopijos → seno sluoksnio išjungimas). Po kiekvieno — tavo patikra.

---

## 3 etapas. Smulkus valymas (kai 1–2 baigti)

3.1. Paslėpti „Turinio moduliai" meniu — puslapiuose moduliai nebenaudojami, meniu tik klaidina.
3.2. Pataisyti „1&1" simbolių kodavimą Patirties puslapyje (vizualiai nesimato, bet netvarkinga).
3.3. Išvalyti negyvą senų ekranų kodą iš papildinio, kad jį būtų pigiau prižiūrėti ir atnaujinti.

Ką pamatysi pats: administravimo meniu liks tik tai, kas realiai naudojama.

---

## Eiga ir stabdžiai

Kiekvienam etapui: (1) patvirtini pradžią, (2) darbas daromas debesies kopijoje, (3) automatinis palyginimas su etalonu, (4) pakeitimai perkeliami į tavo diską ir įrašomi į git, (5) tu patikrini pagal „ką pamatysi" sąrašą. Jei patikra nepavyksta — grąžinama per git, niekas neliečia veikiančios svetainės.
