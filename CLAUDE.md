# Contact Package — Developer Notes

See also: `liberty/CLAUDE.md` for xref machinery underpinning this package.

## Person vs Business model

Two distinct contact types, entered via separate pages:
- `add_person.php` — auto-injects `$00` type; name stored pipe-separated
  (`prefix|forename|surname|suffix`) in `liberty_xref.xkey_ext` of the `$00` record;
  `lc.title` = surname; redirects to `edit.php` for further detail
- `add_business.php` — no `$00`; user picks from `$02`+ subtypes (Supplier, Manufacturer
  etc., expandable via DB); `lc.title` = organisation name; redirects to `edit.php`

Type codes in `liberty_xref_item` (`content_type_guid='contact'`, `x_group='type'`, `sort_order=0`):
- `$00` Person — triggers name fields in edit/display; never shown as a checkbox in UI
- `$01` Organisation — deprecated, not used in new UI
- `$02`+ Business subtypes — shown as checkboxes in `add_business.php` and `edit.php`

`edit.php` detects person via `contact_types[0].content_id` → `$isPerson` flag:
- Person: name fields shown, Contact Types section hidden, Organisation hidden
- Business: org field shown, Contact Types (`$02`+) shown, name fields hidden

`display_contact.tpl` heading = "Personal Contact" / "Business Contact" from `contact_types.0.content_id`.
Name loaded from `$00` xref `xkey_ext` via SQL join in `Contact::load()` (`x00.xkey_ext AS name`).

xref item templates gate dates and edit actions on `{$xrefAllowEdit}` (pass `allow_edit=false`
in view, `allow_edit=true` in edit).

**`contact_types`** — separate from the display path. Populated by `loadXrefTypeList()` which
queries sort_order=0 items (the 'type' group: `$00`, `$02`+). Used for `$isPerson` detection
in `edit.php` and display templates. `loadXrefInfo()` deliberately excludes sort_order=0,
so there is no overlap.

**SCREF** — short reference code for a contact, stored in `liberty_xref.xkey` where `item='SCREF'`.
Used as the `from` value in stock movement CSVs to identify the supplier/source contact.
`contact/includes/lookup_contact.php` provides JSON autocomplete searching by `lc.title` or SCREF `xkey`.

## ContactPerson / ContactBusiness subclasses

Replaces the old `$isPerson` hack (`$00` xref presence) with proper subclasses following the
dual-guid xref pattern (as per stock):

- `ContactPerson extends Contact` — `mContentTypeGuid = 'contactperson'`, `mPackageGuid = 'contact'`
- `ContactBusiness extends Contact` — `mContentTypeGuid = 'contactbusiness'`, `mPackageGuid = 'contact'`

Shared schema (addresses, SCREF etc.) stays at `content_type_guid='contact'`.
Person-specific types (`$00` default, kitelf, committee member etc.) at `contactperson` level.
Business subtypes (`$02`+: Supplier, Manufacturer etc.) at `contactbusiness` level.
`$isPerson` flag disappears — the class IS the distinction.
Template resolution works via `mContentTypeGuid` path lookup in LibertyContent.
`lookup_contact_inc.php` returns the correctly-typed subclass via `getLibertyObject()`.

**Implemented and live on `merg`** (found 2026-08-10 — this section previously said "not yet
implemented, testing on rainbowdigitalmedia first", which was stale; confirmed via merg's DB:
0 rows at `content_type_guid='contact'`, 11 across `contactperson`/`contactbusiness`). Not
verified whether every other site has actually run the migration — check before assuming.
Upgrade script `contact/admin/upgrades/5.0.3.php`:
1. Registers `contactperson` and `contactbusiness` content types
2. `UPDATE liberty_content SET content_type_guid = 'contactperson'` for records with a `$00` xref
3. Remaining `content_type_guid='contact'` records become `contactbusiness`

## Entry points
`view.php` (renamed from `display_contact.php` 2026-08-10 — no separate person/business
display, so no suffix was ever needed; matches its own existing `/contact/view/NNN` pretty
URL and the bare `view.php`/`edit.php` pattern other single-content-type packages use, e.g.
mapper/fisheye). `edit.php` is the shared editor for both types. `list1.php`/`list2.php` (+
`list2.tpl`) deleted same day — dead, unreachable from any menu/template, superseded by
`list_people.php`/`list_businesses.php`/`list_contacts.php` (all three share `list.tpl`).

**`isValid()` — checks for a real record, not just a valid-looking id** (2026-08-10):
`Contact::isValid()` (inherited by `ContactPerson`/`ContactBusiness`) queries `liberty_content`
directly for a matching `content_id` + `content_type_guid`, not just `verifyId($mContentId)`.
Before this, a nonexistent `content_id` read as "valid" — `view.php` fell straight through to
`isValid()`'s redirect-to-list without an informative message, and `edit.php` silently fell
into create-new mode. Both now show a proper "No contact exists with the given ID" 404 when a
content_id was given but didn't resolve. A `LibertyContent`-wide version of this fix was tried
and reverted — see `liberty/CLAUDE.md` for why; same per-package pattern used in `stock`.
This same change also exposed a real kernel destructor bug (crashed live on srv10 until fixed
2026-08-11, `Contact::isValid()` was the most common trigger) — see `liberty/CLAUDE.md`'s
"Side effect found 2026-08-11" note.

## Contact::load() — raw xref joins
Joins `liberty_xref` directly for `$00` (person name), `#S` (service address), `#L` (location),
`IMG` (gallery). `IMG`, `#S`, `#L` have no live data.

**Pending cleanup** — remove the three commented-out dead joins; replace active raw xref joins
with the proper path: `$00` name from `loadXrefTypeList()` with `xkey_ext` added;
`#S`/`#L`/`ap` from `loadXrefInfo()` address group (postcode join already present in
`LibertyXrefGroup::loadXrefs()`); gallery association needs rethinking separately.

## CSV import xorder
`ImportContactCSV.php` explicitly sets xorder: 0 for single items, 1 for #P/#F (multiple=1).
Will need addressing when more than one record is needed per xref group.

## Delete / expunge
`edit.php` handles `expunge=1`: checks `p_contact_expunge`, calls `$gContent->expunge()`,
redirects to `list_contacts.php`. `contact_date_bar.tpl` uses
`{smartlink ... ifile="edit.php" expunge=1}`. `Contact::expunge()` deletes liberty_xref rows
then calls `LibertyContent::expunge()`.

## Install/reinstall/uninstall saga — 2026-08-19
`contact` was the first package put through a genuine fatal-mid-cycle install test this session
(rebuilding rdmcloud from scratch via `install/install.php`), surfacing both real installer bugs
(fixed in `install/CLAUDE.md`-equivalent detail, see the top-level `bitweaver/CLAUDE.md` entry -
`install` has no `CLAUDE.md` of its own) and several bugs specific to this package's own schema:

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
