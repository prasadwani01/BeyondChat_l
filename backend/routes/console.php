<?php

use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Article;

Artisan::command('scrape:beyondchats', function () {
    $client = new Client(['verify' => false]);
    $baseUrl = 'https://beyondchats.com/blogs/';
    
    $this->info("Step 1: Finding the last page...");

    // --- Logic to find the last page ---
    try {
        $response = $client->get($baseUrl);
        $crawler = new Crawler((string) $response->getBody());
        
        $lastPageNode = $crawler->filter('.page-numbers')->last();
        if ($lastPageNode->count() > 0 && str_contains($lastPageNode->text(), 'Next')) {
             $lastPageNode = $crawler->filter('.page-numbers')->eq($crawler->filter('.page-numbers')->count() - 2);
        }
        
        $lastPageUrl = ($lastPageNode->count() > 0) ? $lastPageNode->attr('href') : $baseUrl;
        $this->info("Found start page: " . $lastPageUrl);

    } catch (\Exception $e) {
        $lastPageUrl = $baseUrl;
        $this->error("Using main page as fallback.");
    }

    // --- Fetch Links ---
    $this->info("Step 2: Fetching article links...");
    $response = $client->get($lastPageUrl);
    $pageCrawler = new Crawler((string) $response->getBody());

    $articleLinks = $pageCrawler->filter('a')->each(fn($node) => $node->attr('href'));

    // Filter valid articles
    $targets = [];
    foreach (array_unique($articleLinks) as $link) {
        if (!str_contains($link, '/blogs/')) continue;
        if (str_contains($link, '/tag/') || str_contains($link, '/category/') || str_contains($link, '/page/') || rtrim($link, '/') == 'https://beyondchats.com/blogs') continue;
        $targets[] = $link;
    }

    $targets = array_slice($targets, 0, 5);

    if (empty($targets)) {
        $this->error("No valid links found.");
        return;
    }

    // --- Scrape Each Article ---
    foreach ($targets as $url) {
        if (!str_starts_with($url, 'http')) $url = 'https://beyondchats.com' . $url;
        $this->info("Scraping: " . $url);
        
        try {
            $artResponse = $client->get($url);
            $artCrawler = new Crawler((string) $artResponse->getBody());

            // Extract Title
            $title = null;
            if ($artCrawler->filter('h1')->count() > 0) $title = $artCrawler->filter('h1')->text();
            elseif ($artCrawler->filter('.entry-title')->count() > 0) $title = $artCrawler->filter('.entry-title')->text();
            
            if (!$title) {
                $this->warn("Skipping (No title): $url");
                continue;
            }

            // Extract Content
            $content = null;
            $selectors = ['div.entry-content', 'div.post-content', 'div.gh-content', 'article'];
            foreach ($selectors as $selector) {
                if ($artCrawler->filter($selector)->count() > 0) {
                    $content = $artCrawler->filter($selector)->html();
                    break;
                }
            }

            if (!$content) {
                $this->warn("Skipping (No content): $url");
                continue;
            }

            // Save
            Article::updateOrCreate(
                ['original_url' => $url],
                ['title' => $title, 'original_content' => $content, 'status' => 'pending']
            );

            $this->info("Saved: " . $title);

        } catch (\Exception $e) {
            $this->error("Failed: " . $e->getMessage());
        }
    }

    $this->info("Scraping Completed!");

})->purpose('Scrape blogs directly from routes');