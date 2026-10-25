<?php
/**
 * PSR-4 autoloader for the AI Provider for Gisoo plugin.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	static function ( string $class_name ): void {
		$prefix = 'Gisoo\\AiProviderForGisoo\\';
		$length = strlen( $prefix );

		if ( strncmp( $class_name, $prefix, $length ) !== 0 ) {
			return;
		}

		$relative = substr( $class_name, $length );
		$file     = __DIR__ . '/' . str_replace( '\\', '/', $relative ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
