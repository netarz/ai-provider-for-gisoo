<?php
/**
 * Plugin Name:       AI Provider for Gisoo
 * Plugin URI:        https://github.com/netarz/ai-provider-for-gisoo
 * Description:       Gisoo AI provider for the WordPress AI Client: chat, reasoning, vision, tool calling and image generation with hundreds of models through one key.
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            Gisoo
 * Author URI:        https://gisoo.pro/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ai-provider-for-gisoo
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo;

use Gisoo\AiProviderForGisoo\Provider\GisooProvider;
use WordPress\AiClient\AiClient;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

define( 'AI_PROVIDER_FOR_GISOO_VERSION', '1.0.0' );
define( 'AI_PROVIDER_FOR_GISOO_DIR', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/src/autoload.php';

/**
 * Registers the Gisoo provider with the AI Client.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_provider(): void {
	if ( ! class_exists( AiClient::class ) ) {
		return;
	}

	$registry = AiClient::defaultRegistry();

	if ( $registry->hasProvider( GisooProvider::class ) ) {
		return;
	}

	$registry->registerProvider( GisooProvider::class );
}

add_action( 'init', __NAMESPACE__ . '\\register_provider', 5 );
