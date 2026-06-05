<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VatsimBookingService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.vatsim_bookings.url'), '/');
        $this->apiKey  = config('services.vatsim_bookings.key');
    }

    public function getBookings(array $params = []): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/booking", $params);
            if ($response->successful()) {
                return ['status' => 'ok', 'data' => $response->json()];
            }
            return ['status' => 'error', 'data' => []];
        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => []];
        }
    }

    public function createBooking(array $data): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/booking", $data);
            if ($response->successful()) {
                return ['status' => 'ok', 'data' => $response->json()];
            }
            return ['status' => 'error', 'errors' => $response->json()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'errors' => ['message' => 'Could not reach booking service.']];
        }
    }

    public function updateBooking(int $id, array $data): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->put("{$this->baseUrl}/booking/{$id}", $data);
            if ($response->successful()) {
                return ['status' => 'ok', 'data' => $response->json()];
            }
            return ['status' => 'error', 'errors' => $response->json()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'errors' => ['message' => 'Could not reach booking service.']];
        }
    }

    public function deleteBooking(int $id): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->delete("{$this->baseUrl}/booking/{$id}");
            \Log::info('VATSIM delete booking', ['id' => $id, 'status' => $response->status(), 'body' => $response->body()]);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
