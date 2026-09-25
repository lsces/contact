# Contact as a Universal Person/Entity Hub — Design

**Status: design, not yet built.** Nothing described here exists as working code yet, but it isn't
a new idea either — `contactperson`'s existing `WP01`-`WP06` type markers (Actor/Director/Composer/
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

## Existing seed: the `WPxx`/`WBxx` role markers

`contactperson`'s `type` xref group carries a set of person-role markers, at `sort_order=0`
(Liberty's toggleable-multi-tag convention — see `MANUAL.md`'s own `P01`/`P02` for the pattern),
distinct from the personal/business-capacity `P01`/`P02` markers. `contactbusiness` carries the
equivalent group/ensemble-role markers:

| Item | Role |
|---|---|
| `WP01` | Actor |
| `WP02` | Director |
| `WP03` | Composer |
| `WP04` | Artist |
| `WP05` | Arranger |
| `WP06` | Performer |

| Item | Role |
|---|---|
| `WB01` | Band |
| `WB02` | Orchestra |
| `WB03` | Choir |
| `WB04` | Ensemble |
| `WB05` | Production Company |
| `WB06` | Record Label |

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

Contact serves every media type, so this splits by medium — music, film/TV, books/authors — plus
one source that's genuinely medium-agnostic. The shape of the problem repeats in each block: the
"official" metadata database for the medium gives structured identity but is thin or absent on
actual prose biography, so a real bio needs a second, often community-run source keyed off the
same id. It also repeats sideways: a person can legitimately belong to more than one block at once
(a composer scoring films, an author whose novel gets adapted) — `contact:external` items coexist
on one Contact rather than forcing a single source per person.

**Music** — MusicBrainz gives structured identity but **no prose biography** at all, a deliberate MB
project policy, not a gap in the API:

| Source | What it gives | Notes |
|---|---|---|
| **MusicBrainz Artist** (`/ws/2/artist/<mbid>`) | `name`, `sort-name`, `disambiguation`, `type` (Person/Group/Orchestra/Choir/...), `gender`, `country`/`area`, `begin-area`/`end-area`, `life-span`, `aliases[]`, `ipis[]`/`isnis[]`, `tags[]`/`genres[]`, `rating`; via `inc=url-rels`: links to Wikidata, Discogs, official homepage, social accounts, IMDb, etc. | No biography field. The MBID is already captured today via `FISHEYEALBUM_COMMON_TAG_MAP`'s `MUSICBRAINZ_ALBUMID` handling, so it's the natural join key for everything below. |
| **TheAudioDB** (`theaudiodb.com/api/v1/json/2/artist-mb.php?i=<mbid>`) | `strBiographyEN` (+ other languages), formed year, genre/style/mood, thumb/fanart/banner images | Keyed directly off the MBID — a single hop, no name-matching ambiguity. Likely the closest match to what Plex's own music agent shows as "About the Artist". Best first choice for real bio prose. |
| **Discogs artist profile** | `profile` field — prose bio | `FisheyeAlbum::fetchDiscogsLink()` already exists for album-level Discogs data, so some of this plumbing is reusable. Good fallback when TheAudioDB has nothing for an artist. |

**TMDb does not generally cover the music space** — its own person catalogue is scoped to people
with an actual film/TV credit, not musicians broadly. A working artist with no documentary, concert
film, biopic, or scoring credit simply has no TMDb entry at all, so it can't stand in for
MusicBrainz/TheAudioDB/Discogs as a general music source. The overlap is real but narrower than it
looks: a composer who also scores films, or an artist who's the subject of a concert film or
documentary, genuinely does get a TMDb person page — for exactly that person, a Contact can (and
should) carry *both* an `mbid` and a `tmdb_id` at once, since these are separate `contact:external`
items on the same record, not a choice between one or the other. TMDb is worth checking as a
supplementary link whenever that overlap exists, just not assumed as a first-choice music source.

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

**Books/Authors** — the fourth media block, not yet built out at all (no `fisheyebook`-equivalent
content type exists today). The same author-also-writes-for-film/TV overlap as composers/musicians
applies here too — a novelist credited as "story by"/"based on characters created by" on a TMDb
crew list is a real, if imperfect, case for the same Contact carrying both an `openlibrary_id` and
a `tmdb_id`:

