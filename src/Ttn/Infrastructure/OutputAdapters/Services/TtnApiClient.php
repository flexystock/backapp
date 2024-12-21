<?php

// src/Ttn/Infrastructure/OutputAdapters/Services/TtnApiClient.php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TtnApiClient
{
    private HttpClientInterface $client;
    private string $apiBase;
    private string $apiKey;
    private string $tenantId;

    public function __construct(HttpClientInterface $client, string $apiBase, string $apiKey, string $tenantId)
    {
        $this->client = $client;
        $this->apiBase = $apiBase;
        $this->apiKey = $apiKey;
        $this->tenantId = $tenantId;
    }

    public function post(string $uri, array $data): array
    {
        $response = $this->client->request('POST', $this->apiBase.$uri, [
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
