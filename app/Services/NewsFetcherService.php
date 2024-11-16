<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use App\Services\Contracts\NewsSource;

class NewsFetcherService {
    protected $sources = [];

    public function __construct() {
        $this->sources = [
            [
                'url'     => config('services.news_api.url'),
                'params'  => ['apiKey' => config('services.news_api.key'), 'q' => 'latest'],
                'adapter' => \App\Services\NewsApiService::class,
            ],
            [
                'url'     => config('services.ny_times.url'),
                'params'  => ['api-key' => config('services.ny_times.key')],
                'adapter' => \App\Services\NyTimesApiService::class,
            ],
            [
                'url'     => config('services.guardian.url'),
                'params'  => ['api-key' => config('services.guardian.key'), 'show-fields' => 'headline,thumbnail,body,author,byline'],
                'adapter' => \App\Services\GuardianApiService::class,
             ]

        ];
    }

    public function fetchAndSaveNews() {
        try {
            foreach ($this->sources as $source) {
                $response = Http::get($source['url'], $source['params']);

                if ($response->successful()) {
                    $rawData = $response->json();
                    if ($source['url'] === config('services.news_api.url')) {
                        $rawData['articles'] = collect($rawData['articles'])
                            ->filter(function ($item) {
                                return !collect($item)->contains('[Removed]');
                            })
                            ->toArray();
                    }
                    if ($source['url'] === config('services.ny_times.url')) {
                        $rawData['articles'] = $rawData['results'];
                    }

                    if($source['url'] === config('services.guardian.url')) {
                        $rawData['articles'] = $rawData['response']['results'];
                    }

                    $this->processFetchedData($rawData['articles'], new $source['adapter']);
                } else {
                    throw new \Exception('Failed to fetch news: ' . $response->body());
                }

            }

        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch news: ' . $e->getMessage());
        }

    }

    protected function processFetchedData(array $rawData, NewsSource $source) {

        foreach ($rawData as $item) {
            $normalizedData = $source->normalize($item);

            Article::updateOrCreate(
                ['title' => $normalizedData['title'], 'source' => $normalizedData['source']],
                $normalizedData
            );
        }
    }

}
