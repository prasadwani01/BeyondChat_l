<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Article;

class ScrapeBlogs extends Command
{
    protected $signature = 'scrape:beyondchats';
    protected $description = 'Scrape 5 oldest articles from BeyondChats';

    public function handle()
    {
        $client = new Client(['verify' => false]); // Skip SSL verification if needed
        $baseUrl = 'https://beyondchats.com/blogs/';
        
        $this->info("Step 1: Finding the last page...");

        // --- Logic to find the last page ---
        try {
            $response = $client->get($baseUrl);
            $crawler = new Crawler((string) $response->getBody());
            
            // Find the last page number from pagination
            $lastPageNode = $crawler->filter('.page-numbers')->last();
            
            // If the last node is "Next", take the one before it
            if ($lastPageNode->count() > 0 && str_contains($lastPageNode->text(), 'Next')) {
                 $lastPageNode = $crawler->filter('.page-numbers')->eq($crawler->filter('.page-numbers')->count() - 2);
            }
            
            if ($lastPageNode->count() > 0) {
                $lastPageUrl = $lastPageNode->attr('href');
                $this->info("Found last page: " . $lastPageUrl);
            } else {
                // Fallback: Just scrape the first page if pagination fails
                $lastPageUrl = $baseUrl; 
                $this->info("Pagination not found. Defaulting to main page.");
            }

        } catch (\Exception $e) {
            $lastPageUrl = $baseUrl;
            $this->error("Error finding last page. Defaulting to main page.");
        }

        // --- Fetch Links from that page ---
        $this->info("Step 2: Fetching article links...");
        $response = $client->get($lastPageUrl);
        $pageCrawler = new Crawler((string) $response->getBody());

        // Get all links that look like blog posts
        $articleLinks = $pageCrawler->filter('a')->each(function ($node) {
            return $node->attr('href');
        });

        // Filter valid articles
        $targets = [];
        foreach (array_unique($articleLinks) as $link) {
            // Must contain /blogs/
            if (!str_contains($link, '/blogs/')) continue;
            
            // Ignore "Tag", "Category", "Page" or the main Index
            if (str_contains($link, '/tag/') || 
                str_contains($link, '/category/') || 
                str_contains($link, '/page/') ||
                rtrim($link, '/') == 'https://beyondchats.com/blogs') {
                continue;
            }

            $targets[] = $link;
        }

        // Take the first 5 unique links
        $targets = array_slice($targets, 0, 5);

        if (empty($targets)) {
            $this->error("No valid article links found!");
            return;
        }

        // --- Scrape Each Article ---
        foreach ($targets as $url) {
            // Ensure absolute URL
            if (!str_starts_with($url, 'http')) {
                $url = 'https://beyondchats.com' . $url;
            }

            $this->info("Scraping: " . $url);
            
            try {
                $artResponse = $client->get($url);
                $artCrawler = new Crawler((string) $artResponse->getBody());

                // 1. Try to find Title (try h1, then entry-title, then post-title)
                $title = null;
                if ($artCrawler->filter('h1')->count() > 0) {
                    $title = $artCrawler->filter('h1')->text();
                } elseif ($artCrawler->filter('.entry-title')->count() > 0) {
                    $title = $artCrawler->filter('.entry-title')->text();
                }
                
                if (!$title) {
                    $this->warn("Skipping (No title found): $url");
                    continue;
                }

                // 2. Try to find Body Content (try multiple common selectors)
                $content = null;
                $selectors = [
                    'div.entry-content',   // Standard WordPress
                    'div.post-content',    // Common theme
                    'div.gh-content',      // Ghost
                    'article',             // Generic HTML5
                    'div.elementor-widget-theme-post-content' // Elementor
                ];

                foreach ($selectors as $selector) {
                    if ($artCrawler->filter($selector)->count() > 0) {
                        $content = $artCrawler->filter($selector)->html();
                        break; // Stop if we found it
                    }
                }

                if (!$content) {
                    $this->warn("Skipping (No content found): $url");
                    continue;
                }

                // 3. Save to DB
                Article::updateOrCreate(
                    ['original_url' => $url],
                    [
                        'title' => $title,
                        'original_content' => $content,
                        'status' => 'pending'
                    ]
                );

                $this->info("Saved: " . $title);

            } catch (\Exception $e) {
                $this->error("Failed: " . $e->getMessage());
            }
        }

        $this->info("Scraping Completed!");
    }
}<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Article;

