# 5G TECH projekto darbo taisyklės

## Produkcinis šaltinis

- Viešos svetainės šaltinis yra `wordpress/wp-content/themes/5gtech` ir `wordpress/wp-content/plugins/5gtech-core`.
- Valdomas svetainės turinys saugomas „WordPress“ įrašuose ir `deploy/content/snapshot.json` sinchronizavimo kopijoje.
- Katalogas `dizainas/maketai` yra tik dizaino ir ankstesnių sprendimų nuoroda. Jame nekeisti galutinės svetainės turinio, navigacijos, kalbų ar funkcijų, nebent naudotojas aiškiai paprašo atnaujinti patį statinį maketą.

## Svetainės pakeitimų eiga

1. Pakeitimą įgyvendinti „WordPress“ temoje, `5gtech-core` papildinyje arba valdomame „WordPress“ turinyje.
2. Kalboms naudoti esamą „WordPress“ / „Polylang“ mechanizmą ir `5gtech-core` vertimų sluoksnį. Nekurti atskiro prototipo vertimo mechanizmo produkcijai.
3. Prieš užbaigiant vizualiai ir funkciškai patikrinti vietinę svetainę adresu `http://5gtech.test` bent darbalaukio ir mobiliajame pločiuose.
4. Patikrinti titulinį ir bent vieną vidinį puslapį, bendrą antraštę, mobilų meniu ir kalbų perjungimą.
