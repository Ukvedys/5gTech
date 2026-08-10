#!/bin/bash
# Lokalios svetaines turinio atnaujinimas is deploy/content/snapshot.json.
# Naudojimas: bash tools/atnaujinti-lokalia.sh
set -e
cd "$(dirname "$0")/.."
rm -f .git/HEAD.lock .git/index.lock
mkdir -p wordpress/wp-content/g5-deploy
cp deploy/sync-content.php deploy/polylang-shared.php wordpress/wp-content/g5-deploy/
cp -R deploy/content wordpress/wp-content/g5-deploy/
php wordpress/wp-content/g5-deploy/sync-content.php
rm -rf wordpress/wp-content/g5-deploy
echo ""
echo "Lokalus turinys atnaujintas. Jei viskas gerai - paleisk: git push"
