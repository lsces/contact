{* Every artist/composer gallery under Music/ - already-linked ones shown for information, not-yet-
   linked ones with a checkbox where a clean MusicBrainz->Wikidata match was actually found. See
   load_wiki_artists.php's own docblock. *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin liberty">
	<div class="header">
		<h1>{tr}Load Wiki Artist Contacts{/tr}</h1>
	</div>

	<div class="body">

		{if $result}
			{if $result.created}
				<div class="alert alert-success">
					<p>{tr}Contacts created{/tr}:</p>
					<ul>
						{foreach from=$result.created item=row}
							<li>{$row.title|escape} - <a href="{$smarty.const.CONTACT_PKG_URL}view.php?content_id={$row.content_id}">{tr}View{/tr}</a></li>
						{/foreach}
					</ul>
				</div>
			{/if}
			{if $result.errors}
				<div class="alert alert-danger">
					<p>{tr}Failed{/tr}:</p>
					<ul>{foreach from=$result.errors item=row}<li>{$row.error|escape}</li>{/foreach}</ul>
				</div>
			{/if}
		{/if}

		{if $candidates}
			{form legend="" action="{$smarty.const.CONTACT_PKG_URL}load_wiki_artists.php"}
				<p>{tr}Showing up to{/tr} {$candidateLimit} {tr}artist/composer galleries under Music/{/tr}:</p>
				<table class="table table-condensed">
					<thead>
						<tr>
							<th></th>
							<th>{tr}Gallery{/tr}</th>
							<th>{tr}Status{/tr}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$candidates item=candidate}
							<tr>
								<td>
									{if $candidate.existing_contact}
										&nbsp;
									{elseif $candidate.wikidata_qid}
										<input type="checkbox" name="selected[]" value="{$candidate.gallery_content_id}" checked="checked" />
										<input type="hidden" name="qid_{$candidate.gallery_content_id}" value="{$candidate.wikidata_qid|escape}" />
										<input type="hidden" name="mbtype_{$candidate.gallery_content_id}" value="{$candidate.mb_match.type|escape}" />
									{else}
										&nbsp;
									{/if}
								</td>
								<td>{$candidate.title|escape}</td>
								<td>
									{if $candidate.existing_contact}
										<a href="{$smarty.const.CONTACT_PKG_URL}view.php?content_id={$candidate.existing_contact.content_id}">{tr}Already linked to{/tr}: {$candidate.existing_contact.title|escape}</a>
									{elseif $candidate.wikidata_qid}
										{tr}Match found{/tr}: {$candidate.mb_match.name|escape} ({$candidate.mb_match.type|escape}, {$candidate.wikidata_qid|escape})
									{else}
										<span class="text-muted">{$candidate.note|escape}</span>
									{/if}
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				<input type="submit" class="btn btn-primary" name="fCreate" value="{tr}Create Selected Contacts{/tr}" />
			{/form}
		{else}
			<p>{tr}No artist/composer galleries found under Music/.{/tr}</p>
		{/if}

	</div>
</div>
{/strip}
