# Public VATCAN Academy roster sync fix

## What failed

The Academy was selecting the authenticated VATCAN API whenever an API key was
present, even though this installation was designed to obtain membership from
the public CZWG facility page. The public page also currently contains one home
member whose rating is `Suspended`; the old strict rating parser treated that
status as a broken roster and rejected the entire synchronization.

## What this changes

- The public CZWG facility roster is the primary Academy roster source.
- A configured authenticated API is retained only as a validated fallback when
  the public page is unavailable.
- `Suspended` and `Inactive` home entries are safely excluded from Academy
  membership rather than invalidating the full roster.
- Existing minimum-member and maximum-drop safety checks remain unchanged.
- Existing Academy memberships and enrollments are left untouched if neither
  source supplies a valid roster.

## Scope

Only these repository files change:

- `app/Services/VatcanService.php`
- `tests/Unit/VatcanPublicRosterTest.php`

No migrations, courses, views, routes, Site Settings, homepage files, or
unrelated controller-tracking systems are changed.

## Test and deploy

After replacing the two files in their matching repository folders, run:

```bat
php artisan test --filter=VatcanPublicRosterTest
php artisan optimize:clear
```

After deployment, open **Academy Enrollments** and select **Sync VATCAN Roster
Now**. The source should read **Public VATCAN CZWG roster**. The current public
page has 47 home rows; one is suspended, so 46 eligible home controllers should
synchronize. It currently has 10 visiting-controller rows.

No migration is required.

## Pull request

**Title:** Use the public VATCAN roster for Academy sync

**Description:**

Uses the public CZWG facility roster as the Academy's primary membership source,
retains the authenticated endpoint only as a validated fallback, and safely
excludes suspended/inactive members without rejecting the complete roster. All
existing destructive-change safety checks remain in place.
