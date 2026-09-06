# Academy public VATCAN roster sync

The Academy can synchronize Winnipeg home controllers from the public VATCAN CZWG facility roster without an API key.

## Behaviour

- The authenticated VATCAN API remains the preferred source when `VATCAN_API_KEY` is configured.
- Without an API key, the sync reads `https://vatcan.ca/division/facility/CZWG`.
- Only the **Home Controllers** table is used for course entitlements. Visitors are counted and ignored.
- Members are matched by CID. A CID without a local website account is retained as pending and claimed automatically on first login.
- The sync runs hourly through Laravel's scheduler and can also be run from Academy Enrollments or with:

  ```bash
  php artisan academy:sync-vatcan-roster
  ```

## Safety

No memberships or enrollments are changed when the request fails, required table headings are missing, a CID or rating is invalid, fewer than five home controllers are returned, or the roster falls more than 25% from the last successful snapshot.

These optional environment values can override the defaults:

```dotenv
VATCAN_PUBLIC_ROSTER_URL=https://vatcan.ca/division/facility/CZWG
VATCAN_PUBLIC_ROSTER_MIN_MEMBERS=5
VATCAN_PUBLIC_ROSTER_MAX_DROP_PERCENT=25
VATCAN_PUBLIC_ROSTER_USER_AGENT="Winnipeg FIR Academy roster sync"
```

The server must run Laravel's scheduler (normally `php artisan schedule:run` every minute) for the hourly automatic sync. No new migration is required for this change.

## Deployment check

```bash
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan academy:sync-vatcan-roster
php artisan schedule:list
```

Confirm the manual sync reports a realistic home-controller count before relying on the hourly schedule.
