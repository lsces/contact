# Contact

A [Bitweaver](https://github.com/lsces/bitweaver) package for people and business contacts — a
real address book in its own right, and also the shared identity layer other packages
(suppliers, kit-holders, sources) link against rather than each rolling their own contact fields.

**Status: active, in production use.**

## Why this exists

A proper address book needs to hold more than a single address/phone/email per person or
business — real contacts have a home and a work address, a mobile and a landline, several email
addresses. Most address book tools (Thunderbird's included) still model a contact as a fixed set
of single-value fields. This package builds contacts on Liberty's xref system instead, where each
of those — address, phone number, email — is its own expandable, multi-value item attached to the
contact record, not a fixed field slot. The same mechanism also makes a contact a real, linkable
record any other package can point at, rather than a free-text name field — but that's a
consequence of the model, not the whole point of it.

## What it does

- **A genuine address book** — people and businesses as full content records, each with as many
  addresses/phone numbers/email addresses as it actually needs, not capped at one of each
- **People and businesses as distinct types** (`ContactPerson`/`ContactBusiness`), sharing common
  fields but each with their own type-specific detail — people get name-structure fields,
  businesses get a set of subtype tags (Supplier, Manufacturer, etc.)
- **A short reference code (SCREF)** per contact, usable as a lightweight lookup key from other
  packages — [`stock`](https://github.com/lsces/stock)'s movement CSV import uses this to resolve
  a supplier without needing a full content_id
- **Search/autocomplete** other packages can call directly (`includes/lookup_contact.php`) rather
  than duplicating contact lookup logic

## What's planned

- Merging email correspondence history into each contact's own record — matching an inbound/
  outbound message to the right contact and keeping it alongside their other detail, rather than
  correspondence living only in a separate mail client with no link back to who it's actually with
- A few small internal cleanups — some dead code paths from an earlier contact-model iteration
  that were never fully removed (see `MANUAL.md`'s "Known gap" note), low priority since they're
  inert rather than broken

See `MANUAL.md` for the full current picture — schema, the person/business type model, and how
other packages are expected to link against a contact record.

## Requirements

- [Bitweaver](https://github.com/lsces/bitweaver) 5.x
- [`liberty`](https://github.com/lsces/liberty) package — built entirely on Liberty's generic
  content/xref framework, same foundation [`stock`](https://github.com/lsces/stock) and
  [`food`](https://github.com/lsces/food) use

Since this package isn't through a stable install/upgrade cycle yet, see `MANUAL.md` in this repo
for the current schema-deployment approach if you're installing it fresh (`CLAUDE.md` is a dated
development log, not a reference — useful for *why* something's built the way it is, not *how*
to set it up).
