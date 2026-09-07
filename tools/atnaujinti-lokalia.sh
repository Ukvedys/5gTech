#!/bin/bash
# Lokalios svetaines turinio atnaujinimas is deploy/content/snapshot.json.
# Naudojimas: bash tools/atnaujinti-lokalia.sh
set -e
cd "$(dirname "$0")/.."
if [ "${1:-}" != "--import-content" ]; then
  echo "Import disabled. After backing up the database: bash tools/atnaujinti-lokalia.sh --import-content"
  exit 0
fi
php deploy/sync-content.php --wordpress="$PWD/wordpress" --import-content
