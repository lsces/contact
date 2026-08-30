<?php
/**
 * Person contact — extends Contact with content_type_guid='contactperson'.
 *
 * liberty_content.title holds the plain, sortable "Surname, Prefix Forename
 * Suffix" display form directly - needed so a real SQL ORDER BY on lc.title sorts
 * correctly (see list_contacts.php's combined-query pagination fix). The
 * individual prefix/forename/surname/suffix parts (needed to repopulate the
 * edit form) live separately as a JSON array on a dedicated 'NAME' xref item
 * (see Contact::store() and the schema_inc.php registration) - not in title
 * itself any more, and not pipe-encoded (that was the pre-2026-08-30 scheme;
 * pipe-encoding lc.title directly is what broke sort order once pagination
 * needed a real ORDER BY instead of an in-memory re-sort).
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\Liberty\LibertyContent;

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

	/** Build "Prefix Forename Surname Suffix" from a [prefix,forename,surname,suffix] parts array. */
	public static function formatDisplayName( array $pParts ): string {
		$name = trim( $pParts[0] ?? '' );
		$name = trim( $name.' '.( $pParts[1] ?? '' ) );
		$name = trim( $name.' '.( $pParts[2] ?? '' ) );
		$name = trim( $name.' '.( $pParts[3] ?? '' ) );
		return $name;
	}

	/**
	 * Build the plain "Surname, Prefix Forename Suffix" sort/display form (what
	 * lc.title itself stores) from all four individual name parts — shared by
	 * verify() and any caller that needs to match/build a title the same way
	 * outside a full save (e.g. ImportContactCSV.php's by-title lookup). Suffix
	 * stays at the end regardless of whether prefix/forename are present, same
	 * relative position as in formatDisplayName()'s full form.
	 */
	public static function buildSortTitle( string $pPrefix, string $pForename, string $pSurname, string $pSuffix ): string {
		$prefix   = trim( $pPrefix );
		$forename = trim( $pForename );
		$surname  = trim( $pSurname );
		$suffix   = trim( $pSuffix );
		$title = $surname;
		if( $prefix !== '' ) {
			$title .= ', '.$prefix.( $forename !== '' ? ' '.$forename : '' );
		} elseif( $forename !== '' ) {
			$title .= ', '.$forename;
		}
		if( $suffix !== '' ) {
			$title .= ' '.$suffix;
		}
		return $title;
	}

	/**
	 * Load the NAME xref item's JSON parts into separate mInfo fields for the
	 * edit form, and set mInfo['name']/mInfo['title'] to the full display form
	 * ("Prefix Forename Surname Suffix") — several templates read mInfo.title
	 * directly, with no route through getTitle()'s override. The DB's own
	 * lc.title (surname-led, used for listing/sorting) is intentionally
	 * overwritten here for display purposes, same as before this class stored
	 * name parts as JSON rather than pipe-encoding title itself.
	 */
	public function load( $pContentId = NULL, $pPluginParams = NULL ) {
		parent::load( $pContentId, $pPluginParams );
		if( !empty( $this->mInfo ) ) {
			$nameXref = LibertyContent::lookupXrefByItem( $this->mContentId, 'NAME', $this->mContentTypeGuid );
			$parts = ( $nameXref && !empty( $nameXref['data'] ) ) ? ( json_decode( $nameXref['data'], true ) ?: [] ) : [];
			$this->mInfo['prefix']   = $parts[0] ?? '';
			$this->mInfo['forename'] = $parts[1] ?? '';
			$this->mInfo['surname']  = $parts[2] ?? '';
			$this->mInfo['suffix']   = $parts[3] ?? '';
			$this->mInfo['name']     = self::formatDisplayName( $parts );
			$this->mInfo['title']    = $this->mInfo['name'];
		}
	}

	/**
	 * Build title as the plain "Surname, Prefix Forename" sort/display form from
	 * the edit form's separate name fields, before delegating to Contact::verify()
	 * — the same way ContactBusiness passes its organisation string through as
	 * title. The individual parts themselves are persisted separately, as JSON on
	 * the NAME xref item — see Contact::store().
	 */
	public function verify( &$pParamHash ): bool {
		if( isset( $pParamHash['surname'] ) ) {
			$pParamHash['title'] = self::buildSortTitle(
				$pParamHash['prefix'] ?? '', $pParamHash['forename'] ?? '', $pParamHash['surname'], $pParamHash['suffix'] ?? ''
			);
		}
		return parent::verify( $pParamHash );
	}

}
