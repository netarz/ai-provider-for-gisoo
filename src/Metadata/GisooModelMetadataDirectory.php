<?php
/**
 * Model metadata directory for Gisoo.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo\Metadata;

use Gisoo\AiProviderForGisoo\Provider\GisooProvider;
use WordPress\AiClient\Files\Enums\FileTypeEnum;
use WordPress\AiClient\Files\Enums\MediaOrientationEnum;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\DTO\Response;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Http\Exception\ResponseException;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\SupportedOption;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleModelMetadataDirectory;

/**
 * Discovers Gisoo models from the public GET /models endpoint.
 *
 * Chat models become text generation models, image models become image
 * generation models. Capability flags reported by Gisoo (tools, json, vision)
 * decide which options each model advertises. The AI Client caches the result.
 *
 * @since 1.0.0
 */
class GisooModelMetadataDirectory extends AbstractOpenAiCompatibleModelMetadataDirectory {

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param HttpMethodEnum $method  HTTP method.
	 * @param string         $path    API path.
	 * @param array          $headers Headers.
	 * @param mixed          $data    Request body data.
	 */
	protected function createRequest( HttpMethodEnum $method, string $path, array $headers = array(), $data = null ): Request {
		return new Request( $method, GisooProvider::url( $path ), $headers, $data );
	}

	/**
	 * The model list is public, so it is requested without credentials.
	 * A missing or wrong key therefore never hides the catalogue.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, ModelMetadata> Model metadata keyed by model ID.
	 */
	protected function sendListModelsRequest(): array {
		$request  = $this->createRequest( HttpMethodEnum::GET(), 'models' );
		$response = $this->getHttpTransporter()->send( $request );
		$this->throwIfNotSuccessful( $response );

		$map = array();
		foreach ( $this->parseResponseToModelMetadataList( $response ) as $metadata ) {
			$map[ $metadata->getId() ] = $metadata;
		}

		return $map;
	}

	/**
	 * Asks GET /me whether the configured key is accepted. The call is free.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when Gisoo accepts the key.
	 */
	public function isKeyValid(): bool {
		$request  = $this->getRequestAuthentication()->authenticateRequest(
			$this->createRequest( HttpMethodEnum::GET(), 'me' )
		);
		$response = $this->getHttpTransporter()->send( $request );

		return $response->isSuccessful();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param Response $response HTTP response of GET /models.
	 * @return list<ModelMetadata> Model metadata.
	 * @throws ResponseException If the response has no model list.
	 */
	protected function parseResponseToModelMetadataList( Response $response ): array {
		$data = $response->getData();

		if ( ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
			throw ResponseException::fromMissingData( 'Gisoo', 'data' );
		}

		$models = array();
		foreach ( $data['data'] as $model ) {
			if ( ! is_array( $model ) || empty( $model['id'] ) || ! is_string( $model['id'] ) ) {
				continue;
			}

			$type = isset( $model['type'] ) && is_string( $model['type'] ) ? $model['type'] : 'chat';
			$name = isset( $model['name'] ) && is_string( $model['name'] ) && '' !== $model['name'] ? $model['name'] : $model['id'];

			if ( 'chat' === $type ) {
				$models[] = new ModelMetadata(
					$model['id'],
					$name,
					array( CapabilityEnum::textGeneration(), CapabilityEnum::chatHistory() ),
					$this->textOptions( $model )
				);
			} elseif ( 'image' === $type ) {
				$models[] = new ModelMetadata(
					$model['id'],
					$name,
					array( CapabilityEnum::imageGeneration() ),
					$this->imageOptions( $model )
				);
			}
		}

		usort(
			$models,
			static function ( ModelMetadata $a, ModelMetadata $b ): int {
				return strcmp( $a->getId(), $b->getId() );
			}
		);

		return $models;
	}

	/**
	 * Builds the supported options of a chat model from its Gisoo capability flags.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $model Model entry of GET /models.
	 * @return list<SupportedOption> Supported options.
	 */
	private function textOptions( array $model ): array {
		$flags = isset( $model['capabilities'] ) && is_array( $model['capabilities'] ) ? $model['capabilities'] : array();

		$options = array(
			new SupportedOption( OptionEnum::systemInstruction() ),
			new SupportedOption( OptionEnum::candidateCount() ),
			new SupportedOption( OptionEnum::maxTokens() ),
			new SupportedOption( OptionEnum::temperature() ),
			new SupportedOption( OptionEnum::topP() ),
			new SupportedOption( OptionEnum::stopSequences() ),
			new SupportedOption( OptionEnum::presencePenalty() ),
			new SupportedOption( OptionEnum::frequencyPenalty() ),
			new SupportedOption( OptionEnum::customOptions() ),
			new SupportedOption( OptionEnum::outputModalities(), array( array( ModalityEnum::text() ) ) ),
		);

		if ( ! empty( $flags['tools'] ) ) {
			$options[] = new SupportedOption( OptionEnum::functionDeclarations() );
		}

		if ( ! empty( $flags['json'] ) ) {
			$options[] = new SupportedOption( OptionEnum::outputMimeType(), array( 'text/plain', 'application/json' ) );
			$options[] = new SupportedOption( OptionEnum::outputSchema() );
		} else {
			$options[] = new SupportedOption( OptionEnum::outputMimeType(), array( 'text/plain' ) );
		}

		$input = array( array( ModalityEnum::text() ) );
		if ( ! empty( $flags['vision'] ) ) {
			$input[] = array( ModalityEnum::text(), ModalityEnum::image() );
		}
		$options[] = new SupportedOption( OptionEnum::inputModalities(), $input );

		return $options;
	}

	/**
	 * Builds the supported options of an image model.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, mixed> $model Model entry of GET /models.
	 * @return list<SupportedOption> Supported options.
	 */
	private function imageOptions( array $model ): array {
		unset( $model );

		return array(
			new SupportedOption( OptionEnum::inputModalities(), array( array( ModalityEnum::text() ) ) ),
			new SupportedOption( OptionEnum::outputModalities(), array( array( ModalityEnum::image() ) ) ),
			new SupportedOption( OptionEnum::candidateCount() ),
			new SupportedOption( OptionEnum::outputMimeType(), array( 'image/png', 'image/jpeg', 'image/webp' ) ),
			new SupportedOption( OptionEnum::outputFileType(), array( FileTypeEnum::inline() ) ),
			new SupportedOption(
				OptionEnum::outputMediaOrientation(),
				array( MediaOrientationEnum::square(), MediaOrientationEnum::landscape(), MediaOrientationEnum::portrait() )
			),
			new SupportedOption( OptionEnum::outputMediaAspectRatio(), array( '1:1', '3:2', '2:3', '7:4', '4:7' ) ),
			new SupportedOption( OptionEnum::customOptions() ),
		);
	}
}
