<?php

return [
    'owner' => env('SECTOR_FILES_GITHUB_OWNER'),
    'repository' => env('SECTOR_FILES_GITHUB_REPOSITORY'),
    // Fine-grained token restricted to this repository, with Contents read/write.
    'token' => env('SECTOR_FILES_GITHUB_TOKEN'),
    'tag_prefix' => env('SECTOR_FILES_TAG_PREFIX', 'sector-'),
    'max_upload_kb' => 102400,
    'public_url' => env('SECTOR_FILES_PUBLIC_URL', 'https://winnipegfir.ca/atc/sector-files'),
    // Optional existing forum post/thread within the webhook's channel.
    'discord_thread_id' => env('SECTOR_FILES_DISCORD_THREAD_ID'),
    'discord_webhook' => env('SECTOR_FILES_DISCORD_WEBHOOK'),
];
