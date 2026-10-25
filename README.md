# AI Provider for Gisoo

Adds [Gisoo](https://gisoo.pro) as a provider to the WordPress AI Client (`WordPress/php-ai-client` and `wp-ai-client`, bundled with WordPress 7.0+). Any plugin that uses the AI Client, such as the WordPress AI plugin, can then run on Gisoo's models with one API key.

- Provider id: `gisoo`
- Chat models (tools, JSON output and vision where the model supports them) and image models
- Model list read live from `GET https://gisoo.pro/api/v1/models`
- Key checked with the free `GET /me`
- Key entered in **Settings > Connectors**

## Install from GitHub

1. Download `ai-provider-for-gisoo.zip` from the [latest release](https://github.com/netarz/ai-provider-for-gisoo/releases/latest).
2. In WordPress go to **Plugins > Add New > Upload Plugin**, choose the zip and click **Install Now**, then **Activate**.
3. Create an API key at <https://gisoo.pro/app/api/keys>.
4. Open **Settings > Connectors**, find **Gisoo** and paste the key.
5. In any plugin that uses the AI Client, choose Gisoo as the provider.

Requires WordPress 7.0 or newer and PHP 7.4 or newer.

## Configuration

The API address can be changed with a filter (for a proxy, for example):

```php
add_filter( 'ai_provider_for_gisoo_base_url', function () {
	return 'https://gisoo.pro/api/v1';
} );
```

## External service

The plugin talks to the Gisoo API at `https://gisoo.pro/api/v1`: `GET /models` (no key), `GET /me` (key check) and, only when a feature on your site asks for AI output, `/chat/completions` and `/images/generations`. See the [Terms](https://gisoo.pro/terms) and [Privacy Policy](https://gisoo.pro/privacy).

## License

GPL-2.0-or-later.
