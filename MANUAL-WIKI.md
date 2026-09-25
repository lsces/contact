# Contact as a Universal Person/Entity Hub — Design

**Status: design, not yet built.** Nothing described here exists as working code yet, but it isn't
a new idea either — `contactperson`'s existing `W01`-`W06` type markers (Actor/Director/Composer/
Artist/Arranger/Performer, see below) were seeded specifically to support this, ahead of anything
using them. Read this alongside `MANUAL.md` (the package's current, already-live schema) and
`liberty/MANUAL.md` (the xref machinery this design builds on with no new schema of its own).

## The problem this solves

Media packages that credit a person currently store that credit as a bare text string — fisheye's
own `fisheyealbum` content type has `artist`/`composer`/`conductor`/`orchestra`/`performer` xref
items, all `template='text'`, storing a name directly in `xkey_ext`. That means the same real
person (say, a conductor who appears on dozens of unrelated albums) has no single identity anywhere
in the system — no shared bio, no shared external links (MusicBrainz/Discogs/Wikidata), and no way
to ask "what does this system know about, or featuring, this person" as one query. Film/TV cast and
crew credits, whenever built, would hit the identical limitation independently unless designed
against a shared answer from the start.

## Core idea

**Contact is the master record.** Any named person or ensemble referenced by any media package —
music artist/composer/conductor/orchestra/performer today, film/TV actor/director/writer as a
planned retrofit — gets (or reuses) exactly one `Contact` (`ContactPerson` for an individual,
`ContactBusiness` for a group/orchestra/ensemble). Everything else — a discography gallery, a film
credit, a bio, external links — hangs off that one record. The Contact is durable and
package-agnostic; media objects come and go around it.

Two things do **not** get merged into one object:

- **A Contact is not a Gallery, and a Gallery is not a Contact.** `content_type_guid` is a single,
  exclusive value per `liberty_content` row — it drives `getLibertyObject()`'s dispatch,
  permission checks, and `liberty_xref_item` scoping (item names are already joined against
  `content_type_guid` specifically because the same item name means different things on different
  content types). A hybrid row that's simultaneously `fisheyegallery` and `contact` isn't a
  simplification, it's structurally incompatible with how the rest of Liberty already works — and
  it would trap a person's bio/external-links inside fisheye specifically, unreachable from a
  completely different package's own credit xrefs. That defeats the entire point.
- **Not every Contact needs a Gallery.** A discography gallery only exists when there's a literal
  `Music/<Artist>/` folder on disk backing it. An ensemble credited as a performer on dozens of
  other artists' albums but with no folder of its own is a perfectly valid Contact with no gallery
  link at all — that isn't a gap to fill later, it's the expected shape for that kind of entity.

## Existing seed: the `W01`-`W06` role markers

`contactperson`'s `type` xref group already carries a set of role markers, at `sort_order=0`
(Liberty's toggleable-multi-tag convention — see `MANUAL.md`'s own `P01`/`P02` for the pattern),
distinct from the personal/business-capacity `P01`/`P02` markers:

| Item | Role |
|---|---|
| `W01` | Actor |
| `W02` | Director |
| `W03` | Composer |
| `W04` | Artist |
| `W05` | Arranger |
| `W06` | Performer |

These exist purely as reference-item definitions today — no real Contact has been tagged with any
of them yet. They're what "what kind of media-credited person is this" was always meant to answer;
this design is what actually puts them to use, rather than a fresh proposal for new type codes.

## The linking mechanism — already exists, no new liberty schema needed

`liberty_xref` has a dedicated `xref` column (`BIGINT`), entirely separate from the string-value
`xkey`/`xkey_ext` columns — it exists specifically so one xref row can point at *another*
`content_id` instead of storing a literal value. This isn't a new idea: `stock`'s own supplier
link already uses exactly this shape —

- `stockcomponent`'s `sup` xref item stores the Contact's `content_id` in `xref`
- `stock/templates/xref/stockcomponent/view_sup_item.tpl` renders it as:
  `<a href="{$smarty.const.CONTACT_PKG_URL}view.php?content_id={$xrefInfo.xref}">{$xrefInfo.linked_title}</a>`

Every "this links to a person" xref item this design adds reuses that same reference-style
template shape — never `template='text'`.

## Structure once built

- **Contact → discography gallery** (optional): a `music_gallery` xref item, `content_type_guid`
  scoped to `contact` (the shared base level `MANUAL.md` already registers SCREF/addresses at, not
  `contactperson`/`contactbusiness` specifically — a linked identity could be either), `xref` = the
  gallery's `content_id`. Set only when a real folder-backed gallery exists for that Contact.
- **Media credit → Contact** (the actual workhorse link): each work's own credit xref — today
  `fisheyealbum`'s `artist`/`composer`/`conductor`/`orchestra`/`performer`, later a
  `fisheyefilm`/`fisheyeprogram` actor/director/writer equivalent — stores `xref` = the Contact's
  `content_id` directly. **Not routed through the gallery** — a credit on a Film should never need
  to know whether that person happens to also have a Music gallery.
- **Bio/description**: plain `lc.data` on the Contact record itself. No new xref needed — this is
  already Contact's own free-text "note" field, already shown by `display_contact.tpl` above the
  address block.
- **New `contact:external` xref group** (`x_group='external'`, `content_type_guid='contact'`):
  outbound identity links — `musicbrainz`, `theaudiodb`, `discogs_artist`, `wikidata`,
  `official_site` — each an `href`-template item (already a live template value in this DB) with
  `cross_ref_href` set. This mirrors `fisheyealbum`'s own existing `mbid`/Discogs-style href items,
  just promoted from per-album duplication up to the one shared identity record.

## "Everything by/featuring this Contact" — falls out for free

No new mechanism needed for this — it's an inherent reverse query once credits store `xref`
instead of a string:

```sql
SELECT lx.content_id, lx.item, lc.title, lc.content_type_guid
FROM liberty_xref lx
INNER JOIN liberty_content lc ON lc.content_id = lx.content_id
WHERE lx.xref = ?   -- the Contact's content_id
```

This naturally spans every content type at once — an ensemble's own "appears on" listing is just
this query, with no gallery, no per-package plumbing, and no special-casing for the
no-gallery-of-their-own case.

## External sources — what each actually provides

Contact serves every media type, so this splits into music sources and film/TV sources, plus one
source that's genuinely medium-agnostic. The shape of the problem is the same in both halves:
the "official" metadata database for the medium gives structured identity but is thin or absent on
actual prose biography, so a real bio needs a second, community-run source keyed off the same id.

**Music** — MusicBrainz gives structured identity but **no prose biography** at all, a deliberate MB
project policy, not a gap in the API:

| Source | What it gives | Notes |
|---|---|---|
| **MusicBrainz Artist** (`/ws/2/artist/<mbid>`) | `name`, `sort-name`, `disambiguation`, `type` (Person/Group/Orchestra/Choir/...), `gender`, `country`/`area`, `begin-area`/`end-area`, `life-span`, `aliases[]`, `ipis[]`/`isnis[]`, `tags[]`/`genres[]`, `rating`; via `inc=url-rels`: links to Wikidata, Discogs, official homepage, social accounts, IMDb, etc. | No biography field. The MBID is already captured today via `FISHEYEALBUM_COMMON_TAG_MAP`'s `MUSICBRAINZ_ALBUMID` handling, so it's the natural join key for everything below. |
| **TheAudioDB** (`theaudiodb.com/api/v1/json/2/artist-mb.php?i=<mbid>`) | `strBiographyEN` (+ other languages), formed year, genre/style/mood, thumb/fanart/banner images | Keyed directly off the MBID — a single hop, no name-matching ambiguity. Likely the closest match to what Plex's own music agent shows as "About the Artist". Best first choice for real bio prose. |
| **Discogs artist profile** | `profile` field — prose bio | `FisheyeAlbum::fetchDiscogsLink()` already exists for album-level Discogs data, so some of this plumbing is reusable. Good fallback when TheAudioDB has nothing for an artist. |

**Film/TV** — the same shape again, and closer to being ready than it looks: `imdb`/`tmdb`/`tvdb`
are already captured today as plain external-link xref items on `fisheyefilm`/`fisheyeprogram`
(pulled straight from Plex's own metadata GUIDs, `<Guid id="imdb://...">`/`<Guid id="tmdb://...">`)
— currently just stored as link IDs, never used to actually fetch person data, exactly like
music's own `mbid`/`discogs` items before this design.

| Source | What it gives | Notes |
|---|---|---|
| **TMDb** (`/3/person/<tmdb_id>`) | `biography` (real prose — TMDb, unlike MusicBrainz, does host bios directly), `birthday`/`deathday`, `place_of_birth`, `also_known_as[]` (aliases), `profile_path` (photo), `known_for_department`, `gender`, `popularity`; `append_to_response=external_ids` in the same call returns `imdb_id`/`tvdb_id`/`wikidata_id`/`instagram_id`/`twitter_id`/`facebook_id` for free. | Free, actively maintained, generous rate limits — the modern, practical first choice for a real bio, and the natural next step here since the `tmdb` id is already being captured, just not fetched from yet. |
| **TheTVDB** (`/v4/people/<tvdb_id>`) | Name, image, birth date/place, some biography text via translations | Its own person endpoint exists but is comparatively sparse on biography compared to TMDb — TVDB is much stronger on show/season/episode metadata than on cast/crew prose. Useful as a fallback or for a person TMDb hasn't matched, not a first choice. |
| **IMDb** | — | **No accessible API for actual data** — IMDb's own data is proprietary; programmatic access is a paid/licensed commercial product ("Essential Metadata"), not something a self-hosted app integrates directly. The `imdb` xref item should stay exactly what it is today: an outbound link people can click, never a fetchable data source. |

**Medium-agnostic fallback** — Wikidata → Wikipedia works identically for a musician, an actor, or
a director, since it doesn't care which domain-specific database first pointed at it:

| Source | What it gives | Notes |
|---|---|---|
| **Wikidata → Wikipedia** (via a `url-rels`-style Wikidata link from MusicBrainz *or* TMDb's `external_ids`, then Wikipedia's REST summary endpoint) | Lead-paragraph extract | Works, keyless, for any person regardless of medium — but a longer chain (two hops) with less control over tone/length than a purpose-built bio field. Fallback of last resort in both halves above. |

## Open question: bands and ensembles as `ContactBusiness`

`ContactBusiness` is the natural fit for a band/orchestra/ensemble conceptually — it's a group, not
a person. But the role markers above (`W01`-`W06`) currently live only under `contactperson`'s own
`type` group, and a band isn't just "a person with a group label": it's made of individual persons
whose own membership changes over time, and any one of those persons may belong to several
different bands across different periods. Modelling that properly is a real design task on its
own, not something to fold into the person/gallery/credit linking above without thinking it
through — deliberately left open rather than guessed at here.

The one piece already in place for it: `liberty_xref` already carries `start_date`/`end_date` on
every row, so a person↔band membership xref (whichever direction it ends up living on) already has
a mechanism for "member from X to Y" built in — expanding the role-marker scheme to cover this
doesn't need new schema, just a proper pass at the actual xref shape once it's discussed.

## Migration/retrofit shape

- Existing plain-text `artist`/`composer`/etc. values need a one-time pass: find-or-create a
  Contact per unique name, then rewrite the affected `liberty_xref` rows to set `xref` (Contact's
  `content_id`) instead of `xkey_ext` (the name string).
- New reference-style `liberty_xref_item` definitions are needed per credit role per content type
  (mirroring `sup`'s own definition shape), replacing today's `template='text'` rows for
  `fisheyealbum`'s `artist`/`composer`/`conductor`/`orchestra`/`performer`.
- No live music data is committed to the old model yet, so there's no need to migrate existing test
  loads under the string-based scheme first — building the new shape directly is the simpler path.
- Film/TV retrofit is deliberately deferred until this pattern is proven on the music side — it
  will reuse the identical Contact-linking shape once actor/director/writer-style credit xrefs
  exist for `fisheyefilm`/`fisheyeprogram`.

## Build plan

1. **Find-or-create-Contact-by-name helper** — used both when the artist-folder scan creates a
   discography gallery, and when registering any media credit, so both call sites converge on the
   same Contact rather than each minting their own. Same dedup shape as
   `FisheyeGallery::findOrCreateNestedGallery()` already uses (scoped lookup, not a bare name
   match).
2. New `liberty_xref_item` definitions: Contact's own `music_gallery` reference item and
   `contact:external` href-style items; `fisheyealbum`'s `artist`/`composer`/`conductor`/
   `orchestra`/`performer` converted from `template='text'` to the new reference template.
3. New `view_xxx_item.tpl` templates for the reference-style items (mirroring stock's
   `view_sup_item.tpl`), hyperlinking into `contact/view.php?content_id=`.
4. Bio-fetch integration: TheAudioDB (keyed by the MBID already captured) as the primary source,
   Discogs artist profile as fallback.
