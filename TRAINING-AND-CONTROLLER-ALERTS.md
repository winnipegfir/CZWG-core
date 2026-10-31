# Double training and controller Discord alerts

## Double training

The availability form has an optional Double training checkbox. A normal slot still has one seat. Double training creates two independent seats owned by the same provider, with shared availability identity and separate session IDs. Each student books a one-hour window, receives their own confirmation, and keeps their own booking/training history. Existing eligibility rules remain in effect for each booking.

Splitting an availability window preserves its seat identity. Cancellation reopens only that seat and merges adjacent open time only within that seat. Two students may overlap only on different seats in the same double-training group. A third student, repeat booking by the same student, unrelated provider overlap, and ineligible student bookings are rejected. Existing provider/user row locks serialize booking changes on production databases that support row locking.

Calendar/table labels and booking notifications identify double training. Instructors still confirm students separately. Removing one open seat removes only that seat; to remove the whole double availability, remove both open seats. Cancel booked seats individually first. This update does not convert or combine existing booked sessions automatically. Create double availability for future shared sessions.

## Controller alerts

Two separate incoming Discord webhooks are required:

- Online channel: normal controller connections and Student on solo position announcements.
- Staff-only review channel: missing/inactive/ambiguous roster entries, rating or position mismatches, and Training endorsements. These are review prompts, not automatic findings of misconduct. The application never disconnects anyone or changes roster permissions.

Configure ordinary text-channel webhooks (not forum-thread URLs) privately in the production environment:

```dotenv
CONTROLLER_ALERTS_ENABLED=false
CONTROLLER_ALERTS_ONLINE_WEBHOOK=YOUR_ONLINE_CHANNEL_WEBHOOK
CONTROLLER_ALERTS_REVIEW_WEBHOOK=YOUR_PRIVATE_STAFF_CHANNEL_WEBHOOK
```

Set enabled=true only after migrations, channel privacy, and configuration have been checked. Do not reuse the public webhook for the private setting. A missing private webhook never falls back to the public channel. No Discord bot is required. Keep both URLs out of Git and screenshots.

### Classification

The live CID is matched directly against the website's roster table, which contains both home and visitor records. A website login is not required. Unknown or duplicated CID records go to private review. The local active flag is checked; daily currency totals do not automatically remove authorization.

| Position suffix | Local endorsement | Normal minimum network rating |
| --- | --- | --- |
| DEL | del | S1 |
| GND | gnd | S1 |
| TWR | twr | S2 |
| DEP | dep | S3 |
| APP / ARR | app | S3 |
| CTR | ctr | C1 |

The rating and facility type from the live feed are checked together with the position-specific local endorsement. Roster values are 1=None, 2=Training, 3=Solo, 4=Certified. The site owner confirmed that the current Solo badge is authoritative and staff remove expired badges. Solo may authorize the next rating's position (S1 or above); OBS is never treated as a solo controller. There is no automatic expiry or airport-specific solo validation in the current data model.

I1/I3 and C3 are treated at C1 qualification level; unusual, unused, SUP or ADM ratings receive private review rather than assuming they grant position privileges. An OBS rating on an actual controlling facility triggers private review. Observer and ATIS connections themselves are ignored. Training/supervision cannot be proven from the data feed, so Training and above-rating cases request staff review without declaring the controller unauthorized.

Exact callsign prefixes reuse the existing website list: ZWG, CZWG, CYWG, CYAV, CYPG, CYQR, CYXE, CYQT, CYMJ, WPG. Intermediate sector letters are supported (e.g. CZWG_E_CTR). Unsupported suffixes such as FSS/FMP are excluded from this feature; add them only after agreeing their rules. APP and ARR use the approach/arrival endorsement; DEP uses departure separately.

Reference: https://vatsim.net/docs/basics/becoming-a-controller/ and the VATSIM GCAP solo-endorsement rules. Local roster policy takes part in classification; a rating alone is not proof of local authorization.

### Polling and delivery

`controllers:announce-online` runs once per minute through the existing Laravel scheduler, in the background so webhook delays do not hold up scheduled releases or other jobs. The first enabled poll announces currently connected eligible positions. A missing/stale (over five minutes) or failed feed produces no controller alerts. Both commands use a lock-capable shared cache if multiple servers run the scheduler.

Deduplication is stored in `controller_alert_events`, keyed by CID, callsign, network logon time, and classification. The same connection does not announce every minute or after a brief missing feed. Genuine reconnects with a new logon time announce again; a classification change can generate one new message. There are no offline messages.

Each event is claimed before sending. Failed or uncertain deliveries are marked unconfirmed and logged without the webhook URL. They are not automatically retried, avoiding duplicate posts after uncertain responses. A process crash after claiming can leave an attempting event; inspect Discord before manually posting. At most 40 new deliveries are attempted per poll; further controllers are considered on later polls. Mentions are disabled. Event rows retain CID, callsign, classification and delivery status, not webhook secrets.

## Deployment

This update needs two additive migrations; it must not be deployed as a code-only change. Back up the production database using the normal hosting procedure first. The baseline is official master 5e8bd7e.

1. Review and merge the PR through the usual deployment process. Coordinate migration execution with code activation because training queries use the new columns.
2. Apply the two migrations on the actual production database, using the deployment's correct PHP/environment:

```sh
php artisan migrate --path=database/migrations/2026_10_30_000001_add_double_training_seats.php --force
php artisan migrate --path=database/migrations/2026_10_30_000002_create_controller_alert_events.php --force
```

3. Clear/rebuild config, route and view caches using the normal deployment process. No Composer dependency or frontend build is required.
4. Keep controller alerts disabled until the two Discord destinations are ready. Test webhooks in restricted test channels before pointing at the public/staff destinations. Running the command with enabled=true sends real messages; it is not a read-only diagnostic.
5. Verify double availability with two test students: book both, reject a third, cancel one, confirm the other remains, and verify new booking eligibility. Verify actual concurrent requests on a MySQL staging copy; SQLite tests do not prove MySQL locking behavior.
6. Confirm the production scheduler executes background jobs as the normal application user and that the web and scheduler processes use the same migrated database/cache. The site currently uses container PHP for web traffic and host PHP for cron; verify their settings agree.

Rollback: disable controller alerts and clear config first. Preserve both new tables/columns while reverting application code. Do not remove seat metadata while double-training records remain: its migration intentionally refuses that rollback to avoid losing shared-booking identity. Resolve double availability/bookings before returning to single-slot behavior.

## Validation

65 tests / 291 assertions passed: new focused SQLite training/alert tests, existing booking eligibility unit tests, and Sector Files regression tests. External feed and Discord calls were simulated; no live announcements or production writes were made. Tests cover two-seat capacity, duplicate student rejection, independent confirmation/cancellation, single-seat behavior, eligibility, labels, position/rating/solo cases, channel separation, deduplication, reconnects, stale data and uncertain delivery.

The full legacy MentorBookingTest suite cannot run on the provided SQLite setup because an existing historical migration drops a foreign key using an unsupported SQLite operation. That unrelated migration was not changed. New focused tests create the relevant pre-change schema and execute the new migrations. Browser visual review and MySQL concurrency verification remain staging checks.
