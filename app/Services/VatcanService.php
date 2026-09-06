<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VatcanService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected string $publicRosterUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('vatcan.api_url'), '/');
        $this->apiKey  = config('vatcan.api_key');
        $this->publicRosterUrl = (string) config('vatcan.public_roster_url');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey) && filled($this->baseUrl);
    }

    public function canSyncRoster(): bool
    {
        return $this->isConfigured() || filled($this->publicRosterUrl);
    }

    public function preferredRosterSourceLabel(): string
    {
        return $this->isConfigured() ? 'Authenticated VATCAN API' : 'Public VATCAN CZWG roster';
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

    public function getFirMembershipType(int $cid): array
    {
        $result = $this->getRoster();
        if ($result['status'] === 'error') {
            return ['status' => 'error', 'message' => $result['message']];
        }
        $controllers = collect($result['data']['controllers'] ?? []);
        $visitors    = collect($result['data']['visitors'] ?? []);

        if ($controllers->contains(fn($m) => (int) ($m['cid'] ?? 0) === $cid)) {
            return ['status' => 'ok', 'type' => 'home'];
        }
        if ($visitors->contains(fn($m) => (int) ($m['cid'] ?? 0) === $cid)) {
            return ['status' => 'ok', 'type' => 'visitor'];
        }
        return ['status' => 'ok', 'type' => 'none'];
    }

    public function getRoster(): array
    {
        $apiError = null;
        if ($this->isConfigured()) {
            $apiResult = $this->getRosterFromApi();
            if (($apiResult['status'] ?? 'error') === 'ok') {
                return $apiResult;
            }
            $apiError = $apiResult['message'] ?? 'VATCAN API request failed.';
        }

        $publicResult = $this->getRosterFromPublicPage();
        if (($publicResult['status'] ?? 'error') === 'ok') {
            return $publicResult;
        }

        $message = $publicResult['message'] ?? 'VATCAN public roster request failed.';
        if ($apiError) {
            $message = $apiError.' Public-roster fallback also failed: '.$message;
        }

        return ['status' => 'error', 'message' => $message, 'data' => null];
    }

    protected function getRosterFromApi(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->apiKey,
                'Accept'        => 'application/json',
            ])->get("{$this->baseUrl}/facility/roster");

            $body = $response->json();

            if ($response->successful() && isset($body['data'])) {
                return ['status' => 'ok', 'data' => $body['data'], 'source' => 'api'];
            }

            return ['status' => 'error', 'message' => $body['error'] ?? 'Unknown error', 'data' => null];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Could not reach VATCAN API.', 'data' => null];
        }
    }

    protected function getRosterFromPublicPage(): array
    {
        if (! filled($this->publicRosterUrl)) {
            return ['status' => 'error', 'message' => 'The public VATCAN roster URL is not configured.', 'data' => null];
        }

        if (! class_exists(\DOMDocument::class)) {
            return ['status' => 'error', 'message' => 'The PHP DOM extension is required to read the public VATCAN roster.', 'data' => null];
        }

        try {
            $response = Http::accept('text/html')
                ->withUserAgent((string) config('vatcan.public_roster_user_agent'))
                ->timeout(15)
                ->retry(2, 500)
                ->get($this->publicRosterUrl);

            if (! $response->successful()) {
                return ['status' => 'error', 'message' => 'VATCAN public roster returned HTTP '.$response->status().'.', 'data' => null];
            }

            return $this->parsePublicRoster($response->body());
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => 'Could not reach the public VATCAN roster.', 'data' => null];
        }
    }

    public function parsePublicRoster(string $html): array
    {
        if (trim($html) === '') {
            return ['status' => 'error', 'message' => 'VATCAN public roster returned an empty page.', 'data' => null];
        }

        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return ['status' => 'error', 'message' => 'VATCAN public roster HTML could not be parsed.', 'data' => null];
        }

        $xpath = new \DOMXPath($document);
        $homeTable = $this->tableAfterHeading($xpath, 'Home Controllers');
        if (! $homeTable) {
            return ['status' => 'error', 'message' => 'VATCAN roster format changed: Home Controllers table was not found.', 'data' => null];
        }

        $controllers = $this->parseRosterTable($xpath, $homeTable, true);
        if (($controllers['status'] ?? 'error') !== 'ok') {
            return $controllers;
        }

        $minimum = max(1, (int) config('vatcan.public_roster_min_members', 5));
        if (count($controllers['members']) < $minimum) {
            return [
                'status' => 'error',
                'message' => sprintf('VATCAN roster safety check failed: found %d home controllers; expected at least %d.', count($controllers['members']), $minimum),
                'data' => null,
            ];
        }

        $visitors = [];
        $visitorTable = $this->tableAfterHeading($xpath, 'Visiting Controllers');
        if ($visitorTable) {
            $visitorResult = $this->parseRosterTable($xpath, $visitorTable, false);
            if (($visitorResult['status'] ?? 'error') === 'ok') {
                $visitors = $visitorResult['members'];
            }
        }

        return [
            'status' => 'ok',
            'source' => 'public_roster',
            'data' => ['controllers' => $controllers['members'], 'visitors' => $visitors],
        ];
    }

    protected function tableAfterHeading(\DOMXPath $xpath, string $heading): ?\DOMElement
    {
        $nodes = $xpath->query("//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][contains(normalize-space(.), '{$heading}')]/following::table[1]");
        $table = $nodes?->item(0);
        return $table instanceof \DOMElement ? $table : null;
    }

    protected function parseRosterTable(\DOMXPath $xpath, \DOMElement $table, bool $strictRatings): array
    {
        // VATCAN currently places TH elements directly inside THEAD. Also accept a
        // conventional THEAD/TR structure so a harmless markup cleanup will not break sync.
        $headerNodes = $xpath->query('.//thead//th | .//thead/th', $table);
        if (! $headerNodes || $headerNodes->length === 0) {
            $headerNodes = $xpath->query('.//tr[1]/*[self::th or self::td]', $table);
        }
        $headers = [];
        foreach ($headerNodes ?: [] as $index => $header) {
            $headers[strtolower(trim($header->textContent))] = $index;
        }

        foreach (['cid', 'name', 'rating'] as $required) {
            if (! array_key_exists($required, $headers)) {
                return ['status' => 'error', 'message' => 'VATCAN roster format changed: expected CID, Name, and Rating columns.', 'data' => null];
            }
        }

        $members = [];
        $rows = $xpath->query('.//tr[td]', $table);
        foreach ($rows ?: [] as $row) {
            $cells = $xpath->query('./td', $row);
            $cidText = trim($cells->item($headers['cid'])?->textContent ?? '');
            if (! preg_match('/^\d{5,10}$/', $cidText)) {
                return ['status' => 'error', 'message' => 'VATCAN roster safety check failed: a home-controller CID was invalid.', 'data' => null];
            }

            $ratingText = trim($cells->item($headers['rating'])?->textContent ?? '');
            $rating = $this->ratingIdFromLabel($ratingText);
            if ($strictRatings && $rating === null) {
                return ['status' => 'error', 'message' => 'VATCAN roster safety check failed: unknown rating "'.$ratingText.'".', 'data' => null];
            }

            $name = preg_replace('/\s+/', ' ', trim($cells->item($headers['name'])?->textContent ?? ''));
            if ($name === $cidText) {
                $name = '';
            }
            [$firstName, $lastName] = $this->splitName($name);
            $members[(int) $cidText] = [
                'cid' => (int) $cidText,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'rating' => $rating,
            ];
        }

        return ['status' => 'ok', 'members' => array_values($members)];
    }

    protected function ratingIdFromLabel(string $label): ?int
    {
        $normalized = strtoupper(preg_replace('/[^A-Z0-9]+/i', '', trim($label)));
        return [
            'OBS' => 1, 'OBSERVER' => 1, 'PILOTOBSERVER' => 1,
            'S1' => 2, 'STUDENT1' => 2,
            'S2' => 3, 'STUDENT2' => 3,
            'S3' => 4, 'STUDENT3' => 4,
            'C1' => 5, 'CONTROLLER1' => 5,
            'C2' => 6, 'CONTROLLER2' => 6,
            'C3' => 7, 'CONTROLLER3' => 7,
            'I1' => 8, 'INSTRUCTOR1' => 8,
            'I2' => 9, 'INSTRUCTOR2' => 9,
            'I3' => 10, 'INSTRUCTOR3' => 10,
            'SUP' => 11, 'SUPERVISOR' => 11,
            'ADM' => 12, 'ADMINISTRATOR' => 12,
        ][$normalized] ?? null;
    }

    protected function splitName(string $name): array
    {
        if ($name === '') {
            return [null, null];
        }
        $parts = preg_split('/\s+/', $name, 2);
        return [$parts[0] ?: null, $parts[1] ?? null];
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
