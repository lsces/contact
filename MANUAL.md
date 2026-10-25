# Contact — User Manual

Contact is an address book for people and businesses. A contact is a full record of its own, not a line in a
table: it can have as many addresses, telephone numbers and email addresses as it needs, a rich-text description,
a thread of notes about conversations with it, and a photo grid. Other packages (stock, orders and so on) link to a
contact rather than keeping their own free-text names.

For how the package is built, see [`DEVELOPER.md`](DEVELOPER.md). For the contacts that describe public figures —
actors, directors, authors, bands — see the [`contactwiki`](../contactwiki/MANUAL.md) manual; those are a separate kind
of contact with their own page.

## Two kinds of contact

- **Person** — has a name made of parts (prefix, forename, surname, suffix) and is tagged *Personal* or *Business*
  (a person acting in a business capacity, as opposed to a business record itself).
- **Business** — has an organisation name and one or more subtype tags such as *Service*, *Manufacturer*,
  *Distributor* and *Supplier*.

The tags a site offers are set by an administrator (see [Settings for an administrator](#settings-for-an-administrator)),
so your list may differ.

## Finding contacts

The **Contact** menu has three lists:

- **List People** — people only, sorted by surname.
- **List Businesses** — businesses only.
- **All Contacts** — both, with a search box and the tag filter below.

**All Contacts** can be narrowed with the tick-boxes at the top, one row of tags per kind of contact (for example
*Supplier* or *Manufacturer* for businesses). A contact is listed if it has any of the tags ticked, and with nothing ticked
everything is listed. The **All** box at the start of a row ticks or clears the whole row. Lists are split into pages.
Select a name to open the contact. You need permission to browse the contact list (by default, any registered user).

## Adding a contact

- **Add Person** — fill in the name parts and choose *Personal* or *Business*, then save.
- **Add Business** — enter the organisation name and tick the subtypes that apply, then save.

Adding needs the permission to create contacts (by default, editors). Saving opens the new contact, where the addresses,
telephone numbers and the rest are added with the edit page.

## Viewing a contact

The page shows, from the top:

- the **name** and a heading saying *Personal Contact* or *Business Contact*, with icons to **edit** and **print**;
- a **date bar** with when the contact was created and last changed;
- the **details**: the main address, and for a person the date of birth, with the user account it is linked to if there
  is one;
- the contact's **description** (the Notes, below);
- tabs for the other kinds of information it holds, such as further addresses, telephone numbers, email addresses and
  web sites (these appear only when there is something in them);
- **Local Notes**, the conversation thread — see below (shown to anyone who can read or post comments);
- for a business with clients, a **Client List** tab;
- a **photo grid** of the contact's own pictures, when it has some.

### Local Notes — the conversation thread

**Local Notes** is where the conversations with a contact are kept: a dated thread of notes about calls, emails and
meetings, each added with the comment form under the thread. Anyone who has permission to post comments can add to it, and
anyone who can read comments can read it. The thread belongs to the contact, so it stays with the record wherever the contact is linked from. It is different from
the contact's description: the description says who the contact *is*, Local Notes says what has *happened*.

## Editing a contact

Use the **Edit Contact** icon on the contact's page. The edit page has two tabs.

**The first tab** holds, from the top:

1. **The details** — for a person the name parts and the *Personal*/*Business* tags; for a business the organisation
   name and the subtype tags.
2. **Notes** — the contact's description, in the rich-text editor, with a **Comment** box beside it for a short note about
   what you changed.
3. **The information tabs** — one for each kind of information: contact address, residential address, invoice address,
   service address, tenant address, telephone, fax, email, web site, and free-format information. Each tab lists what is
   there and has an **add** icon; a contact can have as many of each as it needs. Edit or remove a row with the icons at
   the end of the row.

**Liberty Extensions** holds the settings that belong to the content system rather than to the contact: the user
account the contact is linked to (administrators only), categories where they are switched on, and other services such
as tags or a protection level when the site uses them.

Press **Save** to keep the changes or **Cancel** to leave them.

## Removing a contact

Removing a contact deletes its record and all its information, and cannot be undone, so it needs its own permission (by
default, administrators only). Open the contact's **edit** page and use the **Delete Contact** icon at the top; it asks
you to confirm.

## Business information used by other packages

A business can also carry a few reference items that other packages read, when a site has switched them on:

- a **stock source reference** — a short code that the stock package's movement import uses to find the supplier;
- an **account number** and a **VAT number**.

## Exporting contacts

Every contact can be downloaded as a spreadsheet (CSV file) from `export_contacts.php` in the contact folder. There is no
menu entry for it, and it needs only the permission to browse contacts. The file has one row per contact with its name,
address (the full address text and the postcode), up to three telephone numbers and up to two email addresses.

## Settings for an administrator

Under **Administration → Contact**:

- **Contact Type List** — the *Personal/Business* and business-subtype tags people can choose from.
- **Contact Xref List** — the kinds of information (address, telephone, email ...) a contact can hold, and which of them
  show for a person and which for a business.

## Permissions

| Permission | Allows | Default |
|---|---|---|
| View | browse the list and open contacts | registered users |
| Create | add a person or a business | editors |
| Update | edit a contact | editors |
| Expunge | remove a contact | administrators |
| Admin | the settings above | administrators |
