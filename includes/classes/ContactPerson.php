<?php
/**
 * Person contact — extends Contact with content_type_guid='contactperson'.
 *
 * Name storage mirrors ContactBusiness: liberty_content.title is the single
 * source of truth (pipe-encoded prefix|forename|surname|suffix here, rather than
 * a plain organisation string), not a separate xref row — P01 is a pure type
 * marker only (see Contact::store()), it carries no data.
 *
 * Person-specific xref items are registered at the 'contactperson' level; shared
 * contact fields (addresses, SCREF etc.) live at the 'contact' package level and
 * are picked up via the dual-guid xref pattern.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

class ContactPerson extends Contact {

	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct( $pContactId, $pContentId );
		$this->mContentTypeGuid = CONTACTPERSON_CONTENT_TYPE_GUID;
		$this->registerContentType( CONTACTPERSON_CONTENT_TYPE_GUID, [
			'content_type_guid' => CONTACTPERSON_CONTENT_TYPE_GUID,
			'content_name'      => 'Person Contact',
			'handler_class'     => 'ContactPerson',
			'handler_package'   => 'contact',
			'handler_file'      => 'ContactPerson.php',
			'maintainer_url'    => 'http://lsces.co.uk',
		] );
		// mPackageGuid='contact' is set automatically by registerContentType()
		// because handler_package('contact') != content_type_guid('contactperson').
	}

	/**
	 * Reassemble a pipe-encoded prefix|forename|surname|suffix string into a
	 * plain reading-order display name — "Prefix Forename Surname Suffix".
	 * Shared by getTitle() (object already loaded) and any raw-row caller that
	 * only has the stored title string (e.g. list_contacts.php/list_people.php's
	 * getList() rows, which are plain arrays, not ContactPerson instances).
	 */
	public static function formatDisplayName( string $pRawTitle ): string {
		$parts = explode( '|', $pRawTitle );
		$name = trim( $parts[0] ?? '' );
		$name = trim( $name.' '.( $parts[1] ?? '' ) );
		$name = trim( $name.' '.( $parts[2] ?? '' ) );
		$name = trim( $name.' '.( $parts[3] ?? '' ) );
		return $name;
	}

	/**
	 * Reassemble a pipe-encoded title into "Surname, Prefix Forename" — the
	 * sort/list-friendly form used by list_contacts.php/list_people.php so
	 * contacts still group and sort by surname, not by raw title or prefix.
	 * Suffix isn't included, matching the format this replaces exactly.
	 */
	public static function formatListName( string $pRawTitle ): string {
		$parts    = explode( '|', $pRawTitle );
		$prefix   = $parts[0] ?? '';
		$forename = $parts[1] ?? '';
		$surname  = $parts[2] ?? '';
		if( strlen( $surname ) === 0 ) {
			return '';
		}
		$title = $surname;
		if( strlen( $prefix ) > 0 ) {
			$title .= ', '.$prefix.' '.$forename;
		} elseif( strlen( $forename ) > 0 ) {
			$title .= ', '.$forename;
		}
		return $title;
	}

	/**
	 * Explode the stored title (prefix|forename|surname|suffix) into separate
	 * mInfo fields for the edit form, build mInfo['name'] — the reassembled display
	 * form display_contact.tpl already reads directly — and then normalise
	 * mInfo['title'] itself to that same display form. Several templates
	 * (contact_header.tpl, edit.tpl, edit_contact.tpl, page_display.tpl) read
	 * mInfo.title straight from Smarty with no way to route through getTitle()'s
	 * override, so title has to already be display-ready after load(). Safe to
	 * overwrite in place — verify() rebuilds title fresh from the submitted form's
	 * prefix/forename/surname/suffix fields, it never reads mInfo['title'] back.
	 */
	public function load( $pContentId = NULL, $pPluginParams = NULL ) {
		parent::load( $pContentId, $pPluginParams );
		if( !empty( $this->mInfo ) ) {
			$parts = explode( '|', $this->mInfo['title'] ?? '' );
			$this->mInfo['prefix']   = $parts[0] ?? '';
			$this->mInfo['forename'] = $parts[1] ?? '';
			$this->mInfo['surname']  = $parts[2] ?? '';
			$this->mInfo['suffix']   = $parts[3] ?? '';
			$this->mInfo['name']     = self::formatDisplayName( $this->mInfo['title'] ?? '' );
			$this->mInfo['title']    = $this->mInfo['name'];
		}
	}

	/**
	 * Build title (prefix|forename|surname|suffix, pipe-encoded) from the edit
	 * form's separate name fields before delegating to Contact::verify(), the
	 * same way ContactBusiness passes its organisation string through as title.
	 */
	public function verify( &$pParamHash ): bool {
		if( isset( $pParamHash['surname'] ) ) {
			$pParamHash['title'] = ( $pParamHash['prefix'] ?? '' ).'|'.( $pParamHash['forename'] ?? '' ).'|'.$pParamHash['surname'].'|'.( $pParamHash['suffix'] ?? '' );
		}
		return parent::verify( $pParamHash );
	}

}
