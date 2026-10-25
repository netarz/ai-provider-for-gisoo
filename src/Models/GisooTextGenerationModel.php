<?php
/**
 * Gisoo text generation model.
 *
 * @package AiProviderForGisoo
 */

declare(strict_types=1);

namespace Gisoo\AiProviderForGisoo\Models;

use Gisoo\AiProviderForGisoo\Provider\GisooProvider;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;

/**
 * A Gisoo chat model, reached through the OpenAI-compatible chat completions API.
 *
 * @since 1.0.0
 */
class GisooTextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel {

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
}
