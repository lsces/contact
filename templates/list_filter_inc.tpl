{* Contact list type filter - included inside a list page's own search form (list_contacts.php,
   contactwiki's list_wiki.php). One row of type tags per contact class, no class labels; each row's
   leading "All" box ticks or clears every type in that row (individual types stay toggleable
   afterwards) and shows ticked when they all are - it submits nothing itself. Nothing ticked =
   everything listed. See Contact::getListFilterOptions()/applyListFilter(); expects $filterOptions
   with checked flags already set. *}
{strip}
{if $filterOptions}
	<div class="contact-list-filter contact-filter-rows col-xs-12">
		{foreach from=$filterOptions item=class}
			{if $class.types}
				<div class="contact-filter-class" data-filter-row="{$class.guid|escape}">
					<label class="contact-filter-all">
						<input type="checkbox" class="contact-filter-all-box" />
						&nbsp;<strong>{tr}All{/tr}</strong>
					</label>
					{foreach from=$class.types item=type}
						&nbsp;&nbsp;<label class="contact-filter-type">
							<input type="checkbox" name="xref_items[]" value="{$type.item|escape}" class="contact-filter-type-box" {if $type.checked}checked="checked"{/if} />
							&nbsp;{$type.name|escape}
						</label>
					{/foreach}
				</div>
			{/if}
		{/foreach}
	</div>
	<script>
	/* "All" ticks/clears every type in its row; it shows ticked whenever they all are. Block comments only here - Smarty strip may join these lines. */
	document.querySelectorAll( '.contact-filter-rows [data-filter-row]' ).forEach( function( row ) {
		var all = row.querySelector( '.contact-filter-all-box' );
		var types = Array.prototype.slice.call( row.querySelectorAll( '.contact-filter-type-box' ) );
		var sync = function() { all.checked = types.length > 0 && types.every( function( t ) { return t.checked; } ); };
		all.addEventListener( 'change', function() {
			types.forEach( function( t ) { t.checked = all.checked; } );
		} );
		types.forEach( function( t ) { t.addEventListener( 'change', sync ); } );
		sync();
	} );
	</script>
{/if}
{/strip}
