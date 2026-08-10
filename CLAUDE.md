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
