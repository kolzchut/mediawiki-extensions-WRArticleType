<?php
/**
 * WRArticleType extension - sets a page type
 * @author Dror S. [FFS]
 * @copyright © 2014-2015 Dror S. & Kol-Zchut Ltd.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/* Setup */
$wgExtensionCredits['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => 'Kol-Zchut Article Type',
	'author'         => 'Dror S. [FFS] ([http://www.kolzchut.org.il Kol-Zchut])',
	'version'        => '1.2.0',
	'url'            => 'https://github.com/kolzchut/mediawiki-extensions-WRArticleType',
	'license-name'   => 'GPL-2.0+',
	'descriptionmsg' => 'wrarticletype-desc',
);

// Internationalization
$wgExtensionMessagesFiles['WRArticleType'] = __DIR__ . '/WRArticleType.i18n.php';
$wgExtensionMessagesFiles['WRArticleTypeMagic'] = __DIR__ . '/WRArticleType.i18n.magic.php';

// Auto load of classes
$wgAutoloadClasses['WRArticleType'] = __DIR__ . '/WRArticleType_body.php';

// Register hooks
$wgHooks['ParserFirstCallInit'][] = 'WRArticleType::onParserFirstCallInit';
$wgHooks['OutputPageBodyAttributes'][] = 'WRArticleType::onOutputPageBodyAttributes';
$wgHooks['OutputPageParserOutput'][] = 'WRArticleType::onOutputPageParserOutput';
$wgHooks['MakeGlobalVariablesScript'][] = 'WRArticleType::onMakeGlobalVariablesScript';

// Default settings
$wgArticleTypeConfig = array();
$wgArticleTypeConfig['types'] = array(
	'portal', 'right', 'service', 'term', 'proceeding', 'health',
	'organization', 'government', 'event', 'ruling', 'law',
	'letter', 'faq', 'newsletter', 'user', 'mainpage'
);

// List of types that we don't add as text to the page title
$wgArticleTypeConfig['noTitleText'] = array(
	'unknown',
	'portal',
	'user'
);
