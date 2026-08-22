# Contact Package — Developer Notes

Dated history: decisions, bugs found, why things are the way they are, open follow-ups. For the
current architecture/reference — schema, mechanisms, what's built — see `MANUAL.md` instead;
this file only tracks how it got there.

See also: `liberty/CLAUDE.md` for xref machinery underpinning this package.

## 2026-08-10 — ContactPerson/ContactBusiness confirmed live, view.php renamed, isValid() fixed

**Stale-docs correction**: this file previously said ContactPerson/ContactBusiness were "not yet
implemented, testing on rainbowdigitalmedia first" — found stale when checked against `merg`'s
DB: 0 rows at `content_type_guid='contact'`, 11 across `contactperson`/`contactbusiness`. Not
separately re-verified on every other site.

**Entry points tidied**: `display_contact.php` renamed to `view.php` — no separate person/
business display ever existed, so the suffix was never doing anything; matches the bare
`view.php`/`edit.php` pattern other single-content-type packages use (mapper, fisheye).
`list1.php`/`list2.php` (+ `list2.tpl`) deleted same day — dead, unreachable from any menu/
template, fully superseded by `list_people.php`/`list_businesses.php`/`list_contacts.php`.

**`isValid()` real bug fixed**: before this, a nonexistent `content_id` read as "valid" through
the base `LibertyContent::isValid()` — `view.php` fell straight through to a bare list redirect
with no informative message, `edit.php` silently fell into create-new mode. Given `Contact` (and
by inheritance `ContactPerson`/`ContactBusiness`) a real DB-querying override — see `MANUAL.md`'s
`isValid()` section for current behaviour. Same fix applied to `stock`'s classes the same day; a
`LibertyContent`-wide version was tried and reverted, see `liberty/CLAUDE.md` for why.

**Side effect, found 2026-08-11**: this same `isValid()` change exposed a real kernel destructor
bug — `Contact::isValid()` was the most common trigger, crashing live on srv10 until fixed. Full
detail in `kernel/CLAUDE.md`'s "APCu object cache" entry.

## 2026-08-19 — Install/reinstall/uninstall saga

`contact` was the first package put through a genuine fatal-mid-cycle install test this session
(rebuilding rdmcloud from scratch via `install/install.php`), surfacing both real installer bugs
(fixed at the kernel/installer level, see the top-level `bitweaver/CLAUDE.md` entry — `install`
has no `CLAUDE.md` of its own) and several bugs specific to this package's own schema:

- **Duplicate `liberty_content_types` registration.** That table predates `liberty_xref` entirely
  and already has its own original, idempotent registration path -
  `LibertySystem::registerContentType()`, called from `bit_setup_inc.php`, checks the DB before
  inserting and updates in place if the row differs. `schema_inc.php`'s own `defaults` array
  *also* declared raw `INSERT INTO liberty_content_types` statements for `contactperson`/
  `contactbusiness` - non-idempotent, so any reinstall with 'settings' selected (which re-runs
  `defaults`) collided with the row `registerContentType()` had already put there. Removed the
  duplicate declarations from `schema_inc.php` entirely.
- **No `registerContentObjects()` call at all** - every other content-bearing package (stock,
  mapper, wiki, blogs, boards, articles, fisheye, food) calls this in its `schema_inc.php`;
  `contact` never did. The installer's own uninstall cleanup derives each `content_type_guid` by
  instantiating classes off `$gBitInstaller->mContentClasses[$package]` (`BIT_INSTALL` blocks the
  normal `$gLibertySystem->mContentTypes` lookup entirely during install) - with nothing
  registered there for `contact`, that cleanup silently found nothing to do, leaving
  `liberty_content_types`/`liberty_xref_group`/`liberty_xref_item` rows behind on *every single
  uninstall*, ready to collide with the next reinstall's own defaults. Added the missing
  `registerContentObjects()` call (`Contact`, `ContactPerson`, `ContactBusiness`).
- **Three "development upgrade" files deleted** (`admin/upgrades/5.0.1.php`/`5.0.2.php`/
  `5.0.3.php`) - pure legacy migrations for tables/xref-item-codes (`contact_xref_type`, old `$0x`
  style xref items) that only ever existed on one real pre-existing production site, never on a
  fresh install. Picked up by the installer's own upgrade-file loader regardless (once landed on
  the separate, session-only "Upgrade" step, reachable via a direct `?step=5` link) and fataled
  referencing tables that don't exist in the current `schema_inc.php` at all. Package version
  rolled back from `5.0.3` (the deleted files' own latest version tag) to `5.0.1` (the base
  schema's now-single baseline) on all three machines; `food`/`stock`'s own declared `contact`
  dependency dropped from `5.0.2` to `5.0.1` to match.

**Runtime self-heal caveat found along the way**: `BitSystem.php`'s package-status bootstrap
(runs on every page load, not just install) will auto-demote a package's `package_X` config from
`y` (active) back to `i` (installed-not-active) if it ever detects `installed=true` but
`isFeatureActive('package_X')` isn't exactly `y` at that instant - happened at least twice this
session with no fully-confirmed trigger, taking `package_contact_version` down to nothing at the
same time. Fix is just re-setting both directly (`package_contact`→`y`,
`package_contact_version`→whatever the current baseline is) - didn't recur once contact settled
into a genuinely stable installed state, but worth checking first if a package "goes disabled"
with no obvious cause.

Full 10-supermarket-supplier CSV rebuild (needed after this same testing wiped the real
`contactbusiness` records as a side effect of table drop/recreate cycles) - detail in
`food/CLAUDE.md`.

## Pending

`Contact::load()`'s dead `IMG`/`#S`/`#L` joins and the raw-xref-join cleanup — see `MANUAL.md`'s
"Known gap" note under `Contact::load()`. Not scoped or scheduled.
