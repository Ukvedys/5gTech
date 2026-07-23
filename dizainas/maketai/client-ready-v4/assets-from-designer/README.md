# SVG failų perdavimas

Šio aplanko failai bus tiesiogiai naudojami HTML makete, todėl dizainas išliks toks, kokį nupiešė dizaineris.

## 1. Hero nuotraukos kaukė

Failo pavadinimas: `hero-mask.svg`

- `viewBox="0 0 820 1220"`
- viena uždara, baltai užpildyta forma;
- be kontūro, šešėlių, filtrų ar įterptos nuotraukos;
- viskas, kas balta, bus matoma nuotraukoje;
- fonas turi likti skaidrus.

Pradžiai galima naudoti `hero-mask-template.svg`. Jo pilka forma rodo dabartinę kaukę ir gali būti pakeista nauja.

Jeigu rausva banga turi tiksliai sekti kaukę, ją pateikti atskirai:

- failas: `hero-brand-wave.svg`;
- tas pats `viewBox="0 0 820 1220"`;
- tik galutinė rausva / oranžinė forma, skaidriame fone.

## 2. Ikonos

Kiekviena ikona pateikiama atskiru SVG:

- `viewBox="0 0 64 64"`;
- be fono;
- kontūrai: `#073657`;
- rekomenduojamas kontūro storis: `1.5–1.75`;
- `stroke-linecap="round"` ir `stroke-linejoin="round"`;
- nereikia nustatyti galutinio dydžio pikseliais — dydį valdys HTML.

Reikalingi failai:

1. `icon-mobile-tower.svg`
2. `icon-indoor-radio.svg`
3. `icon-fiber-network.svg`
4. `icon-electricity.svg`
5. `icon-security-camera.svg`
6. `icon-solar.svg`
7. `icon-building.svg`

Tos pačios ikonos bus panaudotos ir paslaugų tinklelyje, ir didesnėje projekto istorijos linijoje, todėl visa sistema atrodys vientisa.

## 3. Hero nuotrauka

Kad nuotrauka nepabirtų, rekomenduojamas šaltinis:

- mažiausiai 2400 × 3000 px;
- pageidautina 3000 px ar daugiau pločio;
- JPG, PNG arba WebP;
- be užrašų ir logotipų nuotraukoje.

Failo pavadinimas: `hero-photo.jpg` arba `hero-photo.png`.

## Darbo eiga

1. Dizaineris įkelia kaukę ir ikonų SVG į šį aplanką.
2. SVG failai įdedami į HTML nekeičiant jų geometrijos.
3. Paruošiamas naujas PNG ir palyginimas su pasirinkta kryptimi.
4. Tik patvirtinus geometriją koreguojami tarpai, šriftų dydžiai ir smulkūs akcentai.
