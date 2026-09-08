# CZWG Academy v39 — GitHub PR Overlay

This package was rebuilt against the clean CZWG master supplied on September 8, 2026. It restores the complete Academy additively and preserves the existing Site Settings page and all of its original cards.

Upload everything inside this folder into the repository root so the `app`, `config`, `database`, `public`, `resources`, `routes`, and `tests` folders merge with the existing folders.

## Included

- Academy courses, modules, progress, cumulative knowledge checks, submissions, and manual grading.
- Course thumbnails, static slideshow cards, Academy images, and audio.
- VATCAN home and visitor roster synchronization.
- Visitor Only Course controls and direct-route access protection.
- S3 visitor access through Terminal plus visitor courses; C1+ access through Center plus visitor courses.
- Searchable and independently scrollable home roster, visitor roster, and active assignment lists.
- Banner Appearance editor with live preview, themes, animations, icons, links, and enable/disable control.
- Shared banner on public and dashboard layouts.
- Original Site Information, Emails, Staff, Homepage Images, Homepage Towns, and Audit Log cards preserved.

## Deployment requirement

The deployment workflow must run:

```bash
php artisan migrate --force
php artisan optimize:clear
```

If the production deployment already runs Laravel migrations and clears caches automatically, no separate server command is required.
