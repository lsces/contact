{strip}
<div class="form-group">
	{formlabel label="`$xrefInfo.template_title` Person" for="`$output`person_search"}
	{forminput}
		<input type="hidden" name="{$output}xref" id="{$output}xref" value="{$xrefInfo.xref|escape}" />
		<div style="position:relative">
			<input type="text" class="form-control" id="{$output}person_search"
				autocomplete="off" placeholder="{tr}Type to search contacts…{/tr}" />
			<ul id="{$output}person_dropdown" class="dropdown-menu"
				style="display:none;position:absolute;width:100%;z-index:1000;max-height:220px;overflow-y:auto"></ul>
		</div>
		{formhelp note="Existing contact person to link to this record."}
	{/forminput}
	<div class="clear"></div>
</div>
{/strip}
<script>
(function($) {
	var timer;
	var $input  = $('#{$output}person_search');
	var $hidden = $('#{$output}xref');
	var $dd     = $('#{$output}person_dropdown');

	$input.on('input', function() {
		var q = $(this).val();
		clearTimeout(timer);
		$dd.hide().empty();
		$hidden.val('');
		if (q.length < 2) return;
		timer = setTimeout(function() {
			$.getJSON('{$contactLookupUrl}', {ldelim}q: q, type: 'contactperson'{rdelim}, function(data) {
				if (!data.length) return;
				$.each(data, function(i, row) {
					var label = row.title + (row.scref ? ' (' + row.scref + ')' : '');
					$dd.append($('<li>').append(
						$('<a>').attr('href','#').data('id', row.content_id).data('label', label).text(label)
					));
				});
				$dd.show();
			});
		}, 250);
	});

	$(document).on('mousedown', '#{$output}person_dropdown a', function(e) {
		e.preventDefault();
		$input.val($(this).data('label'));
		$hidden.val($(this).data('id'));
		$dd.hide().empty();
	});

	$input.on('blur', function() { setTimeout(function() { $dd.hide(); }, 150); });

	$input.on('keydown', function(e) {
		if (!$dd.is(':visible')) return;
		var $links = $dd.find('a'), idx = $links.index($dd.find('li.active a'));
		if (e.key === 'ArrowDown') { e.preventDefault(); $links.parent().removeClass('active'); $links.eq(idx + 1 < $links.length ? idx + 1 : 0).parent().addClass('active'); }
		else if (e.key === 'ArrowUp') { e.preventDefault(); $links.parent().removeClass('active'); $links.eq(idx > 0 ? idx - 1 : $links.length - 1).parent().addClass('active'); }
		else if (e.key === 'Enter') { var $a = $dd.find('li.active a'); if ($a.length) { e.preventDefault(); $a.trigger('mousedown'); } }
		else if (e.key === 'Escape') { $dd.hide(); }
	});
}(jQuery));
</script>
