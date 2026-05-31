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
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Accept'        => 'application/json',
            ])->get("{$this->baseUrl}/user/{$cid}/notes");

            $body = $response->json();

            if ($response->successful() && isset($body['notes'])) {
                $notes = collect($body['notes'])->sortByDesc('created_at')->values()->all();
                return ['status' => 'ok', 'notes' => $notes];
            }

            return ['status' => 'error', 'message' => $body['error'] ?? 'Unknown error', 'notes' => []];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Could not reach VATCAN API.', 'notes' => []];
        }
    }

    public function getUser(int $cid): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Accept'        => 'application/json',
            ])->get("{$this->baseUrl}/user/{$cid}/");

            $body = $response->json();

            if ($response->successful() && $body) {
                return ['status' => 'ok', 'data' => $body];
            }

            return ['status' => 'error', 'message' => $body['error'] ?? 'Unknown error', 'data' => null];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Could not reach VATCAN API.', 'data' => null];
        }
    }

    public function assignInstructor(int $studentCid, int $instructorCid, int $assignedBy): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Accept'        => 'application/json',
            ])->post("{$this->baseUrl}/user/{$studentCid}/instructor/assign?" . http_build_query([
                'instructor_cid' => $instructorCid,
                'assigned_by'    => $assignedBy,
            ]));

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unassignInstructor(int $studentCid): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Accept'        => 'application/json',
            ])->post("{$this->baseUrl}/user/{$studentCid}/instructor/unassign");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
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