| Source | What it gives | Notes |
|---|---|---|
| **Open Library** (`openlibrary.org/authors/<id>.json`) | `name`, `bio` (often itself Wikipedia-sourced but presented as clean structured text), `birth_date`/`death_date`, `alternate_names[]`, `photos[]` (via Internet Archive's cover service), `links[]` (including a Wikipedia URL when known); `/authors/<id>/works.json` gives the author's own bibliography for free. | Free, keyless, run by the Internet Archive — the closest thing to "MusicBrainz for books" in spirit, and the practical first choice here. Bonus: bibliography data comes from the same source, no separate lookup needed. |
| **VIAF** (Virtual International Authority File) | Aggregated library-catalogue authority records (Library of Congress, British Library, etc.) — confirms which "John Smith" is meant, links out to national library IDs | Not a bio source itself, but the disambiguation-of-identity role MBIDs play for musicians — worth using to confirm a match before trusting a bio fetched elsewhere, not for the bio text itself. |
| **AbeBooks** | Decent author summary text on their own bookshop site | No public API found for this — their author pages are presentation-only on-site content, not something to integrate against the way Open Library's actual JSON API can be. Fine as a manual reference link, not a fetch source. |
| **Goodreads** | (historically: bio, ratings, "similar authors") | Its public API was deprecated years ago (Amazon-owned) and isn't open to new integrations any more — despite being the name most people think of first, not a realistic modern choice. |

**Medium-agnostic fallback** — Wikidata → Wikipedia works identically for a musician, an actor, a
director, or an author, since it doesn't care which domain-specific database first pointed at it —
for authors specifically this hop is often the *more* reliable base, not just a fallback, since
well-known authors tend to have well-curated Wikipedia biographies:

| Source | What it gives | Notes |
|---|---|---|
| **Wikidata → Wikipedia** (via a `url-rels`-style Wikidata link from MusicBrainz, TMDb's `external_ids`, or Open Library's own `links[]`, then Wikipedia's REST summary endpoint) | Lead-paragraph extract | Works, keyless, for any person regardless of medium — a longer chain (two hops) with less control over tone/length than a purpose-built bio field, but for authors it's less of a fallback and more a first-choice-equivalent to Open Library's own `bio` field, which is itself often just Wikipedia text anyway. |

## Open question: band/ensemble membership over time

`ContactBusiness` is the natural fit for a band/orchestra/ensemble conceptually — it's a group, not
a person — and now has its own `WBxx` role markers (above) alongside `contactperson`'s `WPxx`. What
that split doesn't touch: a band isn't just "a `ContactBusiness` with a group label", it's made of
individual persons whose own membership changes over time, and any one of those persons may belong
to several different bands across different periods. Modelling *that* — the actual person↔band
membership relationship — is a real design task on its own, not something to fold into the
person/gallery/credit linking above without thinking it through — deliberately left open rather
than guessed at here.

The one piece already in place for it: `liberty_xref` already carries `start_date`/`end_date` on
every row, so a person↔band membership xref (whichever direction it ends up living on) already has
a mechanism for "member from X to Y" built in — this doesn't need new schema, just a proper pass at
the actual xref shape once it's discussed.

**Deferred idea, not decided**: collapsing `WPxx`/`WBxx`'s separate xref rows into one packed flag
value — see `liberty/MANUAL.md`'s "Type-marker convention" section, its correct home since it's a
generic Liberty idea, not Contact-specific.

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

0. **`ContactWikiIndividual`/`ContactWikiGroup` classes** — both done.
   `ContactWikiIndividual extends ContactPerson` (`contact/includes/classes/
   ContactWikiIndividual.php`), `content_type_guid='contactwikiindi'`; `ContactWikiGroup extends
   ContactBusiness` (`ContactWikiGroup.php`), `content_type_guid='contactwikigroup'` - a group's own
   name is plain `organisation`/`liberty_content.title`, no NAME-xref name-parts mechanism needed.
   Each overrides `storeXref()` to mirror its own editable date xref (`dob` for an individual,
   `formed` for a group) into `liberty_content.event_time` whenever it's written (covers both a
   fresh add and a later edit through the normal `edit_xref.php` flow, since `upsertXref()`
   delegates to this same `storeXref()` call). `add_wiki_person.php`/`add_wiki_group.php` instantiate
   these instead of plain `ContactPerson`/`ContactBusiness` - no `P01`/type injection, since these
   are genuinely separate content types, not a tagged Personal/generic business.

   Every Wikidata fetch/apply helper (entity fetch, claim extraction, TMDb biography, Commons image
   download, `reloadFromWikidata()` itself) lives on `ContactWikiTrait` (`ContactWikiTrait.php`),
   shared by both classes - a trait rather than a shared base class since the two already have
   divergent real parents (`ContactPerson` vs `ContactBusiness`, no multiple inheritance in PHP).
   `reloadFromWikidata( ?string $pQid = null )` runs the full fetch-then-apply cascade (raw entity
   json, external ids, this content type's own biography dates via `biographyDateProps()` -
   dob/dod vs formed/disbanded, P18 image) and is shared by each add-flow's own initial Save and
   `edit.php`'s fisheye-style `fReloadWikidata` button (`edit.tpl`, `.btn-secondary`, same
   convention as `edit_album.tpl`'s Reload Images/Tracks) - the latter re-fetches using
   `getWikidataQid()`, the contact's already-stored `wikidata` xref. Role-tag suggestions stay
   per-class (`OCCUPATION_MAP` from P106 for an individual, `GROUP_TYPE_MAP` from P31 for a group) -
   `GROUP_TYPE_MAP` currently has just the one live-confirmed entry (`Q215380` "musical group" ->
   `WB01`, confirmed against Fleetwood Mac/`Q106648`); the rest of WBxx's own Q-ids aren't guessed,
   same "curated, not exhaustive" reasoning as `OCCUPATION_MAP`.

   `view_wiki_profile.tpl` (the shared profile-style `view.php` layout for both wiki content types)
   reads its biography/external-link panels straight from `$gXrefInfo->mGroups` rather than
   pre-flattened PHP variables - a new item added to either group later just appears, no template
   change needed.

   **Not yet built**: linking a group's own Wikidata "member"-style claims to create/link the
   individual members' own Contact records - flagged as something that "may come out in the wash"
   once a real group's data is fetched and looked at properly, not designed yet.
1. **Find-or-create-Contact-by-name helper** — used both when the artist-folder scan creates a
   discography gallery, and when registering any media credit, so both call sites converge on the
   same Contact rather than each minting their own. Same dedup shape as
   `FisheyeGallery::findOrCreateNestedGallery()` already uses (scoped lookup, not a bare name
   match).
2. New `liberty_xref_item` definitions: Contact's own `music_gallery` reference item (not yet
   done) and `contact:external` href-style items (done — `rdmcloud`'s own
   `config/local/xref_schemes/contact.php`); `fisheyealbum`'s `artist`/`composer`/`conductor`/
   `orchestra`/`performer` converted from `template='text'` to the new reference template (not yet
   done).
