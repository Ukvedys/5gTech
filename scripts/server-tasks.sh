#!/usr/bin/env bash
# Serverio priežiūros užduotys.
#
# Šį failą vykdo GitHub Actions kiekvieno diegimo pabaigoje, prisijungęs
# prie serverio, WordPress šakniniame kataloge (ten, kur wp-config.php).
#
# TAISYKLĖ: kiekviena užduotis privalo būti idempotentiška — saugu ją
# paleisti daug kartų iš eilės. Vienkartinės užduotys, kai atliktos,
# iš failo IŠTRINAMOS.
set -euo pipefail

echo "== Serverio užduotys =="

# Išvalyti archyvus, per klaidą paliktus mu-plugins kataloge.
rm -f wp-content/mu-plugins/uploads-turinys.zip
rm -f wp-content/mu-plugins/uploads-wp-content.zip

# Išvalyti LiteSpeed kešą, kad diegimo pakeitimai matytųsi iš karto.
wp litespeed-purge all 2>/dev/null || true

echo "== Užduotys baigtos =="
