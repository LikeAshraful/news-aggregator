<?php

namespace App\Services;

use App\Services\Contracts\NewsSource;

class NewsApiService implements NewsSource
{
    public function normalize(array $rawData) : array
    {
        return [
            'title' => $rawData['title'],
            'content' => $rawData['content'],
            'source' => $rawData['source']['name'],
            'category' => $rawData['category'] ?? '',
            'author' => $rawData['author'] ?? '',
            'img_url' => $rawData['urlToImage'] ?? '',
            'publish_at' => $rawData['publishedAt'] ?? '',
        ];
    }
}
