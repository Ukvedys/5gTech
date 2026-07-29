# 5G TECH SEO ir 301 nukreipimų planas

## Jau įgyvendinta lokaliai

- automatiniai `title` iš WordPress puslapio ar įrašo pavadinimo;
- automatiniai `meta description` iš jau administruojamų santraukų;
- `canonical` nuorodos per WordPress branduolį;
- Open Graph ir X / Twitter kortelių žymos;
- `Organization`, `WebSite`, `WebPage`, `Service`, `CreativeWork` ir `Article` struktūriniai duomenys;
- WordPress XML sitemap;
- autorių sitemap išjungtas;
- nevieši projektai, uždarytos pozicijos ir nevieši komandos profiliai neįtraukiami į sitemap ir paiešką;
- firminiai 404 ir paieškos puslapiai;
- paieškos ir 404 puslapiai neindeksuojami pagal WordPress taisykles.

## Patvirtinti 301 nukreipimai

| Senas adresas | Naujas adresas |
|---|---|
| `/5gtech-telecom-academy/` | `/akademija/` |
| `/rinkis-mus/` | `/patirtis/` |
| `/karjeros-forma/` | `/kandidatuoti/` |

Šie adresai rasti veikiančioje `5gtech.lt` svetainėje 2026-07-29 ir jau nukreipiami lokaliai.

## Prieš paleidimą

1. Iš produkcinio serverio ir „Google Search Console“ eksportuoti visus indeksuotus URL.
2. Prieš keičiant svetainę išsaugoti galutinį senų URL sąrašą.
3. Kiekvienam pasikeitusiam adresui priskirti artimiausią naują puslapį.
4. Netinkamus ar nebeaktualius adresus nukreipti tik tada, kai yra prasmingas atitikmuo; kitais atvejais palikti 404 arba 410.
5. Įjungus LT, EN ir DE versijas pridėti `hreflang` tik tarp tikrai susietų vertimų.
6. Po paleidimo patikrinti sitemap, 301 grandines, canonical, 404 ir „Search Console“ indeksavimo ataskaitą.

## Integracija su SEO papildiniu

Bazinis funkcionalumas yra `5gtech-core` papildinyje. Jei ateityje bus įjungtas „Yoast SEO“, „Rank Math“, „SEOPress“ arba „All in One SEO“, nuosavos meta ir struktūrinių duomenų žymos automatiškai nebus išvedamos, kad nesidubliuotų.
