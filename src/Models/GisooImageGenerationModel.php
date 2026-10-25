<?php
/**
 * Gisoo image generation model.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo\Models;

use Gisoo\AiProviderForGisoo\Provider\GisooProvider;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleImageGenerationModel;

/**
 * A Gisoo image model, reached through the OpenAI-compatible images API.
 *
 * @since 1.0.0
 */
class GisooImageGenerationModel extends AbstractOpenAiCompatibleImageGenerationModel {

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
		return new Request(
			$method,
			GisooProvider::url( $path ),
			$headers,
			$data,
			$this->getRequestOptions()
		);
	}

	/**
	 * Gisoo always answers with base64 data, so the response_format field is dropped.
	 *
	 * @since 1.0.0
	 *
	 * @param list<Message> $prompt The prompt messages.
	 * @return array<string, mixed> Request parameters.
	 */
	protected function prepareGenerateImageParams( array $prompt ): array {
		$params = parent::prepareGenerateImageParams( $prompt );
		unset( $params['response_format'] );

		return $params;
	}
}
