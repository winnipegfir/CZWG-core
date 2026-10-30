# Winnipeg FIR Sector Files: deployment and operation

## What this update provides

Staff publish one release with two required ZIPs: Full Install Package and Nav Data Update.
The newest non-archived release is prominent at the top of the public page, with both download
buttons. Older versions remain below it, newest first. Previously published single-ZIP releases
remain available as explicitly labelled legacy downloads; they are not assumed to be full packages.

Ryan's working files can remain on Google Drive. Staff prepare both ZIPs, enter the version/AIRAC,
title and notes, then publish after 0900Z on publication day in accordance with the team SOP.
Publishing is immediate, not scheduled. This update does not edit repository branch files or Drive.

## Pages and access

- Public: /atc/sector-files (no login).
- Staff: /dashboard/staff/sector-files (existing auth + staff middleware, permissions >= 4).
- ATC navigation and Staff dashboard include Sector Files.
- Dashboard ATC Resources links Winnipeg Enhanced Sector Files to the official GitHub Releases list.
  Existing VATSIM/vATIS resources and their access conditions remain unchanged.
- No database migrations, new Composer dependencies, or frontend build are required.

## Production configuration

Add these settings to the production environment, never to Git:

```dotenv
SECTOR_FILES_GITHUB_OWNER=winnipegfir
SECTOR_FILES_GITHUB_REPOSITORY=ZWG-Enhanced-Sector-File
SECTOR_FILES_GITHUB_TOKEN=YOUR_PRIVATE_TOKEN
SECTOR_FILES_TAG_PREFIX=sector-
SECTOR_FILES_PUBLIC_URL=https://winnipegfir.ca/atc/sector-files
SECTOR_FILES_DISCORD_WEBHOOK=YOUR_PRIVATE_WEBHOOK_URL
SECTOR_FILES_DISCORD_THREAD_ID=1468760234276163664
```

Use a fine-grained token scoped to the sector repository with Contents read/write and any required
organization approval. The repository must be public for anonymous ZIP downloads. Note the token's
expiration and replace it before expiry. GitHub App credential generation is not implemented.
The Discord setting is optional. The public URL defaults to the official address shown above and
must use HTTPS. Until the website feature is deployed, that public link will not work on the live site.
Never copy the local .env onto production or put a token/webhook into the change package.

Only release tags beginning with the configured nonempty prefix are managed. Existing releases with
other tag conventions are not imported. Confirm this convention with the Facility Engineer.
Immutable GitHub releases can prevent editing or deleting: check the repository policy before launch.

## Upload limits and server configuration

Each ZIP has a 100 MB application limit. Both are submitted in one request, so PHP and the web server
must allow their combined size, for example:

```ini
upload_max_filesize=100M
post_max_size=210M
max_execution_time=600
max_input_time=600
```

Allow at least two uploaded files and a web-server/proxy body limit of 210 MB. Allow approximately
600 seconds for the whole request when testing full-size packages; each GitHub upload times out at
180 seconds. Check disk space in PHP's temporary upload directory. No permanent local ZIP copies
are stored, and ZIPs are not extracted. Staff are responsible for reviewing package contents.
Multiple app servers need a shared lock-capable cache. App changes invalidate the public cache;
changes made directly in GitHub may take up to 60 seconds to appear.

## Failure handling

A release starts as a hidden GitHub draft. Both named assets must be uploaded and confirmed before
publication. If either upload fails, nothing is published or announced. Inspect history before retrying:
- Both packages uploaded: Publish draft can finish the operation.
- Incomplete draft: delete the draft, then submit both packages again.
- Already published: do not create a duplicate; check its public download links.

No mutating request is automatically retried. Archive/restore is recorded in GitHub notes and preserves
assets. Deleting requires confirmation and removes the release and both ZIPs, but retains the Git tag.
Restoring an archived release does not restore repository files or update files already downloaded.
The app leaves GitHub's own Latest badge unchanged; its website uses publication date and archive status.

## Discord

After successful publication, the checked announcement option sends title, version/AIRAC, release notes
and the public website link. Long notes are shortened to fit Discord with a notice to read the full notes
on the website. Both package choices are described. Mentions are disabled. No ZIP is attached to Discord:
this is a link-based alternative to the SOP's current attachment workflow and should be agreed with Ryan.
Webhook failure does not undo publication. Check the channel before manually reposting an uncertain delivery.
Editing notes/title does not resend or edit an existing Discord message.

API references: https://docs.github.com/en/rest/releases/releases and
https://docs.discord.com/developers/resources/webhook

## Apply and release

The supplied patch is against official winnipegfir/CZWG-core master at
`e784b6b` (fetched October 29, 2026). It includes the complete feature because the official base
had no Sector Files feature. The files-only ZIP contains just the 16 changed/added repository files,
not the whole application. Prefer the patch: replacing shared files can overwrite subsequent edits.

In a clean current website checkout, create a feature branch, run `git apply --check` on the supplied
patch, then `git apply`. Review and commit the changes, push the branch, and open a PR against the
website repository's master branch. Do not apply the full patch over the already-updated Laragon copy.

After merging and deploying through your usual website deployment process, configure the environment
and clear the compiled caches:

```sh
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Rebuild config/route/view caches only if your normal deployment uses them. No migration is needed.
Before public announcement, check anonymous downloads, staff permissions, both package labels,
the actual server upload limits, and the public Discord link. Use a disposable GitHub repository and
restricted Discord channel for test uploads. Local tests pointed at the official repository still
publish real releases. Remove any earlier test release separately if appropriate.

Rollback: revert the website feature commit and deploy the prior code. This does not delete releases
already stored on GitHub. Keep deployment backups and do not delete valid release assets as a rollback.

## Validation

25 feature tests / 126 assertions cover both uploads before publication, failed second uploads,
public ordering, separate download labels, legacy compatibility, private-repository protection,
all staff-route permission checks, archive/restore/edit/delete behavior, concurrency locks, pagination,
Discord content bounds/public links/no mentions, and no announcement on edit.
Tests fake all GitHub and Discord calls and do not publish real releases or send real announcements.
Run `vendor/bin/phpunit --filter SectorFilesTest`.
The test fixture layouts avoid unrelated site database requirements. Sample-data rendering and installed route/template checks also passed. Full browser visual verification remains to be completed on the running local site.
The existing Composer lock/manifest mismatch was not changed as part of this feature.

Discord Update Hub: create the webhook on the parent tech-support forum channel. The optional thread ID targets the existing UPDATE HUB post. Leave it blank for a regular text channel. Confirm posting works with the thread moderation state before launch; an archived locked thread cannot be automatically reopened by a webhook. No live Discord message was sent during automated checks.
