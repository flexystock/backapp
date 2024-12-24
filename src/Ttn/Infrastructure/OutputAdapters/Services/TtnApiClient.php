<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class TtnApiClient
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private string $apiBase;
    private string $apiKey;
    private string $tenantId;
    private string $apiUserKey;

    public function __construct(HttpClientInterface $client, string $apiBase, string $apiKey, string $tenantId, string $apiUserKey, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->apiBase = $apiBase;
        $this->apiKey = $apiKey;
        $this->tenantId = $tenantId;
        $this->apiUserKey = $apiUserKey;
        $this->logger = $logger;
    }

    public function post(string $uri, array $data, $apiUserKey = null): array
    {
        if ($apiUserKey) {
            $token = $this->apiUserKey;
        } else {
            $token = $this->apiKey;
        }
        $this->logger->info('Payload para TTN API:', ['data' => $data]);
        $this->logger->info('Token para TTN API:', ['token' => $token]);
        $this->logger->info('URI para TTN API:', ['uri' => $uri]);
        $response = $this->client->request('POST', $this->apiBase.$uri, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false); // Devuelve el contenido sin lanzar excepción

        if ($statusCode < 200 || $statusCode >= 300) {
            // Aquí puedes decodificar el contenido para ver el error exacto de TTN
            $errorData = json_decode($content, true);
            // Lanza una excepción personalizada con el mensaje de error
            throw new \RuntimeException('TTN API error: '.($errorData['message'] ?? 'Unknown error'));
        }

        return $response->toArray(false);
    }

    public function put(string $uri, array $data): array
    {
        $response = $this->client->request('PUT', $this->apiBase.$uri, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false); // Devuelve el contenido sin lanzar excepción

        if ($statusCode < 200 || $statusCode >= 300) {
            // Aquí puedes decodificar el contenido para ver el error exacto de TTN
            $errorData = json_decode($content, true);
            // Lanza una excepción personalizada con el mensaje de error
            throw new \RuntimeException('TTN API error: '.($errorData['message'] ?? 'Unknown error'));
        }

        return $response->toArray(false);
    }
}
