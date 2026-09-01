# Contact Package — Reference Manual

How the package actually works today. For the history of *why* — decisions, bugs found, wrong
turns — see `CLAUDE.md`'s dated session log instead; this file only tracks current behaviour.

See also: `liberty/MANUAL.md` for the xref machinery underpinning this package.

## Person vs Business subclasses

Two distinct content types, each its own `LibertyContent` subclass:
- `ContactPerson extends Contact` — `mContentTypeGuid = 'contactperson'`
- `ContactBusiness extends Contact` — `mContentTypeGuid = 'contactbusiness'`

Both share `Contact`'s common schema (addresses, SCREF, etc., registered at the bare
`content_type_guid='contact'` package level). The class itself *is* the person/business
distinction — `edit.php` detects which one it's rendering via `$gContent instanceof
\Bitweaver\Contact\ContactPerson`, not by inspecting any stored data.

- `add_person.php` — creates a `ContactPerson`, tags it with the `P01` type marker.
- `add_business.php` — creates a `ContactBusiness`; the user picks one or more subtype markers
  from `getXrefSourceList()` (Supplier, Manufacturer, etc. — expandable via the DB).

Type-tag markers live in `liberty_xref_item` at `sort_order=0` (Liberty's type-marker
convention — see `liberty/MANUAL.md` — excluded from the normal tabbed xref display,
loaded separately via `getTypeMarkers()`/`getContentTypeMarkers()`):
- `contactperson`'s `type` group: `P01` (Personal), `P02` (Business — a person acting in a
  business capacity, not a business record itself)
- `contactbusiness`'s `type` group: `B01`-`B04` (Service/Manufacturer/Distributor/Supplier)

`Contact::loadXrefTypeList()` loads these markers into `$this->mInfo['contact_types']`
(`getContentTypeMarkers()`) for display/edit-form use. Saving diffs the submitted set against
what's actually stored (`getTypeMarkerXrefs()`) rather than delete-all-then-reinsert, so an
unrelated add/remove doesn't reset an unchanged tag's `entry_date` — removed tags are hard-deleted
(`expunge=3`), added tags go through the normal `storeXref()`/`fAddXref` path. A hidden
`fContactTypesSubmitted` field distinguishes "the type-tag section was on this form with nothing
checked" (clear all tags) from "this caller doesn't touch type tags at all" (e.g. an import
script) — both would otherwise look identical as an absent `contact_types` key.

`display_contact.tpl` heading = "Personal Contact" / "Business Contact" from the same
`instanceof`/class check `edit.php` uses.

xref item templates gate dates and edit actions on `{$xrefAllowEdit}` (pass `allow_edit=false`
in view, `allow_edit=true` in edit).

**SCREF** — short reference code for a contact, stored in `liberty_xref.xkey` where `item='SCREF'`.
Used as the `from` value in stock movement CSVs to identify the supplier/source contact.
`contact/includes/lookup_contact.php` provides JSON autocomplete searching by `lc.title` or SCREF `xkey`.

## Name storage (`ContactPerson` only)

`liberty_content.title` holds the plain, sortable "Surname, Prefix Forename Suffix" form directly
(a real column, not derived at query time) — needed so a real SQL `ORDER BY lc.title` sorts
correctly for listing/pagination. The individual `prefix`/`forename`/`surname`/`suffix` parts
(needed to repopulate the edit form) live separately as a JSON array on a dedicated `NAME` xref
item, its own `name` group at `sort_order=0` — deliberately not sharing the `type` group's
sort_order=0 slot, since `NAME` isn't a togglable type marker and would otherwise leak into the
`P01`/`P02` type-tag picker.

`ContactPerson::load()` reads the `NAME` xref's JSON and overwrites `mInfo['title']`/`mInfo['name']`
with the fuller "Prefix Forename Surname Suffix" display form for templates — the DB's own
surname-led `lc.title` (used for listing/sorting) is intentionally overridden here for display
purposes only. `ContactPerson::verify()` builds the surname-led sort form from the edit form's
separate name fields before delegating to `Contact::verify()`, the same way `ContactBusiness`
passes its organisation string through as `title`.

`ContactBusiness` has no equivalent — `liberty_content.title` is just the organisation name
directly, no separate xref item involved.

## Entry points

`view.php` — shared display for both types (no separate person/business display file, matches
its own `/contact/view/NNN` pretty URL and the bare `view.php`/`edit.php` pattern other
single-content-type packages use, e.g. mapper/fisheye). `edit.php` — shared editor for both
types. Listing is split three ways: `list_people.php`/`list_businesses.php`/`list_contacts.php`
(all three share `list.tpl`).

## `isValid()` — checks for a real record, not just a valid-looking id

`Contact::isValid()` (inherited by `ContactPerson`/`ContactBusiness`) queries `liberty_content`
directly for a matching `content_id` + `content_type_guid`, not just a bare id-format check — so a
nonexistent `content_id` correctly 404s ("No contact exists with the given ID") rather than
`view.php` falling through to a bare list redirect or `edit.php` silently switching to create-new
mode. `stock` uses the same per-package pattern.

## `Contact::load()` — xref-derived mInfo fields

After `loadXrefInfo()`, pulls a few fields from the already-loaded `$this->mXrefInfo` straight
onto the flat `$this->mInfo` array for templates: `IMG` (gallery, `client_gallery`), `#L`
(location, `x_coordinate`/`y_coordinate` — hand-entered directly), and the contact's own address
(`house` = `xkey_ext`, `postcode` = `xkey` — raw hand-entered values, no lookup).

**Address lookup is template-driven, not a fixed item code**: `findAddressXref()` scans the loaded
xref rows for whichever item(s) have `liberty_xref_item.template = 'address'` (`#C`/`#I`/`#R`/`#S`/
`#T` today, but a site could register others), preferring the first one with a real postcode, since
a contact can have more than one address item populated at once. `export_contacts.php` uses the
same `template === 'address'` filter independently.

`address_postcode` (an OS-style postcode→area-code reference table) is no longer used anywhere in
this package or in Liberty core — address enrichment relies purely on whatever's hand-entered on
the address xref rows themselves (`xkey`/`xkey_ext`), with `#L`'s hand-entered
`x_coordinate`/`y_coordinate` as the only source of a map pin. Postcode/coordinate lookup belongs
to `mapper` (OSM-based) going forward, not this package.

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
