{* Tidies a phone number typed or pasted into the box whose id is "{$xid}xkey": every dash and space is removed, so
   0345-677-9866 and "0345 677 9866 " both become 03456779866. It runs when the box is left, just after a paste,
   and on submit. It is a form convenience only: the store keeps whatever it is given, so imports and other code
   can still pass a number exactly as they have it. *}
<script>
(function() {
	var el = document.getElementById('{$xid}xkey');
	if (!el) { return; }
	function tidy() {
		el.value = el.value.replace(/[\s\u00a0\u2010-\u2015\u2212-]+/g, '');
	}
	el.addEventListener('change', tidy);
	el.addEventListener('paste', function() { setTimeout(tidy, 0); });
	if (el.form) { el.form.addEventListener('submit', tidy); }
})();
</script>
