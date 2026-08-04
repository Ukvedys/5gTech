# Diegimas iš GitHub į Hostinger

Kaip veikia: kiekvienas `push` į `main`, palietęs temą, papildinius arba `deploy/` turinį, automatiškai sukompiliuoja blokus ir atnaujina **staging** svetainę. Prieš turinio sinchronizavimą sukuriama duomenų bazės atsarginė kopija, tada įkeliama tema, `5gtech-core`, `Polylang`, naudojamos nuotraukos ir vietinio WordPress turinio momentinis įrašas.

WordPress branduolys, vartotojai ir kiti papildiniai neliečiami. Į `live` vis dar diegiama tik rankiniu būdu.

---

## 1. Vienkartinis paruošimas

### 1.1. SSH raktas

Savo kompiuterio terminale:

```bash
ssh-keygen -t ed25519 -C "github-deploy-5gtech" -f ~/.ssh/5gtech_deploy -N ""
```

Sukuriami du failai:

- `~/.ssh/5gtech_deploy.pub` — **viešasis**, jį dedi į Hostinger
- `~/.ssh/5gtech_deploy` — **privatusis**, jį dedi į GitHub Secrets

Viešojo rakto turinį pamatysi:

```bash
cat ~/.ssh/5gtech_deploy.pub
```

Hostinger hPanel → **Advanced → SSH Access → SSH Keys → Add SSH key** → įklijuoji viešąjį raktą.

### 1.2. GitHub Secrets

GitHub repozitorijoje: **Settings → Secrets and variables → Actions → New repository secret**.

Sukurk šešis:

| Pavadinimas | Reikšmė | Kur rasti |
|---|---|---|
| `SSH_HOST` | serverio adresas arba IP | hPanel → Advanced → SSH Access |
| `SSH_PORT` | prievadas (Hostinger dažniausiai `65002`) | ten pat |
| `SSH_USER` | vartotojas (pvz. `u123456789`) | ten pat |
| `SSH_PRIVATE_KEY` | **viso** privataus rakto failo turinys | `cat ~/.ssh/5gtech_deploy` |
| `REMOTE_PATH_STAGING` | staging svetainės kelias | žr. žemiau |
| `REMOTE_PATH_LIVE` | pagrindinės svetainės kelias | žr. žemiau |

Privatų raktą kopijuok **visą**, kartu su eilutėmis `-----BEGIN OPENSSH PRIVATE KEY-----` ir `-----END OPENSSH PRIVATE KEY-----`.

### 1.3. Kelias serveryje

Prisijungęs per SSH:

```bash
ssh -p 65002 uXXXXXXXXX@serverio-adresas
ls ~/domains
```

Kelias atrodys maždaug taip:

```
/home/uXXXXXXXXX/domains/tavo-domenas.lt/public_html
```

Būtent jį (be pabaigos brūkšnio) įrašai į `REMOTE_PATH_LIVE`. Staging aplinka Hostinger'yje turi savo atskirą katalogą — jį rasi hPanel staging skiltyje.

---

## 2. Kasdienis naudojimas

**Automatiškai:** bet koks `push` į `main`, palietęs temą, papildinius arba `deploy/`, diegia į **staging**.

**Rankiniu būdu:** GitHub → **Actions → Diegimas į serverį → Run workflow** → pasirink `staging` arba `live`.

Į `live` diegiama tik rankiniu būdu — sąmoningai, kad atsitiktinis push'as nepakeistų veikiančios svetainės.

---

## 3. Ką daro kiekvienas diegimas

1. Patikrina **visų** projekto PHP failų sintaksę — jei kur nors klaida, diegimas nutrūksta ir serveris lieka nepaliestas.
2. Sukompiliuoja blokus (`npm ci && npm run build`) ir patikrina, ar rezultatas atsirado.
3. Patikrina WordPress katalogą ir per `mariadb-dump` arba `mysqldump` padaro duomenų bazės kopiją už viešo svetainės katalogo.
4. Per `rsync` įkelia temą, `5gtech-core` ir `Polylang`.
5. Įkelia `deploy/content/snapshot.json`, sinchronizavimo scenarijų ir momentiniame įraše naudojamas nuotraukas.
6. Jei yra `deploy/content/SYNC-ON`, serveryje pritaiko vietinį turinį ir išvalo kešą.

Į serverį **nekeliama:** `node_modules/`, `blocks-src/` (šaltiniai — serveryje jų nereikia), `package-lock.json`.

`--delete` reiškia, kad temos, `5gtech-core`, `Polylang` ir `g5-deploy` kataloguose ištrinami failai, kurių repozitorijoje nebėra. Todėl šių katalogų **serveryje ranka redaguoti negalima** — pakeitimai dings per kitą diegimą. Kiti `uploads/` failai automatiškai netrinami.

---

## 4. Turinio perkėlimas

Kol repozitorijoje yra `deploy/content/SYNC-ON`, vietinė WordPress svetainė yra turinio šaltinis:

1. Turinys redaguojamas vietinėje WordPress kopijoje.
2. Paleidžiama `php tools/export-content-snapshot.php`.
3. Atnaujintas `deploy/content/snapshot.json` ir `deploy/uploads/` įrašomi į Git.
4. `push` į `main` automatiškai atnaujina staging svetainę.

Sinchronizuojami puslapiai, naujienos, komanda, paslaugos, projektai, darbo pozicijos, DUK, partneriai, turinio moduliai, jų kalbos, vertimų ryšiai, turinio laukai ir naudojamos nuotraukos. Serverio bandomieji valdomų tipų įrašai, kurių vietinėje kopijoje nėra, pašalinami.

Duomenų bazė iš SQLite į MySQL tiesiogiai nekopijuojama. Turinys perkeliamas aplinkoms neutraliu JSON momentiniu įrašu.

---

## 5. Kada išjungti turinio perrašymą

Kol yra `deploy/content/SYNC-ON`, serveryje per WordPress administravimą atlikti turinio pakeitimai per kitą diegimą bus perrašyti vietine versija.

Kai `5gtech.lt` paleidžiama gyvai ir turinys pradedamas redaguoti tiesiogiai serveryje:

1. Ištrinamas `deploy/content/SYNC-ON`.
2. Pakeitimas įrašomas ir išsiunčiamas į Git.
3. Toliau diegimas atnaujina kodą bei `Polylang`, bet turinio nebepakeičia.

---

## 6. Saugumas

- Slaptažodžiai ir raktai gyvena tik GitHub Secrets. Kode ir susirašinėjime jų nėra.
- Privatus raktas iš GitHub Secrets neišimamas — jį galima tik perrašyti nauju.
- Jei raktas kada nors nutekėtų: pašalink viešąjį raktą Hostinger hPanel'e ir sukurk naują porą.
- Rekomenduojama Hostinger'yje įjungti dviejų veiksnių patvirtinimą.