class ScrapeBlogs extends Command
{
    protected $signature = 'scrape:beyondchats';
    protected $description = 'Scrape 5 oldest articles from BeyondChats';

    public function handle()
    {
        $client = new Client(['verify' => false]); // Skip SSL verification if needed
        $baseUrl = 'https://beyondchats.com/blogs/';
        
        $this->info("Step 1: Finding the last page...");

        // --- Logic to find the last page ---
        try {
            $response = $client->get($baseUrl);
            $crawler = new Crawler((string) $response->getBody());
            
            // Find the last page number from pagination
            $lastPageNode = $crawler->filter('.page-numbers')->last();
            
            // If the last node is "Next", take the one before it
            if ($lastPageNode->count() > 0 && str_contains($lastPageNode->text(), 'Next')) {
                 $lastPageNode = $crawler->filter('.page-numbers')->eq($crawler->filter('.page-numbers')->count() - 2);
            }
            
            if ($lastPageNode->count() > 0) {
                $lastPageUrl = $lastPageNode->attr('href');
                $this->info("Found last page: " . $lastPageUrl);
            } else {
                // Fallback: Just scrape the first page if pagination fails
                $lastPageUrl = $baseUrl; 
                $this->info("Pagination not found. Defaulting to main page.");
            }

        } catch (\Exception $e) {
            $lastPageUrl = $baseUrl;
            $this->error("Error finding last page. Defaulting to main page.");
        }

        // --- Fetch Links from that page ---
        $this->info("Step 2: Fetching article links...");
        $response = $client->get($lastPageUrl);
        $pageCrawler = new Crawler((string) $response->getBody());

        // Get all links that look like blog posts
        $articleLinks = $pageCrawler->filter('a')->each(function ($node) {
            return $node->attr('href');
        });

        // Filter valid articles
        $targets = [];
        foreach (array_unique($articleLinks) as $link) {
            // Must contain /blogs/
            if (!str_contains($link, '/blogs/')) continue;
            
            // Ignore "Tag", "Category", "Page" or the main Index
            if (str_contains($link, '/tag/') || 
                str_contains($link, '/category/') || 
                str_contains($link, '/page/') ||
                rtrim($link, '/') == 'https://beyondchats.com/blogs') {
                continue;
            }

            $targets[] = $link;
        }

        // Take the first 5 unique links
        $targets = array_slice($targets, 0, 5);

        if (empty($targets)) {
            $this->error("No valid article links found!");
            return;
        }

        // --- Scrape Each Article ---
        foreach ($targets as $url) {
            // Ensure absolute URL
            if (!str_starts_with($url, 'http')) {
                $url = 'https://beyondchats.com' . $url;
            }

            $this->info("Scraping: " . $url);
            
            try {
                $artResponse = $client->get($url);
                $artCrawler = new Crawler((string) $artResponse->getBody());

                // 1. Try to find Title (try h1, then entry-title, then post-title)
                $title = null;
                if ($artCrawler->filter('h1')->count() > 0) {
                    $title = $artCrawler->filter('h1')->text();
                } elseif ($artCrawler->filter('.entry-title')->count() > 0) {
                    $title = $artCrawler->filter('.entry-title')->text();
                }
                
                if (!$title) {
                    $this->warn("Skipping (No title found): $url");
                    continue;
                }

                // 2. Try to find Body Content (try multiple common selectors)
                $content = null;
                $selectors = [
                    'div.entry-content',   // Standard WordPress
                    'div.post-content',    // Common theme
                    'div.gh-content',      // Ghost
                    'article',             // Generic HTML5
                    'div.elementor-widget-theme-post-content' // Elementor
                ];

                foreach ($selectors as $selector) {
                    if ($artCrawler->filter($selector)->count() > 0) {
                        $content = $artCrawler->filter($selector)->html();
                        break; // Stop if we found it
                    }
                }

                if (!$content) {
                    $this->warn("Skipping (No content found): $url");
                    continue;
                }

                // 3. Save to DB
                Article::updateOrCreate(
                    ['original_url' => $url],
                    [
                        'title' => $title,
                        'original_content' => $content,
                        'status' => 'pending'
                    ]
                );

                $this->info("Saved: " . $title);

            } catch (\Exception $e) {
                $this->error("Failed: " . $e->getMessage());
            }
        }

        $this->info("Scraping Completed!");
    }
}