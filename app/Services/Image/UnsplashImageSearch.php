<?php
namespace App\Services\Image;

use App\Contracts\ImageSearchInterface;
use Unsplash\HttpClient;
use Unsplash\Search;

class UnsplashImageSearch implements ImageSearchInterface
{

    public function search(string $query): ?array
    {
        HttpClient::init([
            'applicationId' => config('image.unsplash.access_key'),
            'secret'        => config('image.unsplash.secret_key'),
            'utmSource'     => 'Event-Planner',
        ]);

        $searchResults = Search::photos($query, 1, 1);
        $photoResults  = $searchResults ? $searchResults->getResults() : [];

        if (empty($photoResults)) {
            return null;
        }

        $imageUrls = $photoResults[0]['urls'];

        return [
            'image' => [
                'url'       => $imageUrls['regular'] ?? null,
                'thumbnail' => $imageUrls['small'] ?? null,
                'alt'       => $query,
            ],
        ];
    }

}
