<?php
/**
 * Streams a Contact's own downloaded extra image (currently just Wikidata's P18, saved locally by
 * ContactWikiIndividual::reloadFromWikidata()) - same small PHP-mediated server as fisheye's own
 * view_extra_image.php, for the same reason (no nginx location for this storage tree).
 *
 * Takes xref_id only, never a raw path - the file actually served is always exactly what's already
 * stored server-side against that xref row, resolved via the owning content object's own
 * getExtraImagePath() (ContactWikiIndividual's own STORAGE_PKG_PATH-relative implementation),
 * rather than building the path here directly - no path-traversal surface.
 *
 * @package contact
 * @subpackage functions
 */

use Bitweaver\KernelTools;
use Bitweaver\HttpStatusCodes;
use Bitweaver\Liberty\LibertyContent;

require_once '../kernel/includes/setup_inc.php';
global $gBitSystem, $gBitDb;

$gBitSystem->verifyPackage( 'contact' );

$xrefId = (int)( $_REQUEST['xref_id'] ?? 0 );
$row = $xrefId ? $gBitDb->getRow(
	"SELECT content_id, xkey_ext FROM `".BIT_DB_PREFIX."liberty_xref` WHERE xref_id = ? AND item = 'image'",
	[ $xrefId ]
) : null;
if( !$row ) {
	$gBitSystem->fatalError( KernelTools::tra( 'No such image' ), null, null, HttpStatusCodes::HTTP_NOT_FOUND );
}

$gContent = LibertyContent::getLibertyObject( (int)$row['content_id'] );
if( !$gContent || !method_exists( $gContent, 'getExtraImagePath' ) ) {
	$gBitSystem->fatalError( KernelTools::tra( 'No such image' ), null, null, HttpStatusCodes::HTTP_NOT_FOUND );
}
// same viewer permission as the contact itself - the saved image is no more sensitive than the
// contact's own biography/details, but shouldn't bypass a private contact's access control.
$gContent->verifyViewPermission();

$relativePath = $row['xkey_ext'];
if( empty( $relativePath ) ) {
	$gBitSystem->fatalError( KernelTools::tra( 'No such image' ), null, null, HttpStatusCodes::HTTP_NOT_FOUND );
}

$path = $gContent->getExtraImagePath( $relativePath );
if( empty( $path ) || !is_file( $path ) ) {
	$gBitSystem->fatalError( KernelTools::tra( 'Image file not found' ), null, null, HttpStatusCodes::HTTP_NOT_FOUND );
}

header( 'Content-Type: '.$gBitSystem->verifyMimeType( $path ) );
header( 'Content-Length: '.filesize( $path ) );
header( 'Cache-Control: private, max-age=86400' );
while( ob_get_level() > 0 ) {
	ob_end_clean();
}
readfile( $path );
