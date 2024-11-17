<?php

namespace App\Services;

use App\Services\Contracts\NewsSource;

class NyTimesApiService implements NewsSource
{
    public function normalize(array $rawData) : array
    {
        return [
            'title' => $rawData['title'],
            'content' => $rawData['abstract'],
            'source' => $rawData['source'],
            'category' => $rawData['section'] ?? '',
            'author' => $rawData['author'] ?? '',
            'img_url' => $rawData['multimedia'][0]['url'] ?? '',
            'publish_at' => $rawData['published_date'] ?? '',
        ];
    }

    public function processResponse(array $response): array
    {
        return $response['results'] ?? [];
    }
}