3. New `view_xxx_item.tpl` templates for the reference-style items (mirroring stock's
   `view_sup_item.tpl`), hyperlinking into `contact/view.php?content_id=`.
4. Bio/identity-fetch integration: **Wikidata as the entry point, not just one source among
   several** — `action=wbgetentities`, or simpler, a plain GET against
   `wikidata.org/wiki/Special:EntityData/<Qid>.json` (no API key), returns every property
   (`Pnnnn`) on that person's entity at once. Property→item mapping, hard-confirmed against a real
   entity (Olivia Newton-John, `Q185165`) by cross-matching values against ids already confirmed
   by hand, not guessed from memory:

   | xref item | Wikidata property | Confirmed how |
   |---|---|---|
   | `imdb` | `P345` | value matched the hand-confirmed id exactly |
   | `tmdb` | `P4985` | value matched the hand-confirmed id exactly |
   | `tvdb` | `P7920` | value matched the hand-confirmed id exactly |
   | `musicbrainz` | `P434` | value is a valid MBID-shaped UUID |
   | `viaf` | `P214` | value is VIAF-shaped |
   | `openlibrary` | `P648` | value carries Open Library's own author-id `...A` suffix |
   | `official_site` | `P856` | value is a real URL |
   | `discogs_artist` | `P1953` | confirmed against a real Discogs page |

   One Wikidata fetch populates most of `contact:external` in a single call rather than searching
   each source individually. The one confirmed gap: **TheAudioDB has no Wikidata property at all**
   (checked directly, not present on this entity) — its own item still needs a separate step, but
   not its own search, since its API takes the MusicBrainz id directly (already populated via
   Wikidata) rather than a TheAudioDB-specific id. `tmdb`/`tvdb`/`imdb` all only need the bare id in
   `xkey` — confirmed against real pages for all three, the trailing name slug each site shows is
   cosmetic. Open Library's own page loaded fine by hand but is bot-gated against a plain fetch -
   won't be scriptable the same way the id-based sources above are, worth knowing before assuming
   every source here is equally automatable.

   **What actually answers "what populates the bio" — the real open question this whole design
   started from**: not a separate lookup at all. The same entity JSON carries a top-level
   `sitelinks` object (distinct from `claims`, easy to miss when only scanning for `Pnnnn`
   properties - confirmed: `sitelinks.enwiki` gave `{title: "Olivia Newton-John", url:
   "https://en.wikipedia.org/wiki/Olivia_Newton-John"}` directly, no search needed). That title
   feeds straight into Wikipedia's own REST summary endpoint (`en.wikipedia.org/api/rest_v1/page/
   summary/<title>`) for a lead-paragraph extract.

   **Settled, superseding an earlier draft of this section**: Wikipedia is now the *only* biography
   source (`ContactWikiTrait::fetchWikipediaSummary()`), not TMDb - TMDb's own `biography` field is
   person/film-cast only (meaningless for a `ContactWikiGroup`), and TheAudioDB, the other candidate
   floated early on, turned out to have a dead free API (confirmed live 2026-09-25: its old public
   test key 404s on well-known artists). `fetchTmdbBiography()` is kept on the trait but no longer
   called from `reloadFromWikidata()` - flagged for a later film/TV credit use, where TMDb's own
   person bios genuinely are the better/more detailed source, unlike here.

   **Finding the Wikidata id without searching for it**: MusicBrainz's own *artist*-level entity
   (not a release/album - `FisheyeAlbum::fetchDiscogsLink()`'s existing MusicBrainz lookup only ever
   queries the release endpoint, which has no reason to carry this) commonly carries a `wikidata`
   url-rel pointing straight at the artist's own Wikidata item - confirmed live against Fleetwood
   Mac's own MusicBrainz artist id, resolving to exactly `Q106648`. Both add-flows' own "Wikidata ID"
   field now also accepts a bare MusicBrainz artist id/URL (`ContactWikiTrait::
   extractMusicBrainzArtistId()`/`resolveWikidataQidFromMusicBrainzArtist()`) - the common case for a
   group, whose MusicBrainz artist id is usually already known from the album tags fisheye scanned
   in, without a separate manual Wikidata search at all.

   **Decision**: cache the whole raw Wikidata entity JSON on its own `wikidata` item's `data` field
   (a `data`-holding item, not just a bare id-in-`xkey` href like the others), so a property nobody
   thought to map yet can be mined later from what's already stored rather than re-fetching.

   **New gap surfaced, not yet built**: birth/death details (DOB/POB/DOD/POD) have nowhere to live
   yet. DOB is a good fit for `liberty_content.event_time` - a real, already-existing generic
   column (not an xref at all), the same one Calendar's own day content already sorts by
   (`event_time_asc`/`_desc`) - reusing it gives free sort/list support, no new schema. It only
   holds one date though, so POB/DOD/POD still need a home - most likely a new `details` item with
   a small JSON blob (same `json-list`-style shape as Track's own `disc`/`track` data), not
   designed yet.
