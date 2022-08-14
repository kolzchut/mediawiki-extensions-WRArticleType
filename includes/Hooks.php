<?php

namespace MediaWiki\Extension\ArticleType;

use OutputPage;
use Parser;
use ParserOutput;

class Hooks implements
	\MediaWiki\Hook\MakeGlobalVariablesScriptHook,
	\MediaWiki\Hook\ParserFirstCallInitHook,
	\MediaWiki\Hook\OutputPageParserOutputHook,
	\MediaWiki\Hook\OutputPageBodyAttributesHook
{

	/**
	 * This hook is called when the parser initialises for the first time.
	 *
	 * @param Parser $parser Parser object being initialised
	 *
	 * @return void
	 * @throws \MWException
	 */
	public function onParserFirstCallInit( $parser ) {
		$parser->setFunctionHook( 'articletype', [ __CLASS__, 'setArticleType' ] );
	}

	/**
	 * Parser hook handler for {{#articletype}}
	 *
	 * @param Parser &$parser : Parser instance available to render
	 *  wikitext into html, or parser methods.
	 * @param string $articleType : the article type to set
	 *
	 * @return string HTML to insert in the page.
	 */
	public static function setArticleType( Parser &$parser, string $articleType ): string {
		$articleType = trim( htmlspecialchars( $articleType ) );
		$articleType = ArticleType::isValidArticleType( $articleType ) ? $articleType : 'unknown';

		$parser->getOutput()->setExtensionData( ArticleType::DATA_VAR, $articleType );
		$parser->getOutput()->setProperty( ArticleType::DATA_VAR, $articleType );

		return '';
	}

	/**
	 * Save data from ParserOutput to OutputPage
	 *
	 * @inheritDoc
	 */
	public function onOutputPageParserOutput( $out, $parserOutput ): void {
		global $wgArticleTypeConfig;

		$type = self::getArticleTypeFromOutput( $out, $parserOutput );
		if ( !in_array( $type, $wgArticleTypeConfig['noTitleText'] ) ) {
			self::setPageTitle( $out, $parserOutput );
		}

		$out->wgArticleType = $type;
	}

	/**
	 * Get the article type from OutputPage or ParserOutput
	 *
	 * @param OutputPage $out
	 * @param ParserOutput|null $parserOutput
	 *
	 * @return mixed|string
	 */
	private static function getArticleTypeFromOutput( OutputPage $out, ParserOutput $parserOutput = null ) {
		$type = null;
		if ( $parserOutput ) {
			$type = $parserOutput->getExtensionData( ArticleType::DATA_VAR );
		}
		if ( $type == null && isset( $out->wgArticleType ) ) {
			$type = $out->wgArticleType;
		}

		return $type ?: 'unknown';
	}

	/**
	 * Add the article type as an HTML body class
	 *
	 * @inheritDoc
	 */
	public function onOutputPageBodyAttributes( $out, $sk, &$bodyAttrs ) {
		$type = self::getArticleTypeFromOutput( $out );
		$bodyAttrs['class'] .= " article-type-$type";
	}

	/**
	 * Save the content area as a JS variable
	 *
	 * @inheritDoc
	 */
	public function onMakeGlobalVariablesScript( &$vars, $out ) {
		$vars['wgArticleType'] = self::getArticleTypeFromOutput( $out );
	}

	/**
	 * @param OutputPage &$out
	 * @param ParserOutput $parserOutput
	 *
	 * @internal param string $articleType
	 */
	protected static function setPageTitle( OutputPage &$out, ParserOutput $parserOutput ) {
		$type = self::getArticleTypeFromOutput( $out, $parserOutput );
		$articleTypeReadable = ArticleType::getReadableArticleTypeFromCode( $type );
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
