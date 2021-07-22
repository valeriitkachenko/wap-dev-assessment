<?php


namespace App\Services\Marketplace;

use App\Services\Marketplace\Exceptions\InvalidConfigException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class MarketplaceClient
{
    const BASE_URI = 'https://apptest.wearepentagon.com/devInterview/API/en/';

    /**
     * @var array $config
     */
    private $config;

    /**
     * @var Client $client
     */
    private $client;

    /**
     * MarketplaceClient constructor.
     *
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
        $this->setClient();
    }

    /**
     * @param array $config
     * @throws InvalidConfigException
     */
    private function setConfig(array $config): void
    {
        if (empty($config['client_id']) || empty($config['client_secret'])) {
            throw new InvalidConfigException();
        }

        $this->config = $config;
    }

    private function setClient(): void
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUri()
        ]);
    }

    /**
     * If a specific base uri is not defined in the config the default one will be used
     *
     * @return string
     */
    private function getBaseUri(): string
    {
        return Arr::get($this->config, 'base_uri', self::BASE_URI);
    }

    /**
     * POST method
     *
     * @param string $uri
     * @param array $payload
     * @param bool $withoutAuth
     * @param string $type
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function post(
        string $uri,
        array $payload = [],
        bool $withoutAuth = false,
        string $type = 'form_params'
    ): ResponseInterface {
        return $this->send('POST', $uri, [$type => $payload], $withoutAuth);
    }

    /**
     * GET method
     *
     * @param string $uri
     * @param array $payload
     * @param bool $withoutAuth
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function get(string $uri, array $payload = [], bool $withoutAuth = false): ResponseInterface
    {
        return $this->send('GET', $uri, ['query' => $payload], $withoutAuth);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $payload
     * @param bool $withoutAuth
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(string $method, string $uri, array $payload = [], bool $withoutAuth = false): ResponseInterface
    {
        if ($withoutAuth) {
            return $this->client->request($method, $uri, $payload);
        }

        $payload = $this->addAuthorizationHeaderToRequestPayload($payload);

        try {
            return $this->client->request($method, $uri, $payload);
        } catch (BadResponseException $exception) {
            // Token is invalid for some reason
            if ($exception->getCode() == Response::HTTP_UNAUTHORIZED) {
                // Get new access token, update the authorization header and try again
                $this->getNewAccessToken();
                $payload = $this->addAuthorizationHeaderToRequestPayload($payload);

                return $this->client->request($method, $uri, $payload);
            }

            throw $exception;
        }
    }

    /**
     * @param array $payload
     * @return array
     */
    private function addAuthorizationHeaderToRequestPayload(array $payload): array
    {
        return array_merge($payload, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessTokenFromCache(),
            ]
        ]);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    private function getAccessTokenFromCacheOrCreateNew(): string
    {
        $accessToken = $this->getAccessTokenFromCache();

        if (empty($accessToken)) {
            $accessToken = $this->getNewAccessToken();
        }

        return $accessToken;
    }

    /**
     * @return string|null
     */
    private function getAccessTokenFromCache(): ?string
    {
        return Cache::get('marketplace.access-token');
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    private function getNewAccessToken(): string
    {
        $tokenResponse = $this->getAccessToken();

        Cache::put('marketplace.access-token', $tokenResponse['access_token'], $tokenResponse['expires_in']);

        return $tokenResponse['access_token'];
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    private function getAccessToken(): array
    {
        $response = $this->post('access-token', [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret']
        ], true);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function getProductOrOrder(): string
    {
        return $this->get('get-random-test-feed')->getBody()->getContents();
    }
}
