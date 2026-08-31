# Contact Package — Reference Manual

How the package actually works today. For the history of *why* — decisions, bugs found, wrong
turns — see `CLAUDE.md`'s dated session log instead; this file only tracks current behaviour.

See also: `liberty/MANUAL.md` for the xref machinery underpinning this package.

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

Live on every real site as of 2026-08-10 (not all sites separately re-confirmed since). Migrated
via upgrade script `contact/admin/upgrades/5.0.3.php`:
1. Registers `contactperson` and `contactbusiness` content types
2. `UPDATE liberty_content SET content_type_guid = 'contactperson'` for records with a `$00` xref
3. Remaining `content_type_guid='contact'` records become `contactbusiness`

## Entry points

`view.php` — shared display for both types (no separate person/business display file, matches
its own `/contact/view/NNN` pretty URL and the bare `view.php`/`edit.php` pattern other
single-content-type packages use, e.g. mapper/fisheye). `edit.php` — shared editor for both
types. Listing is split three ways: `list_people.php`/`list_businesses.php`/`list_contacts.php`
(all three share `list.tpl`).

## `isValid()` — checks for a real record, not just a valid-looking id

`Contact::isValid()` (inherited by `ContactPerson`/`ContactBusiness`) queries `liberty_content`
directly for a matching `content_id` + `content_type_guid`, not just `verifyId($mContentId)` —
so a nonexistent `content_id` correctly 404s ("No contact exists with the given ID") rather than
`view.php` falling through to a bare list redirect or `edit.php` silently switching to create-new
mode. A `LibertyContent`-wide version of this fix was tried and reverted — see `liberty/CLAUDE.md`
for why; `stock` uses the same per-package pattern.

## `Contact::load()` — xref-derived mInfo fields

After `loadXrefInfo()`, pulls a few fields from the already-loaded `$this->mXrefInfo` straight
onto the flat `$this->mInfo` array for templates: `IMG` (gallery, `client_gallery`), `#L`
(location, `x_coordinate`/`y_coordinate` — hand-entered directly, see below), and the contact's
own address (`house` = `xkey_ext`, `postcode` = `xkey` — raw hand-entered values, no lookup).

**Address lookup is template-driven, not a fixed item code** (fixed 2026-08-31 — see
`CLAUDE.md`'s dated entry): `findAddressXref()` scans the loaded xref rows for whichever item(s)
have `liberty_xref_item.template = 'address'` (`#C`/`#I`/`#R`/`#S`/`#T` today, but a site could
register others), preferring the first one with a real postcode. The previous version hardcoded
`#S` (Service Address), which has no live data anywhere — real address data is almost always
under `#C` (Contact Address), occasionally `#R` (Residential); a contact can have more than one
populated at once. `export_contacts.php` already used this same `template === 'address'` filter
independently — `Contact::load()` was the one place still hardcoding a single item code.

**`address_postcode` (the OS-style postcode→area-code reference table) dropped entirely from
contact, same session** — was previously joined in three independent places (`Contact::load()`,
`Contact::getList()`, `export_contacts.php`) to enrich the raw address with `add1`-`county`
and, for `load()` only, a `grideast`/`gridnorth`-derived map pin (`phpcoord`'s `OSRef` class,
`contact/lib/phpcoord-2.3.php`) as a fallback when `#L` had no hand-entered coordinates. Also
turned out to be joined *blindly* at the liberty level — `LibertyXrefType::loadContent()` LEFT
JOINed `address_postcode` on every xref row of any content type, not just contact's, purely on
the chance an `xkey` matched a postcode string (feeding `view_address_item.tpl`'s `.address`
display, the only real consumer) — a package-agnostic core file depending on a table owned by
one specific package (contact), something liberty can't assume is even installed. All of it
removed: the liberty-level join, `Contact::load()`/`getList()`'s postcode enrichment (raw
`xkey`/`xkey_ext` only now — the "backup address details" already always stored on the xref row
itself), `export_contacts.php`'s CSV columns, `list.tpl`'s add1-4/town columns,
`view_address_item.tpl`'s `.address` display. `contact/lib/phpcoord-2.3.php` relocated to
`mapper/lib/` (unwired, parked for whenever mapper grows real coordinate-conversion needs) rather
than deleted outright — `#L`'s hand-entered `x_coordinate`/`y_coordinate` path is untouched and
still the only way to get a map pin on a contact. `address_postcode` itself stays in
`admin/schema_inc.php` for now (not dropped) — pending resolving a version-tracking mismatch
found while looking at how to do that properly (contact has no `admin/upgrades/` directory despite
`MANUAL.md` describing an already-run "5.0.3" upgrade; the DB's tracked
`package_contact_version` is still `5.0.1`).

`IMG`/`#L` stay as direct `findRowByItem()` lookups — genuinely fixed single-purpose item codes,
not subject to the same per-site variability as address items.

## CSV import xorder

`ImportContactCSV.php` explicitly sets xorder: 0 for single items, 1 for #P/#F (multiple=1).
Will need addressing when more than one record is needed per xref group.

## Delete / expunge

`edit.php` handles `expunge=1`: checks `p_contact_expunge`, calls `$gContent->expunge()`,
redirects to `list_contacts.php`. `contact_date_bar.tpl` uses
`{smartlink ... ifile="edit.php" expunge=1}`. `Contact::expunge()` deletes liberty_xref rows
then calls `LibertyContent::expunge()`.
