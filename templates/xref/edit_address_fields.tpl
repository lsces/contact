{* Fields for an 'address' item: the shared JSON form. The item's definition data (liberty_xref_item.data) names
   the parts (address, town, county, postcode, country, notes), mirrors the postcode to xkey and the address as
   one line to xkey_ext on save - so the list, export and postcode lookup keep reading the plain columns - and
   starts a row that has no JSON yet from those columns. *}
{include file="bitpackage:liberty/xref/edit_json-list_fields.tpl"}
