=== AI Provider for Gisoo ===
Contributors: gisooai
Tags: ai, gisoo, artificial-intelligence, connector, llm
Requires at least: 7.0
Tested up to: 7.1
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Gisoo as an AI provider to the WordPress AI Client, so any plugin that uses it can run on hundreds of models with one Gisoo key.

== Description ==

WordPress ships an official AI Client. Plugins such as the WordPress AI plugin ask it for a model, and it sends the request to whichever provider the site has connected. This plugin adds **Gisoo** to that list.

Gisoo gives one API key access to hundreds of models from many labs. Once you add your key, every plugin that uses the WordPress AI Client can choose Gisoo and its models.

**What you get**

* Text generation and chat with every Gisoo chat model
* Function (tool) calling, JSON output and image input, on the models that support them
* Image generation with the Gisoo image models
* The model list is read from Gisoo automatically, so new models appear without a plugin update
* The provider shows up in Settings > Connectors, where you paste your key
* A missing or wrong key is handled gracefully: Gisoo is simply shown as not connected

Embeddings, speech, music and video models are not exposed yet, because the WordPress AI Client in current WordPress versions does not offer them to this kind of provider.

== External services ==

This plugin connects to the Gisoo API (https://gisoo.pro/api/v1) to list the available AI models and to generate text and images. A Gisoo account and API key are required. You can create a key at https://gisoo.pro/app/api/keys.

What is sent, and when:

* **Model list.** When WordPress needs to know which models exist (for example when a plugin asks the AI Client for a model, or on the Connectors screen), the plugin requests `GET /models`. This request carries no key and no personal data, and the answer is cached by the AI Client for 24 hours.
* **Key check.** When WordPress checks whether Gisoo is connected, the plugin requests `GET /me` with your API key. The request is free and returns the account behind the key.
* **Generation.** Only when another plugin or feature on your site asks for AI output through Gisoo, the plugin sends that request to `/chat/completions` or `/images/generations` together with your API key. The request contains the prompt and any content the calling plugin includes (for example post text or an image), the chosen model and the generation settings.

Nothing is sent until you add an API key and a feature on your site uses Gisoo, except the key-less model list request.

The service is provided by Gisoo: Terms of Service https://gisoo.pro/terms, Privacy Policy https://gisoo.pro/privacy.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/ai-provider-for-gisoo/`, or install the zip from Plugins > Add New > Upload Plugin.
2. Activate the plugin through the Plugins screen.
3. Create a key at https://gisoo.pro/app/api/keys.
4. Open Settings > Connectors, find Gisoo, and paste the key.
5. In any plugin that uses the WordPress AI Client, choose Gisoo as the provider.

You can also define the key in `wp-config.php` or as an environment variable, the same way as for other AI Client providers: `GISOO_API_KEY`.

== Frequently Asked Questions ==

= Do I need the AI plugin? =

No. The AI Client is part of WordPress itself. You need a plugin or feature that uses it, such as the WordPress AI plugin.

= Where do I get an API key? =

Sign in at https://gisoo.pro and create a key at https://gisoo.pro/app/api/keys.

= What happens if my key is wrong or missing? =

Gisoo is shown as not connected in Settings > Connectors and AI features do not use it. Nothing breaks on your site.

= Can I point the plugin to another API address? =

Yes. Use the `ai_provider_for_gisoo_base_url` filter, for example for a proxy. The default is `https://gisoo.pro/api/v1`.

= Which models are available? =

All chat and image models Gisoo lists at https://gisoo.pro/api/v1/models, with their capabilities (tools, JSON output, vision).

== Changelog ==

= 1.0.0 =

* First release
* Gisoo registered as a provider in the WordPress AI Client
* Chat and image models discovered from the Gisoo API, with tools, JSON and vision capabilities
* Key check through the free GET /me endpoint
