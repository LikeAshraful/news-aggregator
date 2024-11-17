<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Contracts\NewsSource;

class NewsFetcherService {
    protected $sources;
    public function __construct()
    {
        $this->sources = config('services.news_sources');
    }

    public function fetchAndSaveNews()
    {
        foreach ($this->sources as $sourceConfig) {
            $this->fetchFromSource($sourceConfig);
        }
    }

    protected function fetchFromSource(array $sourceConfig)
    {
        try {
            $response = Http::get($sourceConfig['url'], $sourceConfig['params']);
            if ($response->successful()) {
                $adapter = app($sourceConfig['adapter']);
                $rawData = $adapter->processResponse($response->json());
                $this->processFetchedData($rawData, $adapter);
            } else {
                Log::error('Failed to fetch news from ' . $sourceConfig['url'], [
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching news from ' . $sourceConfig['url'], [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function processFetchedData(array $rawData, NewsSource $adapter)
    {
        foreach ($rawData as $item) {
            $normalizedData = $adapter->normalize($item);
            Article::updateOrCreate(
                ['title' => $normalizedData['title'], 'source' => $normalizedData['source']],
                $normalizedData
            );
        }
    }

}
