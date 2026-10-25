<?php
/**
 * Gisoo provider for the WordPress AI Client.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo\Provider;

use Gisoo\AiProviderForGisoo\Http\GisooProviderAvailability;
use Gisoo\AiProviderForGisoo\Metadata\GisooModelMetadataDirectory;
use Gisoo\AiProviderForGisoo\Models\GisooImageGenerationModel;
use Gisoo\AiProviderForGisoo\Models\GisooTextGenerationModel;
use WordPress\AiClient\AiClient;
use WordPress\AiClient\Common\Exception\RuntimeException;
use WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;
use WordPress\AiClient\Providers\Models\Contracts\ModelInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;

/**
 * Class for the Gisoo provider.
 *
 * Gisoo exposes an OpenAI-compatible API, so the OpenAI-compatible base classes
 * of the AI Client do the heavy lifting.
 *
 * @since 1.0.0
 */
class GisooProvider extends AbstractApiProvider {

	/**
	 * Default base URL of the Gisoo API.
	 *
	 * @since 1.0.0
	 */
	public const DEFAULT_BASE_URL = 'https://gisoo.pro/api/v1';

	/**
	 * Page where users create an API key.
	 *
	 * @since 1.0.0
	 */
	public const CREDENTIALS_URL = 'https://gisoo.pro/app/api/keys';

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function baseUrl(): string {
		/**
		 * Filters the base URL of the Gisoo API.
		 *
		 * @since 1.0.0
		 *
		 * @param string $base_url Base URL, without a trailing slash.
		 */
		$base_url = apply_filters( 'ai_provider_for_gisoo_base_url', self::DEFAULT_BASE_URL );

		if ( ! is_string( $base_url ) || '' === $base_url ) {
			$base_url = self::DEFAULT_BASE_URL;
		}

		return untrailingslashit( $base_url );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param ModelMetadata    $model_metadata    Model metadata.
	 * @param ProviderMetadata $provider_metadata Provider metadata.
	 * @throws RuntimeException If the model has no supported capability.
	 */
	protected static function createModel(
		ModelMetadata $model_metadata,
		ProviderMetadata $provider_metadata
	): ModelInterface {
		foreach ( $model_metadata->getSupportedCapabilities() as $capability ) {
			if ( $capability->isTextGeneration() ) {
				return new GisooTextGenerationModel( $model_metadata, $provider_metadata );
			}
			if ( $capability->isImageGeneration() ) {
				return new GisooImageGenerationModel( $model_metadata, $provider_metadata );
			}
		}

		throw new RuntimeException(
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			'Unsupported capabilities for Gisoo model ' . $model_metadata->getId()
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function createProviderMetadata(): ProviderMetadata {
		$args = array(
			'gisoo',
			'Gisoo',
			ProviderTypeEnum::cloud(),
			self::CREDENTIALS_URL,
			RequestAuthenticationMethod::apiKey(),
		);

		// Provider description support was added in AI Client 1.2.0.
		if ( version_compare( AiClient::VERSION, '1.2.0', '>=' ) ) {
			$args[] = __( 'Chat, reasoning, vision, tool calling and image generation with hundreds of AI models through one Gisoo API key.', 'ai-provider-for-gisoo' );
		}

		// Provider logo support was added in AI Client 1.3.0.
		if ( version_compare( AiClient::VERSION, '1.3.0', '>=' ) ) {
			$args[] = AI_PROVIDER_FOR_GISOO_DIR . 'assets/images/gisoo.svg';
		}

		return new ProviderMetadata( ...$args );
	}

	/**
	 * Checks the key against the free GET /me endpoint.
	 *
	 * @since 1.0.0
	 */
	protected static function createProviderAvailability(): ProviderAvailabilityInterface {
		return new GisooProviderAvailability( static::modelMetadataDirectory() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface {
		return new GisooModelMetadataDirectory();
	}
}
