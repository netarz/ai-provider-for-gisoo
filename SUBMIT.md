# Submitting to WordPress.org

Maintainer notes. Not part of the plugin zip.

## When

Submit under the WordPress.org account `gisooai` only after the plugin "Gisoo AI" (slug `gisoo-ai`) has been approved. WordPress.org allows one plugin in review per account at a time.

## Steps

1. Build the zip: `bin/build-zip.sh` (writes `../dist/ai-provider-for-gisoo.zip`).
2. Sign in as `gisooai` and open <https://wordpress.org/plugins/developers/add/>.
3. Upload the zip. The requested slug is `ai-provider-for-gisoo`. Answer review emails from the plugin team within a few days.
4. After approval, deploy with SVN:
   ```bash
   svn co https://plugins.svn.wordpress.org/ai-provider-for-gisoo svn-gisoo
   cp -r ai-provider-for-gisoo/* svn-gisoo/trunk/
   cp -r assets-wporg/* svn-gisoo/assets/      # banners, icons, screenshots, if any
   cd svn-gisoo && svn add --force trunk assets
   svn cp trunk tags/1.0.0
   svn ci -m "Release 1.0.0"
   ```
5. Keep `Stable tag` in `readme.txt` equal to the released version, and bump the version in the plugin header, `readme.txt` and the changelog together.

## Before every submission

- `php vendor/bin/phpcs` (uses `phpcs.xml.dist`) is clean.
- Plugin Check (`wp plugin check ai-provider-for-gisoo`) reports no errors.
