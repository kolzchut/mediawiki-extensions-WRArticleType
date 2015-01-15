<?php
/**
 * WRArticleType extension - can be used to add a class to the page
 * @author Dror Snir
 * @copyright (C) 2014 Dror Snir (Kol-Zchut)
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/* Setup */
$wgExtensionCredits['parserhook'][] = array(
    'path'           => __FILE__,
    'name'           => 'Kol-Zchut Article Type',
    'author'         => 'Dror Snir ([http://www.kolzchut.org.il Kol-Zchut])',
    'version'        => '1.1.0',
    'url'            => 'http://www.kolzchut.org.il/he/Project:Extensions/WRArticleType',
    'descriptionmsg' => 'wrarticletype-desc',
);

// Internationalization
$wgExtensionMessagesFiles['WRArticleType'] = __DIR__ . '/WRArticleType.i18n.php';
$wgExtensionMessagesFiles['WRArticleTypeMagic'] = __DIR__ . '/WRArticleType.i18n.magic.php';

// Auto load of classes
$wgAutoloadClasses['WRArticleType'] = __DIR__ . '/WRArticleType_body.php';

// Register hooks
$wgWRArticleType = new WRArticleType;
$wgHooks['ParserFirstCallInit'][] = &$wgWRArticleType;
$wgHooks['OutputPageBodyAttributes'][] = &$wgWRArticleType;
$wgHooks['OutputPageParserOutput'][] = &$wgWRArticleType;