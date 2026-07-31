# Diegimas iš GitHub į Hostinger

Kaip veikia: kiekvienas `push` į `main`, palietęs temą arba papildinį, automatiškai sukompiliuoja blokus ir įkelia **tik** temą ir papildinį. WordPress branduolys, svetimi papildiniai, `uploads/` ir duomenų bazė nepaliečiami niekada.

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

**Automatiškai:** bet koks `push` į `main`, palietęs temą ar papildinį, diegia į **staging**.

**Rankiniu būdu:** GitHub → **Actions → Diegimas į serverį → Run workflow** → pasirink `staging` arba `live`.

Į `live` diegiama tik rankiniu būdu — sąmoningai, kad atsitiktinis push'as nepakeistų veikiančios svetainės.

---

## 3. Ką daro kiekvienas diegimas

1. Patikrina **visų** PHP failų sintaksę — jei kur nors klaida, diegimas nutrūksta ir serveris lieka nepaliestas.
2. Sukompiliuoja blokus (`npm ci && npm run build`) ir patikrina, ar rezultatas atsirado.
3. Per `rsync` įkelia temą ir papildinį.

Į serverį **nekeliama:** `node_modules/`, `blocks-src/` (šaltiniai — serveryje jų nereikia), `package-lock.json`.

`--delete` reiškia, kad serveryje ištrinami failai, kurių repozitorijoje nebėra. Todėl temos ir papildinio katalogų **serveryje ranka redaguoti negalima** — pakeitimai dings per kitą diegimą. Visi pakeitimai eina tik per git.

---

## 4. Pirmasis turinio perkėlimas

Kodas keliauja per git, o turinys — atskirai, vieną kartą:

1. Hostinger'yje įdiegiamas švarus WordPress (MySQL).
2. Paleidžiamas diegimas — atsiranda tema ir papildinys.
3. WordPress administracijoje: **Įrankiai → Importavimas → WordPress**, įkeliamas `demo/content.xml`.
4. Į `wp-content/mu-plugins/` įkeliamas `demo/demo-seed.php` — jis užpildo visus 5G TECH nustatymus ir atkuria modulių priskyrimus.
5. Patikrinama, ar puslapiai atsidaro, ir nustatoma nuolatinių nuorodų struktūra `/%postname%/`.

Duomenų bazė iš SQLite į MySQL **neperkeliama tiesiogiai** — nereikia. Turinys pereina per eksportą, o nustatymai per seed failą. Taip išvengiama formatų konvertavimo.

---

## 5. Saugumas

- Slaptažodžiai ir raktai gyvena tik GitHub Secrets. Kode ir susirašinėjime jų nėra.
- Privatus raktas iš GitHub Secrets neišimamas — jį galima tik perrašyti nauju.
- Jei raktas kada nors nutekėtų: pašalink viešąjį raktą Hostinger hPanel'e ir sukurk naują porą.
- Rekomenduojama Hostinger'yje įjungti dviejų veiksnių patvirtinimą.
