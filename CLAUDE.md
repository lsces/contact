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

## Planned: ContactPerson / ContactBusiness subclasses

The current `$isPerson` detection via `$00` xref presence is a hack. The plan replaces it
with proper subclasses following the dual-guid xref pattern (as per stock):

- `ContactPerson extends Contact` — `mContentTypeGuid = 'contactperson'`, `mPackageGuid = 'contact'`
- `ContactBusiness extends Contact` — `mContentTypeGuid = 'contactbusiness'`, `mPackageGuid = 'contact'`

Shared schema (addresses, SCREF etc.) stays at `content_type_guid='contact'`.
Person-specific types (`$00` default, kitelf, committee member etc.) at `contactperson` level.
Business subtypes (`$02`+: Supplier, Manufacturer etc.) at `contactbusiness` level.
`$isPerson` flag disappears — the class IS the distinction.
Template resolution already works via `mContentTypeGuid` path lookup in LibertyContent.

**Not yet implemented.** Development/testing on `rainbowdigitalmedia` first.
Upgrade script `contact/admin/upgrades/5.0.3.php` will:
1. Register `contactperson` and `contactbusiness` content types
2. `UPDATE liberty_content SET content_type_guid = 'contactperson'` for records with a `$00` xref
3. Remaining `content_type_guid='contact'` records become `contactbusiness`

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
