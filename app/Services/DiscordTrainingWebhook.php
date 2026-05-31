<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordTrainingWebhook
{
    protected ?string $url;

    public function __construct()
    {
        $this->url = config('services.discord.training_webhook');
    }

    public function waitlistAdded(string $studentName, int $studentCid, string $entryType, string $addedBy): void
    {
        $typeLabel = match($entryType) {
            'New Student'  => 'Home Student',
            'New Visitor'  => 'Visiting Controller',
            'New Transfer' => 'Transfer Controller',
            default        => $entryType,
        };

        $this->send([
            'embeds' => [[
                'title'       => 'Student Added to Waitlist',
                'description' => "**{$studentName}** ({$studentCid}) was added to the waitlist.",
                'color'       => 0xf59e0b,
                'fields'      => [
                    ['name' => 'Type',     'value' => $typeLabel,  'inline' => true],
                    ['name' => 'Added by', 'value' => $addedBy,    'inline' => true],
                ],
                'timestamp'   => now()->toIso8601String(),
            ]],
        ]);
    }

    public function studentLinked(string $studentName, int $studentCid, string $instructorName, string $linkedBy): void
    {
        $this->send([
            'embeds' => [[
                'title'       => 'Student Linked to Instructor',
                'description' => "**{$studentName}** ({$studentCid}) was linked to **{$instructorName}**.",
                'color'       => 0x22c55e,
                'fields'      => [
                    ['name' => 'Instructor', 'value' => $instructorName, 'inline' => true],
                    ['name' => 'Linked by',  'value' => $linkedBy,       'inline' => true],
                ],
                'timestamp'   => now()->toIso8601String(),
            ]],
        ]);
    }

    public function studentUnlinked(string $studentName, int $studentCid, string $removedBy): void
    {
        $this->send([
            'embeds' => [[
                'title'       => 'Student Unlinked & Removed',
                'description' => "**{$studentName}** ({$studentCid}) was unlinked from their instructor and removed from the training system.",
                'color'       => 0xef4444,
                'fields'      => [
                    ['name' => 'Removed by', 'value' => $removedBy, 'inline' => true],
                ],
                'timestamp'   => now()->toIso8601String(),
            ]],
        ]);
    }

    protected function send(array $payload): void
    {
        if (!$this->url) {
            return;
        }

        try {
            Http::post($this->url, $payload);
        } catch (\Exception $e) {
            // fire and forget
        }
    }
}
