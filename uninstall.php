<?php
/**
 * Fires when the plugin is uninstalled.
 *
 * The plugin stores no options of its own: the API key lives in the WordPress
 * Connectors settings and is managed by WordPress.
 *
 * @package AiProviderForGisoo
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
