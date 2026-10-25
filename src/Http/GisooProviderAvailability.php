<?php
/**
 * Availability check for the Gisoo provider.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo\Http;

use Exception;
use Gisoo\AiProviderForGisoo\Metadata\GisooModelMetadataDirectory;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;

/**
 * Reports the provider as configured only when the stored key is accepted.
 *
 * GET /models on Gisoo is public, so listing models would report "configured"
 * for any key. GET /me is free and answers 401 for a missing or wrong key.
 *
 * @since 1.0.0
 */
class GisooProviderAvailability implements ProviderAvailabilityInterface {

	/**
	 * The directory that knows how to talk to the API.
	 *
	 * @var ModelMetadataDirectoryInterface
	 */
	private ModelMetadataDirectoryInterface $directory;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ModelMetadataDirectoryInterface $directory Metadata directory.
	 */
	public function __construct( ModelMetadataDirectoryInterface $directory ) {
		$this->directory = $directory;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	public function isConfigured(): bool {
		if ( ! $this->directory instanceof GisooModelMetadataDirectory ) {
			return false;
		}

		try {
			return $this->directory->isKeyValid();
		} catch ( Exception $e ) {
			return false;
		}
	}
}
