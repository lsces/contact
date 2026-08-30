<?php
/**
 * Person contact — extends Contact with content_type_guid='contactperson'.
 *
 * Name storage mirrors ContactBusiness: liberty_content.title is the source of
 * truth (pipe-encoded prefix|forename|surname|suffix), not a separate xref row.
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

	/** Reassemble a pipe-encoded title into "Prefix Forename Surname Suffix". */
	public static function formatDisplayName( string $pRawTitle ): string {
		$parts = explode( '|', $pRawTitle );
		$name = trim( $parts[0] ?? '' );
		$name = trim( $name.' '.( $parts[1] ?? '' ) );
		$name = trim( $name.' '.( $parts[2] ?? '' ) );
		$name = trim( $name.' '.( $parts[3] ?? '' ) );
		return $name;
	}

	/** Reassemble a pipe-encoded title into "Surname, Prefix Forename" for sorting/listing. */
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
	 * Explode the stored title into separate mInfo fields for the edit form, and
	 * normalise mInfo['title'] itself to the display form — several templates read
	 * mInfo.title directly, with no route through getTitle()'s override.
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
