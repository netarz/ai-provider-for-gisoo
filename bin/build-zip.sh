#!/usr/bin/env bash
# Builds the WordPress.org-ready zip (no dev files) into ../dist/.
set -euo pipefail
cd "$(dirname "$0")/.."
SLUG=ai-provider-for-gisoo
OUT="$(pwd)/../dist"
rm -rf "$OUT/$SLUG" "$OUT/$SLUG.zip"
mkdir -p "$OUT/$SLUG"
cp -r ai-provider-for-gisoo.php readme.txt LICENSE index.php uninstall.php src assets "$OUT/$SLUG/"
(cd "$OUT" && zip -qr "$SLUG.zip" "$SLUG")
echo "$OUT/$SLUG.zip"
