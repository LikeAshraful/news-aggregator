<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\NewsFetcherService;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save the news from multiple sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsFetcherService $newsFetcherService)
    {
        try {
            $this->info('Fetching news...');
            $newsFetcherService->fetchAndSaveNews();
            $this->info('News fetched and saved successfully.');
            Log::info('News fetched successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to fetch news: ' . $e->getMessage());
            Log::error('Failed to fetch news: ' . $e->getMessage());
        }

        return 0;
    }
}
