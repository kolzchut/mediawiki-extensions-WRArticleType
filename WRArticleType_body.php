<?php

class WRArticleType {

	/**
	 * Internal store for Article Type
	 *
	 * @private
	 */
	protected $mArticleType;
	/**
	 * const
	 * @private
	 */
	private static $DATA_VAR = 'ArticleType';

	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setFunctionHook( 'articletype', 'WRArticleType::setArticleType' );
		return true;
	}

	/**
	 * Parser hook handler for {{#articletype}}
	 *
	 * @param Parser $parser : Parser instance available to render
	 *  wikitext into html, or parser methods.
	 * @param string $articleType : the article type to set
	 *
	 * @return string: HTML to insert in the page.
	 */
	public static function setArticleType( Parser &$parser, $articleType ) {
		$articleType = trim( htmlspecialchars( $articleType ) );
		$articleType = self::isValidArticleType( $articleType ) ? $articleType : 'unknown';

		$parser->getOutput()->setExtensionData( self::$DATA_VAR, $articleType );
		$parser->getOutput()->setProperty( self::$DATA_VAR, $articleType );

		return;
	}

	public static function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parserOutput ) {
		global $wgArticleTypeConfig;

		$type = self::getArticleType( $out, $parserOutput );
		if ( !in_array( $type, $wgArticleTypeConfig['noTitleText'] ) ) {
			self::setPageTitle( $out, $parserOutput );
		}

		$out->wgArticleType = $type;

		return true;
	}

	public static function getArticleType( OutputPage $out, ParserOutput $parserOutput = null ) {
		$type = null;
		if ( $parserOutput ) {
			$type = $parserOutput->getExtensionData( self::$DATA_VAR );
		}
		if ( $type == null && isset( $out->wgArticleType ) ) {
			$type = $out->wgArticleType;
		}

		return $type ?: 'unknown';
	}

	public static function isValidArticleType( $type ) {
		global $wgArticleTypeConfig;
		return in_array( $type, $wgArticleTypeConfig['types'] );
	}

	public static function getReadableArticleTypeFromCode( $code, $count = 1 ) {
		global $wgArticleTypeConfig;
		if ( self::isValidArticleType( $code ) ) {
			$msgKey = "articletype-type-{$code}";
			$typeMsg = wfMessage( $msgKey );
			if ( $typeMsg->exists() && !$typeMsg->isBlank() ) {
				return $typeMsg->numParams( $count )->text();
			}
		}

		return 'unknown';
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @param $bodyAttribs
	 * @return bool
	 * @throws MWException
	 */
	public static function onOutputPageBodyAttributes( OutputPage $out, $sk, &$bodyAttribs ) {
		$type = self::getArticleType( $out );
		$bodyAttribs['class'] .= " article-type-{$type}";

		return true;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 * Make extension configuration variables available in javascript
	 *
	 * @param array $vars
	 * @param OutputPage $out
	 *
	 * @return true
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$vars['wgArticleType'] = self::getArticleType( $out );

		return true;
	}

	/**
	 * @param OutputPage $out
	 * @param ParserOutput $parserOutput
	 *
	 * @internal param string $articleType
	 */
	protected static function setPageTitle( OutputPage &$out, ParserOutput $parserOutput ) {
		$type = self::getArticleType( $out, $parserOutput );
		$articleTypeReadable = self::getReadableArticleTypeFromCode( $type );
		if ( $articleTypeReadable !== null && $articleTypeReadable !== 'unknown' ) {
			$currentPageTitle = $parserOutput->getDisplayTitle();
			$additionaltext = '<span class="article-type"> (' . $articleTypeReadable . ')</span>';
			$newPageTitle = $currentPageTitle . $additionaltext;

			/* Set display both on ParserOutput and OutputPage, to be on the safe side */
			$parserOutput->setDisplayTitle( $newPageTitle );
			$out->setPageTitle( $newPageTitle );
		}
	}

}
