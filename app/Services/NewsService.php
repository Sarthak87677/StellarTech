<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * Space news feed.
 *
 * Fetched SERVER-SIDE and cached, deliberately:
 *   - visitors never wait on a third-party server
 *   - the site keeps working if the API has an outage (stale cache is served)
 *   - visitors' browsers are not sent to an external domain, which keeps the
 *     privacy policy simple and honest
 *
 * The Spaceflight News API requires no key. If you ever switch providers,
 * the key goes in .env and is read here — never in front-end JavaScript.
 */
class NewsService
{
    protected const ENDPOINT = 'https://api.spaceflightnewsapi.net/v4/articles/';

    public function latest(int $limit = 6): Collection
    {
        $minutes = (int) config('services.news.cache_minutes', 15);

        return Cache::remember("news.latest.{$limit}", now()->addMinutes($minutes), function () use ($limit) {
            return $this->fetch($limit);
        }) ?? collect();
    }

    protected function fetch(int $limit): Collection
    {
        try {
            $response = Http::timeout(6)
                ->retry(2, 250)
                ->get(self::ENDPOINT, [
                    'limit'    => $limit,
                    'ordering' => '-published_at',
                ]);

            if (! $response->successful()) {
                Log::warning('News feed returned ' . $response->status());
                return $this->stale($limit);
            }

            $articles = collect($response->json('results') ?? [])
                ->map(fn ($a) => [
                    'title'        => (string) ($a['title'] ?? ''),
                    'summary'      => (string) ($a['summary'] ?? ''),
                    'url'          => (string) ($a['url'] ?? ''),
                    'image'        => (string) ($a['image_url'] ?? ''),
                    'source'       => (string) ($a['news_site'] ?? 'Unknown'),
                    'published_at' => (string) ($a['published_at'] ?? now()->toIso8601String()),
                ])
                ->filter(fn ($a) => $a['title'] !== '' && $a['url'] !== '')
                ->values();

            // Keep a long-lived copy so an outage degrades to stale, not empty
            Cache::put("news.fallback.{$limit}", $articles, now()->addDays(7));

            return $articles;
        } catch (\Throwable $e) {
            Log::warning('News feed failed: ' . $e->getMessage());
            return $this->stale($limit);
        }
    }

    protected function stale(int $limit): Collection
    {
        return Cache::get("news.fallback.{$limit}", collect());
    }
}
