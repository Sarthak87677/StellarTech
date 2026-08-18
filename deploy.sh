#!/usr/bin/env bash
# Sync assets to the served web root and clear caches.
# Run from the Laravel project root on the server.
set -e

WEB_ROOT="../public_html"

if [ ! -d "$WEB_ROOT" ]; then
  echo "ERROR: $WEB_ROOT not found. Run this from the Laravel project root."
  exit 1
fi

echo "→ copying public/ to $WEB_ROOT"
cp -r public/assets/.  "$WEB_ROOT/assets/"
cp -r public/images/.  "$WEB_ROOT/images/"

echo "→ bumping ASSET_VERSION"
CURRENT=$(grep '^ASSET_VERSION=' .env | cut -d= -f2)
NEXT=$((CURRENT + 1))
sed -i "s/^ASSET_VERSION=.*/ASSET_VERSION=$NEXT/" .env
echo "  ASSET_VERSION $CURRENT → $NEXT"

echo "→ rebuilding caches"
php artisan optimize:clear
php artisan optimize

echo "✓ done. Hard-refresh with Ctrl+Shift+R."
