<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VatcanService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('vatcan.api_url'), '/');
        $this->apiKey  = config('vatcan.api_key');
    }

    public function getNotes(int $cid): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])->get("{$this->baseUrl}/user/{$cid}/notes");

        if ($response->successful()) {
            return $response->json('notes') ?? $response->json() ?? [];
        }

        return [];
    }

    public function createNote(int $cid, string $title, string $content, int $authorCid): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])->post("{$this->baseUrl}/user/{$cid}/notes/create", [
            'title'      => $title,
            'content'    => $content,
            'author_cid' => $authorCid,
        ]);

        return $response->successful();
    }
}
