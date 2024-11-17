<?php

namespace App\Services;

use App\Services\Contracts\NewsSource;

class GuardianApiService implements NewsSource
{
    public function normalize(array $rawData) : array
    {
        return [
            'title' => $rawData['webTitle'],
            'source' => 'The Guardian',
            'content' => $rawData['fields']['body'] ?? '',
            'category' => $rawData['pillarName'] ?? '',
            'author' => $rawData['fields']['byline'] ?? '',
            'img_url' => $rawData['fields']['thumbnail'] ?? '',
            'publish_at' => $rawData['webPublicationDate'] ?? '',
        ];
    }

    public function processResponse(array $response): array
    {
        return $response['response']['results'] ?? [];
    }
}
